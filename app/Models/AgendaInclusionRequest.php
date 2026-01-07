<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgendaInclusionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'notice_id',
        'user_id',
        'attendance_confirmation_id',
        'description',
        'attachments',
        'status',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get the notice
     */
    public function notice(): BelongsTo
    {
        return $this->belongsTo(Notice::class);
    }

    /**
     * Get the user who requested
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the attendance confirmation
     */
    public function attendanceConfirmation(): BelongsTo
    {
        return $this->belongsTo(AttendanceConfirmation::class);
    }

    /**
     * Get the reviewer
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get attachment media objects
     */
    public function getAttachmentMediaAttribute()
    {
        if (empty($this->attachments)) {
            return collect([]);
        }
        return \App\Models\MediaLibrary::whereIn('id', $this->attachments)->get();
    }
}
