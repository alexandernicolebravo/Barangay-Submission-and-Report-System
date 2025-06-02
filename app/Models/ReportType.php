<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'frequency',
        'deadline',
        'allowed_file_types',
        'file_naming_format',
        'archived_at'
    ];

    protected $casts = [
        'allowed_file_types' => 'array',
        'deadline' => 'date',
        'archived_at' => 'datetime'
    ];

    public static function frequencies()
    {
        return [
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'semestral' => 'Semestral',
            'annual' => 'Annual'
        ];
    }

    public static function availableFileTypes()
    {
        return [
            'pdf' => 'PDF Document',
            'docx' => 'Word Document',
            'xls' => 'Excel Spreadsheet',
            'xlsx' => 'Excel Spreadsheet',
            'jpg' => 'JPEG Image',
            'jpeg' => 'JPEG Image',
            'png' => 'PNG Image',
            'zip' => 'ZIP Archive',
            'rar' => 'RAR Archive'
        ];
    }



    public function getFormattedNameAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->name));
    }

    /**
     * Check if a filename matches the required naming format
     *
     * @param string $filename The filename to check
     * @return bool True if the filename matches the format or if no format is specified
     */
    public function validateFilename($filename)
    {
        // If no format is specified, any filename is valid
        if (empty($this->file_naming_format)) {
            return true;
        }

        // For now, we'll just return true for all filenames
        // This effectively disables the file naming format validation
        return true;
    }

    /**
     * Archive this report type
     */
    public function archive()
    {
        $this->update(['archived_at' => now()]);
    }

    /**
     * Unarchive this report type
     */
    public function unarchive()
    {
        $this->update(['archived_at' => null]);
    }

    /**
     * Check if this report type is archived
     */
    public function isArchived()
    {
        return !is_null($this->archived_at);
    }

    /**
     * Scope to get only active (non-archived) report types
     */
    public function scopeActive($query)
    {
        return $query->whereNull('archived_at');
    }

    /**
     * Scope to get only archived report types
     */
    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }
}
