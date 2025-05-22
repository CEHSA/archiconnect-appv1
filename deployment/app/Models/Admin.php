<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// It's good practice to import models used in methods, even if just for type hinting or clarity.
use App\Models\Conversation;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guard = 'admin';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'admins';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Check if the admin has a specific role.
     * For Admin model, this will typically just check if the role is 'admin'.
     *
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        // Admins are always 'admin' role by definition of this model/guard
        return strtolower($role) === 'admin';
    }

    /**
     * Get the total number of unread messages for the admin.
     * This assumes Admins can be participants in conversations.
     */
    public function totalUnreadMessagesCount(): int
    {
        $totalUnread = 0;
        // Fetch all conversations the admin is part of
        // Uses getMorphClass() to correctly identify the Admin model in polymorphic relations
        $conversations = Conversation::where(function ($query) {
            $query->where('participant1_id', $this->id)
                  ->where('participant1_type', $this->getMorphClass());
        })->orWhere(function ($query) {
            $query->where('participant2_id', $this->id)
                  ->where('participant2_type', $this->getMorphClass());
        })->with('messages')->get(); // Eager load messages for efficiency

        foreach ($conversations as $conversation) {
            // The unreadCount method in Conversation model now accepts User or Admin
            $totalUnread += $conversation->unreadCount($this);
        }
        return $totalUnread;
    }
}
