<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientProfile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'company_name',
        'project_preferences',
        'contact_details',
        'profile_picture',
        'company_website',
        'industry',
        'receive_application_notifications', // Added
    ];

    protected $casts = [
        'receive_application_notifications' => 'boolean', // Added
    ];

    /**
     * Get the user that owns the client profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
