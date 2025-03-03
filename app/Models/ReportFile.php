<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportFile extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'report_type_id', 'file_path', 'deadline', 'status', 'remarks'];

    public function reportType()
    {
        return $this->belongsTo(ReportType::class);
    }
}
