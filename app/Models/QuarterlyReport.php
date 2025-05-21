<?php

namespace App\Models;

use App\Models\Traits\ReportStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuarterlyReport extends Model
{
    use HasFactory, ReportStatus;

    protected $fillable = [
        'user_id',
        'report_type_id',
        'quarter_number',
        'year',
        'file_name',
        'file_path',
        'deadline',
        'status',
        'remarks',
        'can_update'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reportType()
    {
        return $this->belongsTo(ReportType::class);
    }
}
