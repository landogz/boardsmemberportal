<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfficialDocument extends Model
{
    protected $fillable = [
        'resolution_number',
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

    /**
     * Get the PDF file from media library
     */
    public function pdf(): BelongsTo
    {
        return $this->belongsTo(MediaLibrary::class, 'pdf_file');
    }

    /**
     * Get the user who uploaded this document
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the Notice of Meeting this resolution is linked to (optional)
     */
    public function notice(): BelongsTo
    {
        return $this->belongsTo(Notice::class, 'notice_id');
    }

    /**
     * Extract resolution number from title (e.g. "BOARD RESOLUTION NO. 11, SERIES OF 2025 - ...").
     */
    public function getParsedResolutionNumberAttribute(): ?int
    {
        if (preg_match('/board\s+resolution\s+no\.?\s*(\d+)/i', $this->title ?? '', $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    /**
     * Extract series year from title (e.g. "BOARD RESOLUTION NO. 10, SERIES OF 2025").
     * This is the classification year — not the approval/effectivity year.
     */
    public function getParsedSeriesYearAttribute(): ?int
    {
        if (preg_match('/series\s+of\s+(?:year\s+)?(\d{4})/i', $this->title ?? '', $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    /**
     * Series classification year for listing/grouping.
     * Prefer SERIES OF YYYY from the title; do not use dates when series is present.
     */
    public function getYearAttribute(): ?string
    {
        if ($this->parsed_series_year) {
            return (string) $this->parsed_series_year;
        }

        return $this->approved_date
            ? $this->approved_date->format('Y')
            : ($this->effective_date ? $this->effective_date->format('Y') : null);
    }

    /**
     * Whether this resolution belongs to a given series year (title-based).
     */
    public function belongsToSeriesYear(int $year): bool
    {
        return (int) $this->year === $year;
    }

    /**
     * Get all versions/history for this document.
     */
    public function versions()
    {
        return $this->hasMany(OfficialDocumentVersion::class)->orderBy('created_at', 'desc');
    }
}
