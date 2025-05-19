<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function unauthenticated_users_cannot_access_admin_user_index()
    {
        $response = $this->get(route('admin.users.index'));
        $response->assertRedirect('/login');
    }

    #[Test]
    public function admin_users_can_delete_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $userToDelete = User::factory()->create(['role' => 'client']);

        $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $userToDelete));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success', 'User deleted successfully.');
        $this->assertDatabaseMissing('users', ['id' => $userToDelete->id]);
    }

    #[Test]
    public function non_admin_users_cannot_access_admin_user_index()
    {
        $user = User::factory()->create(['role' => 'client']);
        $response = $this->actingAs($user)->get(route('admin.users.index'));
        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error', 'You do not have access to this page.');
    }

    #[Test]
    public function admin_users_can_access_admin_user_index()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get(route('admin.users.index'));
        $response->assertOk();
        $response->assertViewIs('admin.users.index');
    }

    #[Test]
    public function admin_users_can_search_users()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user1 = User::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com', 'role' => 'client']);
        $user2 = User::factory()->create(['name' => 'Jane Smith', 'email' => 'jane@example.com', 'role' => 'freelancer']);
        $user3 = User::factory()->create(['name' => 'Peter Jones', 'email' => 'peter@example.com', 'role' => 'client']);

        // Search by name
        $response = $this->actingAs($admin)->get(route('admin.users.index', ['search' => 'John']));
        $response->assertOk();
        $response->assertSee($user1->name);
        $response->assertDontSee($user2->name);
        $response->assertDontSee($user3->name);

        // Search by email
        $response = $this->actingAs($admin)->get(route('admin.users.index', ['search' => 'jane@']));
        $response->assertOk();
        $response->assertSee($user2->name);
        $response->assertDontSee($user1->name);
        $response->assertDontSee($user3->name);
    }

    #[Test]
    public function admin_users_can_filter_users_by_role()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $clientUser = User::factory()->create(['role' => 'client']);
        $freelancerUser = User::factory()->create(['role' => 'freelancer']);
        $anotherClientUser = User::factory()->create(['role' => 'client']);

        // Filter by client role
        $response = $this->actingAs($admin)->get(route('admin.users.index', ['role' => 'client']));
        $response->assertOk();
        $response->assertSee($clientUser->name);
        $response->assertSee($anotherClientUser->name);
        $response->assertDontSee($freelancerUser->name);
        $response->assertDontSee($admin->name);

        // Filter by freelancer role
        $response = $this->actingAs($admin)->get(route('admin.users.index', ['role' => 'freelancer']));
        $response->assertOk();
        $response->assertSee($freelancerUser->name);
        $response->assertDontSee($clientUser->name);
        $response->assertDontSee($anotherClientUser->name);
        $response->assertDontSee($admin->name);
    }

    #[Test]
    public function admin_users_can_search_and_filter_users_simultaneously()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user1 = User::factory()->create(['name' => 'Client One', 'email' => 'client1@example.com', 'role' => 'client']);
        $user2 = User::factory()->create(['name' => 'Freelancer One', 'email' => 'freelancer1@example.com', 'role' => 'freelancer']);
        $user3 = User::factory()->create(['name' => 'Client Two', 'email' => 'client2@example.com', 'role' => 'client']);

        // Search for "One" and filter by "client"
        $response = $this->actingAs($admin)->get(route('admin.users.index', ['search' => 'One', 'role' => 'client']));
        $response->assertOk();
        $response->assertSee($user1->name);
        $response->assertDontSee($user2->name);
        $response->assertDontSee($user3->name);

        // Search for "Two" and filter by "client"
        $response = $this->actingAs($admin)->get(route('admin.users.index', ['search' => 'Two', 'role' => 'client']));
        $response->assertOk();
        $response->assertSee($user3->name);
        $response->assertDontSee($user1->name);
        $response->assertDontSee($user2->name);
    }
}
