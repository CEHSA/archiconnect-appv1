<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetAppeal extends Model
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
        'current_budget',
        'requested_budget',
        'reason',
        'evidence_path',
        'status',
        'admin_remarks',
        'client_decision',
        'client_remarks',
    ];

    /**
     * Get the job assignment associated with the budget appeal.
     */
    public function jobAssignment(): BelongsTo
    {
        return $this->belongsTo(JobAssignment::class);
    }

    /**
     * Get the freelancer associated with the budget appeal.
     */
    public function freelancer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }
}
