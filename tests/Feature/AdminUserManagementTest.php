<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;
    protected User $otherStaff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Admin Boss',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        $this->staff = User::factory()->create([
            'name' => 'Staff Alpha',
            'email' => 'alpha@example.com',
            'role' => 'user',
        ]);

        $this->otherStaff = User::factory()->create([
            'name' => 'Staff Beta',
            'email' => 'beta@example.com',
            'role' => 'user',
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('admin.users.index'));

        $response->assertRedirect('/login');
    }

    public function test_regular_user_cannot_access_users_management(): void
    {
        $response = $this->actingAs($this->staff)->get(route('admin.users.index'));

        $response->assertForbidden();
    }

    public function test_admin_can_view_users_management_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.index'));

        $response->assertOk();
        $response->assertSee('จัดการผู้ใช้งาน');
        $response->assertSee('Staff Alpha');
        $response->assertSee('Staff Beta');
        $response->assertSee('alpha@example.com');
        $response->assertSee('beta@example.com');
    }

    public function test_admin_can_search_and_filter_users(): void
    {
        // Search by name
        $response = $this->actingAs($this->admin)->get(route('admin.users.index', ['search' => 'Alpha']));
        $response->assertOk();
        $response->assertSee('Staff Alpha');
        $response->assertDontSee('Staff Beta');

        // Filter by role: admin
        $response = $this->actingAs($this->admin)->get(route('admin.users.index', ['role' => 'admin']));
        $response->assertOk();
        $response->assertDontSee('Staff Alpha');
        $response->assertDontSee('Staff Beta');
    }
}
