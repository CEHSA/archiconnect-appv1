<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FreelancerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'skills',
        'portfolio_link',
        'hourly_rate',
        'bio',
        'profile_picture_path',
        'availability',
        'experience_level',
        'receive_new_job_notifications', // Add this line
    ];

    /**
     * Get the user that owns the freelancer profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
