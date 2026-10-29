<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\BorrowRecord;
use App\Models\Category;
use App\Models\InventorySurvey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class PolicyAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;
    protected User $otherStaff;
    protected Category $category;
    protected Asset $asset;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['name' => 'Admin User', 'email' => 'admin@test.com']);
        $this->admin->forceFill(['role' => 'admin'])->save();

        $this->staff = User::factory()->create(['name' => 'Staff User', 'email' => 'staff@test.com']);
        $this->otherStaff = User::factory()->create(['name' => 'Other Staff', 'email' => 'other@test.com']);

        $this->category = Category::create([
            'name' => 'IT Equipment',
            'code' => 'IT',
            'description' => 'IT hardware',
        ]);

        $this->asset = Asset::create([
            'category_id' => $this->category->id,
            'asset_code' => 'IT-POLICY-001',
            'name' => 'Dell Latitude',
            'price' => 35000,
            'purchase_date' => now()->subMonths(1),
            'status' => 'available',
        ]);
    }

    public function test_asset_policy_view_abilities(): void
    {
        $this->assertTrue(Gate::forUser(null)->allows('viewAny', Asset::class));
        $this->assertTrue(Gate::forUser($this->staff)->allows('viewAny', Asset::class));
        $this->assertTrue(Gate::forUser(null)->allows('view', $this->asset));
        $this->assertTrue(Gate::forUser($this->staff)->allows('view', $this->asset));
    }

    public function test_asset_policy_crud_abilities(): void
    {
        $this->assertTrue(Gate::forUser($this->admin)->allows('create', Asset::class));
        $this->assertFalse(Gate::forUser($this->staff)->allows('create', Asset::class));

        $this->assertTrue(Gate::forUser($this->admin)->allows('update', $this->asset));
        $this->assertFalse(Gate::forUser($this->staff)->allows('update', $this->asset));

        $this->assertTrue(Gate::forUser($this->admin)->allows('delete', $this->asset));
        $this->assertFalse(Gate::forUser($this->staff)->allows('delete', $this->asset));
    }

    public function test_asset_policy_borrow_ability(): void
    {
        // Regular staff can borrow available asset
        $this->assertTrue(Gate::forUser($this->staff)->allows('borrow', $this->asset));

        // Admin cannot borrow (admins manage)
        $this->assertFalse(Gate::forUser($this->admin)->allows('borrow', $this->asset));

        // When asset is borrowed, cannot borrow
        $this->asset->update(['status' => 'borrowed']);
        $this->assertFalse(Gate::forUser($this->staff)->allows('borrow', $this->asset));
    }

    public function test_category_policy_abilities(): void
    {
        $this->assertTrue(Gate::forUser($this->admin)->allows('create', Category::class));
        $this->assertFalse(Gate::forUser($this->staff)->allows('create', Category::class));

        $this->assertTrue(Gate::forUser($this->admin)->allows('update', $this->category));
        $this->assertFalse(Gate::forUser($this->staff)->allows('update', $this->category));

        $this->assertTrue(Gate::forUser($this->admin)->allows('delete', $this->category));
        $this->assertFalse(Gate::forUser($this->staff)->allows('delete', $this->category));
    }

    public function test_borrow_record_policy_cancel_ability(): void
    {
        $record = BorrowRecord::create([
            'user_id' => $this->staff->id,
            'asset_id' => $this->asset->id,
            'borrow_date' => today(),
            'expected_return_date' => today()->addDays(3),
            'status' => 'pending',
        ]);

        // Owner can cancel pending request
        $this->assertTrue(Gate::forUser($this->staff)->allows('cancel', $record));

        // Another staff cannot cancel
        $this->assertFalse(Gate::forUser($this->otherStaff)->allows('cancel', $record));

        // When already approved, owner cannot cancel
        $record->update(['status' => 'approved']);
        $this->assertFalse(Gate::forUser($this->staff)->allows('cancel', $record));
    }

    public function test_borrow_record_policy_admin_actions(): void
    {
        $record = BorrowRecord::create([
            'user_id' => $this->staff->id,
            'asset_id' => $this->asset->id,
            'borrow_date' => today(),
            'expected_return_date' => today()->addDays(3),
            'status' => 'pending',
        ]);

        // Admin can approve & reject pending
        $this->assertTrue(Gate::forUser($this->admin)->allows('approve', $record));
        $this->assertTrue(Gate::forUser($this->admin)->allows('reject', $record));
        $this->assertFalse(Gate::forUser($this->staff)->allows('approve', $record));

        // Admin can return only approved
        $this->assertFalse(Gate::forUser($this->admin)->allows('return', $record));
        $record->update(['status' => 'approved']);
        $this->assertTrue(Gate::forUser($this->admin)->allows('return', $record));
    }

    public function test_inventory_survey_policy_abilities(): void
    {
        $this->assertTrue(Gate::forUser($this->admin)->allows('create', InventorySurvey::class));
        $this->assertFalse(Gate::forUser($this->staff)->allows('create', InventorySurvey::class));

        $survey = InventorySurvey::create([
            'year' => 2025,
            'started_by' => $this->admin->id,
        ]);

        $this->assertTrue(Gate::forUser($this->admin)->allows('update', $survey));
        $this->assertFalse(Gate::forUser($this->staff)->allows('update', $survey));
    }
}
