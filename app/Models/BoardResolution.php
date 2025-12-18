<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BoardResolution extends Model
{
    protected $fillable = [
        'resolution_number',
        'title',
        'description',
        'pdf_file',
        'category',
        'approved_date',
        'uploaded_by',
    ];

    protected $casts = [
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
     * Get the user who uploaded this resolution
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the year from approved_date
     */
    public function getYearAttribute(): ?string
    {
        return $this->approved_date ? $this->approved_date->format('Y') : null;
    }
}
