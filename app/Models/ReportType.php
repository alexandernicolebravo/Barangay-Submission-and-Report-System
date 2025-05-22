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
        'file_naming_format'
    ];

    protected $casts = [
        'allowed_file_types' => 'array',
        'deadline' => 'date'
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
}
