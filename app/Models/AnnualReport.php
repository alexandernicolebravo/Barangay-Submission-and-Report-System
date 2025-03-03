<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnualReport extends Model
{
    protected $fillable = ['user_id', 'report_type_id','file_path','file_name','deadline', 'status', 'remarks'];

    public function reportType()
    {
        return $this->belongsTo(ReportType::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
