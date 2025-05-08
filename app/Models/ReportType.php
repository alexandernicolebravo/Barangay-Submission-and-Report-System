<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'frequency', 'deadline', 'allowed_file_types'];

    protected $casts = [
        'allowed_file_types' => 'array'
    ];

    public static function frequencies()
    {
        return ['weekly', 'monthly', 'quarterly', 'semestral', 'annual'];
    }

    public static function availableFileTypes()
    {
        return [
            'pdf' => 'PDF Document',
            'doc' => 'Word Document',
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
        return ucfirst($this->name);
    }
}
