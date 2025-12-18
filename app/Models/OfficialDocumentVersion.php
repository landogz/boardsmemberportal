<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfficialDocumentVersion extends Model
{
    protected $fillable = [
        'official_document_id',
        'pdf_file',
        'version',
        'title',
        'description',
        'effective_date',
        'approved_date',
        'uploaded_by',
        'change_notes',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'approved_date' => 'date',
    ];

    /**
     * Get the official document this version belongs to.
     */
    public function officialDocument(): BelongsTo
    {
        return $this->belongsTo(OfficialDocument::class);
    }

    /**
     * Get the PDF file associated with this version.
     */
    public function pdf(): BelongsTo
    {
        return $this->belongsTo(MediaLibrary::class, 'pdf_file');
    }

    /**
     * Get the user who created this version.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
