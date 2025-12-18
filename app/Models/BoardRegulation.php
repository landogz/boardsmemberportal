<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BoardRegulation extends Model
{
    protected $fillable = [
        'title',
        'description',
        'pdf_file',
        'version',
        'effective_date',
        'approved_date',
        'uploaded_by',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'approved_date' => 'date',
    ];

    public function pdf(): BelongsTo
    {
        return $this->belongsTo(MediaLibrary::class, 'pdf_file');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getYearAttribute(): ?string
    {
        return $this->effective_date ? $this->effective_date->format('Y') : null;
    }

    /**
     * Get all versions/history for this regulation.
     */
    public function versions(): HasMany
    {
        return $this->hasMany(BoardRegulationVersion::class)->orderBy('created_at', 'desc');
    }
}

