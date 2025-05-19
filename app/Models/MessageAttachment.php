<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'file_path',
        'original_file_name',
        'file_size',
        'file_type',
    ];

    /**
     * Get the message that this attachment belongs to.
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }
}
