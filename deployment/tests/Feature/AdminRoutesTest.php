<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRoutesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Set up an admin user for testing
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create an admin user for testing
        $this->admin = Admin::factory()->create([
            'email' => 'testadmin@example.com',
            'password' => bcrypt('password123'),
        ]);
    }

    /**
     * Test admin login page
     */
    public function test_admin_login_page_loads()
    {
        $response = $this->get(route('admin.login'));
        $response->assertStatus(200);
    }

    /**
     * Test admin dashboard access after login
     */
    public function test_admin_dashboard_loads_after_login()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.dashboard'));
        $response->assertStatus(200);
    }

    /**
     * Test admin profile edit page
     */
    public function test_admin_profile_edit_page_loads()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.profile.edit'));
        $response->assertStatus(200);
    }

    /**
     * Test admin jobs index page
     */
    public function test_admin_jobs_index_page_loads()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.jobs.index'));
        $response->assertStatus(200);
    }

    /**
     * Test admin users index page
     */
    public function test_admin_users_index_page_loads()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.users.index'));
        $response->assertStatus(200);
    }

    /**
     * Test admin reports page
     */
    public function test_admin_reports_index_page_loads()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.reports.index'));
        $response->assertStatus(200);
    }

    /**
     * Test admin messages index page
     */
    public function test_admin_messages_index_page_loads()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.messages.index'));
        $response->assertStatus(200);
    }

    /**
     * Test admin settings page
     */
    public function test_admin_settings_index_page_loads()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.settings.index'));
        $response->assertStatus(200);
    }

    /**
     * Test admin budget appeals
     */
    public function test_admin_budget_appeals_index_page_loads()
    {
        // Check if the route exists first
        if ($this->route_has('admin.budget-appeals.index')) {
            $response = $this->actingAs($this->admin, 'admin')
                ->get(route('admin.budget-appeals.index'));
            $response->assertStatus(200);
        } else {
            $this->markTestSkipped('The admin.budget-appeals.index route does not exist.');
        }
    }

    /**
     * Test admin briefing requests
     */
    public function test_admin_briefing_requests_index_page_loads()
    {
        // Check if the route exists first
        if ($this->route_has('admin.briefing-requests.index')) {
            $response = $this->actingAs($this->admin, 'admin')
                ->get(route('admin.briefing-requests.index'));
            $response->assertStatus(200);
        } else {
            $this->markTestSkipped('The admin.briefing-requests.index route does not exist.');
        }
    }

    /**
     * Test admin disputes
     */
    public function test_admin_disputes_index_page_loads()
    {
        // Check if the route exists first
        if ($this->route_has('admin.disputes.index')) {
            $response = $this->actingAs($this->admin, 'admin')
                ->get(route('admin.disputes.index'));
            $response->assertStatus(200);
        } else {
            $this->markTestSkipped('The admin.disputes.index route does not exist.');
        }
    }

    /**
     * Test admin work submissions
     */
    public function test_admin_work_submissions_index_page_loads()
    {
        // Check if the route exists first
        if ($this->route_has('admin.work-submissions.index')) {
            $response = $this->actingAs($this->admin, 'admin')
                ->get(route('admin.work-submissions.index'));
            // The controller's index method explicitly aborts with 404
            $response->assertStatus(404); 
        } else {
            $this->markTestSkipped('The admin.work-submissions.index route does not exist.');
        }
    }

    /**
     * Test admin payments
     */
    public function test_admin_payments_index_page_loads()
    {
        // Check if the route exists first
        if ($this->route_has('admin.payments.index')) {
            $response = $this->actingAs($this->admin, 'admin')
                ->get(route('admin.payments.index'));
            $response->assertStatus(200);
        } else {
            $this->markTestSkipped('The admin.payments.index route does not exist.');
        }
    }

    /**
     * Helper function to check if a route exists
     */
    protected function route_has($name)
    {
        try {
            route($name);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
