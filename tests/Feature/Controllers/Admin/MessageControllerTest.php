<?php

namespace Tests\Feature\Controllers\Admin;

use App\Models\Conversation;

use App\Models\Message;

use App\Models\User;

use Illuminate\Foundation\Testing\RefreshDatabase;


use Tests\TestCase;
class MessageControllerTest extends TestCase
{
    use RefreshDatabase;


    #[Test]
    public function unauthenticated_users_cannot_access_admin_message_routes()
    {
        $this->get(route('admin.messages.index'))->assertRedirect(route('login'));

        $message = Message::factory()->create();
        $this->put(route('admin.messages.update', $message))->assertRedirect(route('login'));
    }

    #[Test]
    public function non_admin_users_cannot_access_admin_message_routes()
    {
        $client = User::factory()->create(['role' => 'client']);

        $this->actingAs($client)
            ->get(route('admin.messages.index'))
            ->assertStatus(403);

        $message = Message::factory()->create();
        $this->actingAs($client)
            ->put(route('admin.messages.update', $message))
            ->assertStatus(403);
    }

    #[Test]
    public function admin_can_view_pending_messages()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        $conversation = Conversation::factory()->create();
        $message = Message::factory()->create([
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
            'status' => 'pending'
        ]);

        $this->actingAs($admin)
            ->get(route('admin.messages.index'))
            ->assertStatus(200)
            ->assertSee($message->content);
    }

    #[Test]
    public function admin_can_approve_a_message()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        $conversation = Conversation::factory()->create();
        $message = Message::factory()->create([
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
            'status' => 'pending'
        ]);

        $this->actingAs($admin)
            ->put(route('admin.messages.update', $message), [
                'status' => 'approved',
                'admin_remarks' => 'This message is approved'
            ])
            ->assertRedirect(route('admin.messages.index'));

        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'status' => 'approved',
            'reviewed_by_admin_id' => $admin->id,
            'admin_remarks' => 'This message is approved'
        ]);
    }

    #[Test]
    public function admin_can_reject_a_message()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        $conversation = Conversation::factory()->create();
        $message = Message::factory()->create([
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
            'status' => 'pending'
        ]);

        $this->actingAs($admin)
            ->put(route('admin.messages.update', $message), [
                'status' => 'rejected',
                'admin_remarks' => 'This message contains inappropriate content'
            ])
            ->assertRedirect(route('admin.messages.index'));

        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'status' => 'rejected',
            'reviewed_by_admin_id' => $admin->id,
            'admin_remarks' => 'This message contains inappropriate content'
        ]);
    }

    #[Test]
    public function admin_cannot_update_message_with_invalid_status()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        $conversation = Conversation::factory()->create();
        $message = Message::factory()->create([
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
            'status' => 'pending'
        ]);

        $this->actingAs($admin)
            ->put(route('admin.messages.update', $message), [
                'status' => 'invalid_status',
                'admin_remarks' => 'This is a test'
            ])
            ->assertSessionHasErrors('status');

        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'status' => 'pending',
        ]);
    }
}
