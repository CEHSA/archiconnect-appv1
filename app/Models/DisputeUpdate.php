<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisputeUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'dispute_id',
        'user_id',
        'old_status',
        'new_status',
        'old_admin_remarks',
        'new_admin_remarks',
    ];

    /**
     * Get the dispute that the update belongs to.
     */
    public function dispute(): BelongsTo
    {
        return $this->belongsTo(Dispute::class);
    }

    /**
     * Get the user who made the update.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
