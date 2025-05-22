<?php

namespace Tests\Unit\Policies;

use App\Models\Job;

use App\Models\User;

use App\Policies\JobPolicy;

use Illuminate\Foundation\Testing\RefreshDatabase;


use Tests\TestCase;
class JobPolicyTest extends TestCase
{
    use RefreshDatabase;


    #[Test]
    public function admin_can_view_any_job()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $policy = new JobPolicy();
        
        $this->assertTrue($policy->viewAny($admin));
    }
    
    #[Test]
    public function client_can_view_any_job()
    {
        $client = User::factory()->create(['role' => 'client']);
        $policy = new JobPolicy();
        
        $this->assertTrue($policy->viewAny($client));
    }
    
    #[Test]
    public function freelancer_can_view_any_job()
    {
        $freelancer = User::factory()->create(['role' => 'freelancer']);
        $policy = new JobPolicy();
        
        $this->assertTrue($policy->viewAny($freelancer));
    }
    
    #[Test]
    public function admin_can_view_any_specific_job()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $job = Job::factory()->create();
        $policy = new JobPolicy();
        
        $this->assertTrue($policy->view($admin, $job));
    }
    
    #[Test]
    public function client_can_view_their_own_job()
    {
        $client = User::factory()->create(['role' => 'client']);
        $job = Job::factory()->create(['user_id' => $client->id]);
        $policy = new JobPolicy();
        
        $this->assertTrue($policy->view($client, $job));
    }
    
    #[Test]
    public function client_cannot_view_other_clients_job()
    {
        $client1 = User::factory()->create(['role' => 'client']);
        $client2 = User::factory()->create(['role' => 'client']);
        $job = Job::factory()->create(['user_id' => $client2->id]);
        $policy = new JobPolicy();
        
        $this->assertFalse($policy->view($client1, $job));
    }
    
    #[Test]
    public function freelancer_can_view_open_jobs()
    {
        $freelancer = User::factory()->create(['role' => 'freelancer']);
        $job = Job::factory()->create(['status' => 'open']);
        $policy = new JobPolicy();
        
        $this->assertTrue($policy->view($freelancer, $job));
    }
    
    #[Test]
    public function client_can_create_job()
    {
        $client = User::factory()->create(['role' => 'client']);
        $policy = new JobPolicy();
        
        $this->assertTrue($policy->create($client));
    }
    
    #[Test]
    public function freelancer_cannot_create_job()
    {
        $freelancer = User::factory()->create(['role' => 'freelancer']);
        $policy = new JobPolicy();
        
        $this->assertFalse($policy->create($freelancer));
    }
    
    #[Test]
    public function client_can_update_their_own_job()
    {
        $client = User::factory()->create(['role' => 'client']);
        $job = Job::factory()->create(['user_id' => $client->id]);
        $policy = new JobPolicy();
        
        $this->assertTrue($policy->update($client, $job));
    }
    
    #[Test]
    public function client_cannot_update_other_clients_job()
    {
        $client1 = User::factory()->create(['role' => 'client']);
        $client2 = User::factory()->create(['role' => 'client']);
        $job = Job::factory()->create(['user_id' => $client2->id]);
        $policy = new JobPolicy();
        
        $this->assertFalse($policy->update($client1, $job));
    }
    
    #[Test]
    public function admin_can_update_any_job()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $job = Job::factory()->create();
        $policy = new JobPolicy();
        
        $this->assertTrue($policy->update($admin, $job));
    }
    
    #[Test]
    public function client_can_delete_their_own_job()
    {
        $client = User::factory()->create(['role' => 'client']);
        $job = Job::factory()->create(['user_id' => $client->id]);
        $policy = new JobPolicy();
        
        $this->assertTrue($policy->delete($client, $job));
    }
    
    #[Test]
    public function client_cannot_delete_other_clients_job()
    {
        $client1 = User::factory()->create(['role' => 'client']);
        $client2 = User::factory()->create(['role' => 'client']);
        $job = Job::factory()->create(['user_id' => $client2->id]);
        $policy = new JobPolicy();
        
        $this->assertFalse($policy->delete($client1, $job));
    }
    
    #[Test]
    public function admin_can_delete_any_job()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $job = Job::factory()->create();
        $policy = new JobPolicy();
        
        $this->assertTrue($policy->delete($admin, $job));
    }
}
