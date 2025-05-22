<?php

namespace Tests\Feature\Controllers;

use App\Models\Job;


use App\Models\Proposal;


use App\Models\User;


use Illuminate\Foundation\Testing\RefreshDatabase;


use Tests\TestCase;
class ProposalControllerTest extends TestCase
{
    use RefreshDatabase;


    #[Test]
    public function unauthenticated_users_cannot_access_proposal_routes()
    {
        $job = Job::factory()->create();
        $proposal = Proposal::factory()->create();
        
        $this->get(route('freelancer.proposals.index'))->assertRedirect(route('login'));
        $this->post(route('freelancer.proposals.store', $job))->assertRedirect(route('login'));
        $this->get(route('client.jobs.proposals', $job))->assertRedirect(route('login'));
    }
    #[Test]
    public function freelancer_can_view_their_proposals()
    {
        $freelancer = User::factory()->create(['role' => 'freelancer']);
        $job = Job::factory()->create();
        $proposal = Proposal::factory()->create([
            'user_id' => $freelancer->id,
            'job_id' => $job->id
        ]);
        
        $this->actingAs($freelancer)
            ->get(route('freelancer.proposals.index'))
            ->assertStatus(200)
            ->assertSee($job->title);
    }
    #[Test]
    public function freelancer_can_submit_a_proposal()
    {
        $freelancer = User::factory()->create(['role' => 'freelancer']);
        $job = Job::factory()->create(['status' => 'open']);
        
        $proposalData = [
            'bid_amount' => 1000,
            'proposal_text' => 'This is my proposal for the job. I have extensive experience in this area and can deliver high-quality work.',
        ];
        
        $this->actingAs($freelancer)
            ->post(route('freelancer.proposals.store', $job), $proposalData)
            ->assertRedirect();
            
        $this->assertDatabaseHas('proposals', [
            'user_id' => $freelancer->id,
            'job_id' => $job->id,
            'bid_amount' => 1000,
            'status' => 'pending'
        ]);
    }
    #[Test]
    public function client_can_view_proposals_for_their_job()
    {
        $client = User::factory()->create(['role' => 'client']);
        $job = Job::factory()->create(['user_id' => $client->id]);
        $freelancer = User::factory()->create(['role' => 'freelancer']);
        $proposal = Proposal::factory()->create([
            'user_id' => $freelancer->id,
            'job_id' => $job->id
        ]);
        
        $this->actingAs($client)
            ->get(route('client.jobs.proposals', $job))
            ->assertStatus(200)
            ->assertSee($proposal->proposal_text);
    }
    #[Test]
    public function client_cannot_view_proposals_for_other_clients_jobs()
    {
        $client1 = User::factory()->create(['role' => 'client']);
        $client2 = User::factory()->create(['role' => 'client']);
        $job = Job::factory()->create(['user_id' => $client2->id]);
        
        $this->actingAs($client1)
            ->get(route('client.jobs.proposals', $job))
            ->assertStatus(403);
    }
    #[Test]
    public function client_can_accept_a_proposal()
    {
        $client = User::factory()->create(['role' => 'client']);
        $job = Job::factory()->create(['user_id' => $client->id]);
        $freelancer = User::factory()->create(['role' => 'freelancer']);
        $proposal = Proposal::factory()->create([
            'user_id' => $freelancer->id,
            'job_id' => $job->id,
            'status' => 'pending'
        ]);
        
        $this->actingAs($client)
            ->patch(route('client.proposals.update-status', $proposal), ['status' => 'accepted'])
            ->assertRedirect();
            
        $this->assertDatabaseHas('proposals', [
            'id' => $proposal->id,
            'status' => 'accepted'
        ]);
        
        // Other proposals should be rejected
        $otherFreelancer = User::factory()->create(['role' => 'freelancer']);
        $otherProposal = Proposal::factory()->create([
            'user_id' => $otherFreelancer->id,
            'job_id' => $job->id,
            'status' => 'pending'
        ]);
        
        $this->assertDatabaseHas('proposals', [
            'id' => $otherProposal->id,
            'status' => 'rejected'
        ]);
    }
}
