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
        'notice_id',
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

    /**
     * Get the Notice of Meeting this regulation is linked to (optional).
     */
    public function notice(): BelongsTo
    {
        return $this->belongsTo(Notice::class, 'notice_id');
    }

    /**
     * Extract regulation number from title (e.g. "BOARD REGULATION NO. 5, SERIES OF 2025 - ...").
     */
    public function getParsedRegulationNumberAttribute(): ?int
    {
        if (preg_match('/board regulation no\.\s*(\d+)/i', $this->title ?? '', $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    /**
     * Extract series year from title (e.g. "BOARD REGULATION NO. 2, SERIES OF 2024").
     */
    public function getParsedSeriesYearAttribute(): ?int
    {
        if (preg_match('/series of\s+(\d{4})/i', $this->title ?? '', $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    public function getYearAttribute(): ?string
    {
        if ($this->parsed_series_year) {
            return (string) $this->parsed_series_year;
        }

        return $this->effective_date
            ? $this->effective_date->format('Y')
            : ($this->approved_date ? $this->approved_date->format('Y') : null);
    }

    /**
     * Get all versions/history for this regulation.
     */
    public function versions(): HasMany
    {
        return $this->hasMany(BoardRegulationVersion::class)->orderBy('created_at', 'desc');
    }
}

