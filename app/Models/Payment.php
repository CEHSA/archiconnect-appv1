<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'job_assignment_id',
        'freelancer_id',
        'amount',
        'status',
        'transaction_id',
        'admin_notes',
    ];

    /**
     * Get the job assignment associated with the payment.
     */
    public function jobAssignment(): BelongsTo
    {
        return $this->belongsTo(JobAssignment::class);
    }

    /**
     * Get the freelancer associated with the payment.
     */
    public function freelancer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }
}
