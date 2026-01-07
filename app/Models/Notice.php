<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Notice extends Model
{
    use HasFactory;

    protected $fillable = [
        'notice_type',
        'title',
        'related_notice_id',
        'meeting_type',
        'meeting_link',
        'meeting_date',
        'meeting_time',
        'no_of_attendees',
        'board_regulations',
        'board_resolutions',
        'description',
        'attachments',
        'cc_emails',
        'created_by',
    ];

    protected $casts = [
        'attachments' => 'array',
        'board_regulations' => 'array',
        'board_resolutions' => 'array',
        'meeting_date' => 'date',
    ];

    /**
     * Get the CC emails attribute, handling both array and double-encoded JSON strings
     */
    public function getCcEmailsAttribute($value)
    {
        if (empty($value)) {
            return [];
        }

        // If it's already an array, return it
        if (is_array($value)) {
            return $value;
        }

        // If it's a string, try to decode it
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            
            // If decoding failed or returned null, return empty array
            if (json_last_error() !== JSON_ERROR_NONE || $decoded === null) {
                return [];
            }

            // If the decoded value is still a string (double-encoded), decode again
            if (is_string($decoded)) {
                $decoded = json_decode($decoded, true);
                if (json_last_error() !== JSON_ERROR_NONE || $decoded === null) {
                    return [];
                }
            }

            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    /**
     * Set the CC emails attribute, ensuring it's stored as JSON
     */
    public function setCcEmailsAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['cc_emails'] = null;
            return;
        }

        // If it's already a JSON string, store it as is
        if (is_string($value) && json_decode($value) !== null) {
            $this->attributes['cc_emails'] = $value;
            return;
        }

        // If it's an array, encode it to JSON
        if (is_array($value)) {
            $this->attributes['cc_emails'] = json_encode($value);
            return;
        }

        $this->attributes['cc_emails'] = null;
    }

    /**
     * Get the user who created the notice
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the related notice (for Agenda type)
     */
    public function relatedNotice(): BelongsTo
    {
        return $this->belongsTo(Notice::class, 'related_notice_id');
    }

    /**
     * Get attachment media objects
     */
    public function getAttachmentMediaAttribute()
    {
        if (empty($this->attachments)) {
            return collect([]);
        }
        return MediaLibrary::whereIn('id', $this->attachments)->get();
    }

    /**
     * Get users who have access to this notice
     */
    public function allowedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'notice_user_access', 'notice_id', 'user_id')
                    ->withTimestamps();
    }

    /**
     * Get CC emails as array (for backward compatibility)
     */
    public function getCcEmailsArrayAttribute(): array
    {
        if (empty($this->cc_emails)) {
            return [];
        }
        // If it's already an array (new format), extract emails
        if (is_array($this->cc_emails)) {
            return array_column($this->cc_emails, 'email');
        }
        // Legacy format: comma-separated string
        return array_map('trim', explode(',', $this->cc_emails));
    }

    /**
     * Check if user has access to this notice
     */
    public function hasUserAccess($userId): bool
    {
        return $this->allowedUsers()->where('user_id', $userId)->exists();
    }

    /**
     * Scope for Notice of Meeting type
     */
    public function scopeNoticeOfMeeting($query)
    {
        return $query->where('notice_type', 'Notice of Meeting');
    }

    /**
     * Scope for Agenda type
     */
    public function scopeAgenda($query)
    {
        return $query->where('notice_type', 'Agenda');
    }

    /**
     * Scope for Other Matters type
     */
    public function scopeOtherMatters($query)
    {
        return $query->where('notice_type', 'Other Matters');
    }

    /**
     * Get attendance confirmations for this notice
     */
    public function attendanceConfirmations(): HasMany
    {
        return $this->hasMany(AttendanceConfirmation::class);
    }

    /**
     * Get agenda inclusion requests for this notice
     */
    public function agendaInclusionRequests(): HasMany
    {
        return $this->hasMany(AgendaInclusionRequest::class);
    }
}
