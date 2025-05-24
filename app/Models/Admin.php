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
        'user_id',
    ];

    /**
     * Get the user associated with the admin.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

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
     * Define the relationship for conversations this admin is part of
     */
    public function conversations()
    {
        return $this->belongsToMany(Conversation::class, 'conversation_user', 'user_id', 'conversation_id')
                    ->withPivot('last_read_at')
                    ->withTimestamps();
    }

    /**
     * Get the total number of unread messages for the admin across all conversations.
     */
    public function totalUnreadMessagesCount(): int
    {
        return $this->conversations()
            ->join('messages', 'conversations.id', '=', 'messages.conversation_id')
            ->where(function ($query) {
                $query->whereNull('conversation_user.last_read_at')
                    ->orWhere('messages.created_at', '>', 'conversation_user.last_read_at');
            })
            ->where('messages.user_id', '!=', $this->id) // Don't count own messages
            ->count();
    }
}
