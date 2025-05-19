<?php

namespace Tests\Feature\Controllers;

use App\Models\User;

use Illuminate\Foundation\Testing\RefreshDatabase;


use Tests\TestCase;
class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function unauthenticated_users_cannot_access_dashboard()
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }
    #[Test]
    public function admin_is_redirected_to_admin_dashboard()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertRedirect(route('admin.dashboard'));
    }
    #[Test]
    public function freelancer_is_redirected_to_freelancer_dashboard()
    {
        $freelancer = User::factory()->create(['role' => 'freelancer']);

        $this->actingAs($freelancer)
            ->get(route('dashboard'))
            ->assertRedirect(route('freelancer.dashboard'));
    }
    #[Test]
    public function client_is_redirected_to_client_dashboard()
    {
        $client = User::factory()->create(['role' => 'client']);

        $this->actingAs($client)
            ->get(route('dashboard'))
            ->assertRedirect(route('client.dashboard'));
    }
}
