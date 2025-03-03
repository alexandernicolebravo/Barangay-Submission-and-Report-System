<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuarterlyReport extends Model
{
    protected $fillable = ['user_id', 'report_type_id', 'quarter_number','file_path','file_name','deadline', 'status', 'remarks'];

    public function reportType()
    {
        return $this->belongsTo(ReportType::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
