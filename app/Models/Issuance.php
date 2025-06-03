<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Issuance extends Model
{
    protected $fillable = [
        'title',
        'file_name',
        'file_path',
        'file_size',
        'file_type',
        'uploaded_by',
        'archived_at'
    ];

    protected $dates = [
        'archived_at'
    ];

    /**
     * Get the user who uploaded this issuance.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the file size in human readable format.
     */
    public function getFileSizeHumanAttribute(): string
    {
        if (!$this->file_size) {
            return 'Unknown';
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Scope to get only active (non-archived) issuances.
     */
    public function scopeActive($query)
    {
        return $query->whereNull('archived_at');
    }

    /**
     * Scope to get only archived issuances.
     */
    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }

    /**
     * Check if the issuance is archived.
     */
    public function isArchived(): bool
    {
        return !is_null($this->archived_at);
    }

    /**
     * Archive the issuance.
     */
    public function archive()
    {
        $this->update(['archived_at' => now()]);
    }

    /**
     * Unarchive the issuance.
     */
    public function unarchive()
    {
        $this->update(['archived_at' => null]);
    }
}
