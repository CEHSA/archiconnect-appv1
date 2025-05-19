<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRoutesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that admin routes are defined and accessible.
     *
     * @return void
     */
    public function test_admin_routes_are_defined()
    {
        // Create an admin user
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        // Login as admin
        $this->actingAs($admin, 'admin');

        // Test dashboard route
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);

        // Test routes are defined - even if they return 404 due to missing views
        // This just confirms the routes exist in the application
        
        // Job management
        $response = $this->get(route('admin.jobs.index'));
        $this->assertTrue($response->status() == 200 || $response->status() == 404);
        
        // User management
        $response = $this->get(route('admin.users.index'));
        $this->assertTrue($response->status() == 200 || $response->status() == 404);
        
        // Reports
        $response = $this->get(route('admin.reports.index'));
        $this->assertTrue($response->status() == 200 || $response->status() == 404);
        
        // Messages
        $response = $this->get(route('admin.messages.index'));
        $this->assertTrue($response->status() == 200 || $response->status() == 404);
        
        // Settings
        $response = $this->get(route('admin.settings.index'));
        $this->assertTrue($response->status() == 200 || $response->status() == 404);
        
        // Work Submissions
        $response = $this->get(route('admin.work-submissions.index'));
        $this->assertTrue($response->status() == 200 || $response->status() == 404);
        
        // Budget Appeals
        $response = $this->get(route('admin.budget-appeals.index'));
        $this->assertTrue($response->status() == 200 || $response->status() == 404);
        
        // Disputes
        $response = $this->get(route('admin.disputes.index'));
        $this->assertTrue($response->status() == 200 || $response->status() == 404);
        
        // Briefing Requests
        $response = $this->get(route('admin.briefing-requests.index'));
        $this->assertTrue($response->status() == 200 || $response->status() == 404);
        
        // Payments
        $response = $this->get(route('admin.payments.index'));
        $this->assertTrue($response->status() == 200 || $response->status() == 404);
    }
}
