<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AttendanceConfirmation extends Model
{
    use HasFactory;

    protected $fillable = [
        'notice_id',
        'user_id',
        'status',
        'declined_reason',
    ];

    /**
     * Get the notice
     */
    public function notice(): BelongsTo
    {
        return $this->belongsTo(Notice::class);
    }

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the agenda inclusion request
     */
    public function agendaInclusionRequest(): HasOne
    {
        return $this->hasOne(AgendaInclusionRequest::class);
    }
}
