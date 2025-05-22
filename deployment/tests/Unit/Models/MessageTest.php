<?php

namespace Tests\Unit\Models;

use App\Models\Message;

use App\Models\User;

use App\Models\Conversation;

use Illuminate\Foundation\Testing\RefreshDatabase;


use Tests\TestCase;
class MessageTest extends TestCase
{
    use RefreshDatabase;


    #[Test]
    public function it_has_correct_fillable_attributes()
    {
        $message = new Message();
        
        $this->assertEquals([
            'conversation_id',
            'user_id',
            'content',
            'read_at',
            'status',
            'reviewed_by_admin_id',
            'admin_remarks',
        ], $message->getFillable());
    }

    #[Test]
    public function it_has_correct_casts()
    {
        $message = new Message();
        
        $this->assertEquals([
            'read_at' => 'datetime',
        ], $message->getCasts());
    }

    #[Test]
    public function it_belongs_to_a_conversation()
    {
        $conversation = Conversation::factory()->create();
        $message = Message::factory()->create(['conversation_id' => $conversation->id]);

        $this->assertInstanceOf(Conversation::class, $message->conversation);
        $this->assertEquals($conversation->id, $message->conversation->id);
    }

    #[Test]
    public function it_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $message = Message::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $message->user);
        $this->assertEquals($user->id, $message->user->id);
    }

    #[Test]
    public function it_belongs_to_admin_who_reviewed_it()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $message = Message::factory()->create(['reviewed_by_admin_id' => $admin->id]);

        $this->assertInstanceOf(User::class, $message->reviewedByAdmin);
        $this->assertEquals($admin->id, $message->reviewedByAdmin->id);
    }

    #[Test]
    public function it_can_be_created_with_valid_data()
    {
        $conversation = Conversation::factory()->create();
        $user = User::factory()->create();
        
        $messageData = [
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
            'content' => 'This is a test message',
            'status' => 'pending',
        ];

        $message = Message::create($messageData);

        $this->assertInstanceOf(Message::class, $message);
        $this->assertDatabaseHas('messages', $messageData);
    }
}
