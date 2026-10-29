<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\BorrowRecord;
use App\Models\Category;
use App\Models\InventorySurvey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;
    protected Category $category;
    protected Asset $asset;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
        ]);
        $this->admin->forceFill(['role' => 'admin'])->save();

        $this->staff = User::factory()->create([
            'name' => 'Staff User',
            'email' => 'staff@test.com',
        ]);

        $this->category = Category::create([
            'name' => 'IT Equipment',
            'code' => 'IT',
            'description' => 'Computer and laptops',
        ]);

        $this->asset = Asset::create([
            'category_id' => $this->category->id,
            'asset_code' => 'IT-TEST-001',
            'name' => 'MacBook Pro 16',
            'price' => 75000,
            'purchase_date' => now()->subMonths(2),
            'status' => 'available',
        ]);
    }

    public function test_guest_can_view_public_assets_catalog(): void
    {
        $response = $this->get('/assets');
        $response->assertStatus(200);
        $response->assertSee('IT-TEST-001');
        $response->assertSee('MacBook Pro 16');
    }

    public function test_guest_can_view_asset_details_and_qr_code(): void
    {
        $response = $this->get('/assets/' . $this->asset->id);
        $response->assertStatus(200);
        $response->assertSee('QR Code ประจำครุภัณฑ์');
    }

    public function test_guest_cannot_access_borrow_form_without_login(): void
    {
        $response = $this->get('/assets/' . $this->asset->id . '/borrow');
        $response->assertRedirect('/login');
    }

    public function test_regular_user_cannot_access_admin_categories(): void
    {
        $response = $this->actingAs($this->staff)->get('/categories');
        $response->assertStatus(403);
    }

    public function test_profile_update_shows_a_success_notification(): void
    {
        $response = $this->actingAs($this->staff)->patch('/profile', [
            'name' => 'Updated Staff',
            'email' => $this->staff->email,
        ]);

        $response->assertSessionHas('success', 'อัปเดตข้อมูลบัญชีเรียบร้อยแล้ว');
        $this->assertEquals('Updated Staff', $this->staff->fresh()->name);
    }

    public function test_admin_can_access_categories_management(): void
    {
        $response = $this->actingAs($this->admin)->get('/categories');
        $response->assertStatus(200);
        $response->assertSee('IT Equipment');
    }

    public function test_admin_can_export_asset_register_and_print_report(): void
    {
        $this->actingAs($this->admin)->get('/admin/reports/assets.csv')
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $this->actingAs($this->admin)->get('/admin/reports/assets')
            ->assertOk()
            ->assertSee('รายงานทะเบียนครุภัณฑ์');
    }

    public function test_admin_can_assign_custodian_and_asset_update_is_audited(): void
    {
        $this->actingAs($this->admin)->put('/admin/assets/'.$this->asset->id, [
            'category_id' => $this->category->id,
            'asset_code' => $this->asset->asset_code,
            'name' => $this->asset->name,
            'price' => $this->asset->price,
            'purchase_date' => $this->asset->purchase_date->format('Y-m-d'),
            'status' => 'available',
            'department' => 'IT',
            'building' => 'A',
            'room' => '203',
            'custodian_user_id' => $this->staff->id,
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('assets', ['id' => $this->asset->id, 'custodian_user_id' => $this->staff->id, 'room' => '203']);
        $this->assertDatabaseHas('asset_audit_logs', ['asset_id' => $this->asset->id, 'user_id' => $this->admin->id, 'action' => 'updated']);
    }

    public function test_admin_can_assign_follow_up_for_missing_survey_item(): void
    {
        $survey = InventorySurvey::create(['year' => now()->year, 'started_by' => $this->admin->id]);
        $item = $survey->items()->create(['asset_id' => $this->asset->id, 'status' => 'missing']);

        $this->actingAs($this->admin)->patch('/admin/inventory-surveys/'.$survey->id.'/items/'.$item->id.'/follow-up', [
            'resolution_status' => 'in_progress',
            'assigned_to' => $this->staff->id,
            'resolution_note' => 'ตรวจสอบกับหน่วยงานแล้ว',
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('inventory_survey_items', ['id' => $item->id, 'assigned_to' => $this->staff->id, 'resolution_status' => 'in_progress']);
    }

    public function test_admin_can_view_inventory_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/dashboard');

        $response->assertOk();
        $response->assertSee('ภาพรวมระบบ');
        $response->assertSee('ครุภัณฑ์ทั้งหมด');
        $response->assertSee('IT Equipment');
    }

    public function test_regular_user_cannot_view_admin_dashboard(): void
    {
        $response = $this->actingAs($this->staff)->get('/admin/dashboard');

        $response->assertForbidden();
    }

    public function test_admin_can_run_and_complete_an_annual_inventory_survey(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/inventory-surveys', [
            'year' => now()->year,
        ]);

        $survey = InventorySurvey::where('year', now()->year)->firstOrFail();
        $response->assertRedirect('/admin/inventory-surveys/' . $survey->id);
        $this->assertDatabaseHas('inventory_survey_items', [
            'inventory_survey_id' => $survey->id,
            'asset_id' => $this->asset->id,
            'status' => 'pending',
        ]);

        $item = $survey->items()->firstOrFail();
        $this->patch('/admin/inventory-surveys/' . $survey->id . '/items/' . $item->id, [
            'status' => 'found',
            'note' => 'ตรวจพบที่ห้องปฏิบัติการ',
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('inventory_survey_items', [
            'id' => $item->id,
            'status' => 'found',
            'checked_by' => $this->admin->id,
        ]);

        $this->patch('/admin/inventory-surveys/' . $survey->id . '/complete')
            ->assertSessionHas('success');

        $this->assertNotNull($survey->fresh()->completed_at);
    }

    public function test_regular_user_cannot_access_inventory_surveys(): void
    {
        $this->actingAs($this->staff)
            ->get('/admin/inventory-surveys')
            ->assertForbidden();
    }

    public function test_dashboard_route_sends_admin_to_inventory_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get('/dashboard');

        $response->assertRedirect('/admin/dashboard');
    }

    public function test_staff_can_submit_borrow_request(): void
    {
        $response = $this->actingAs($this->staff)->post('/assets/' . $this->asset->id . '/borrow', [
            'borrow_date' => now()->toDateString(),
            'expected_return_date' => now()->addDays(5)->toDateString(),
            'note' => 'Test borrow for conference',
        ]);

        $response->assertRedirect('/my-borrows');
        $this->assertDatabaseHas('borrow_records', [
            'user_id' => $this->staff->id,
            'asset_id' => $this->asset->id,
            'status' => 'pending',
        ]);
    }

    public function test_registration_cannot_assign_the_admin_role(): void
    {
        $response = $this->post('/register', [
            'name' => 'New User',
            'email' => 'new-user@test.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'admin',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('users', [
            'email' => 'new-user@test.com',
            'role' => 'user',
        ]);
    }

    public function test_asset_cannot_have_more_than_one_open_borrow_request(): void
    {
        BorrowRecord::create([
            'user_id' => $this->staff->id,
            'asset_id' => $this->asset->id,
            'borrow_date' => now()->toDateString(),
            'expected_return_date' => now()->addDays(5)->toDateString(),
            'status' => 'pending',
        ]);

        $otherStaff = User::factory()->create();
        $response = $this->actingAs($otherStaff)->post('/assets/' . $this->asset->id . '/borrow', [
            'borrow_date' => now()->toDateString(),
            'expected_return_date' => now()->addDays(5)->toDateString(),
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('borrow_records', 1);
    }

    public function test_admin_cannot_approve_a_request_when_asset_is_not_available(): void
    {
        $borrow = BorrowRecord::create([
            'user_id' => $this->staff->id,
            'asset_id' => $this->asset->id,
            'borrow_date' => now()->toDateString(),
            'expected_return_date' => now()->addDays(5)->toDateString(),
            'status' => 'pending',
        ]);
        $this->asset->update(['status' => 'maintenance']);

        $response = $this->actingAs($this->admin)->patch('/admin/borrows/' . $borrow->id . '/approve');

        $response->assertSessionHas('error');
        $this->assertEquals('pending', $borrow->fresh()->status);
        $this->assertEquals('maintenance', $this->asset->fresh()->status);
    }

    public function test_admin_can_approve_borrow_and_asset_status_updates(): void
    {
        $borrow = BorrowRecord::create([
            'user_id' => $this->staff->id,
            'asset_id' => $this->asset->id,
            'borrow_date' => now()->toDateString(),
            'expected_return_date' => now()->addDays(5)->toDateString(),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->patch('/admin/borrows/' . $borrow->id . '/approve');
        $response->assertSessionHas('success');

        $this->assertEquals('approved', $borrow->fresh()->status);
        $this->assertEquals('borrowed', $this->asset->fresh()->status);
    }

    public function test_admin_can_return_asset_and_asset_becomes_available(): void
    {
        $borrow = BorrowRecord::create([
            'user_id' => $this->staff->id,
            'asset_id' => $this->asset->id,
            'borrow_date' => now()->subDays(3)->toDateString(),
            'expected_return_date' => now()->addDays(2)->toDateString(),
            'status' => 'approved',
        ]);
        $this->asset->update(['status' => 'borrowed']);

        $response = $this->actingAs($this->admin)->patch('/admin/borrows/' . $borrow->id . '/return');
        $response->assertSessionHas('success');

        $this->assertEquals('returned', $borrow->fresh()->status);
        $this->assertEquals('available', $this->asset->fresh()->status);
    }
}
