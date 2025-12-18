<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfficialDocument extends Model
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
     * Get the year from effective_date
     */
    public function getYearAttribute(): ?string
    {
        return $this->effective_date ? $this->effective_date->format('Y') : null;
    }

    /**
     * Get all versions/history for this document.
     */
    public function versions()
    {
        return $this->hasMany(OfficialDocumentVersion::class)->orderBy('created_at', 'desc');
    }
}
