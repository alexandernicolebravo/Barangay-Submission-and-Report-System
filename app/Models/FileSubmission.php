<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_submission_id',
        'user_id',
        'file_path',
        'status', // submitted, pending, completed
        'resubmittable',
    ];

    public function reportSubmission()
    {
        return $this->belongsTo(ReportSubmission::class, 'report_submission_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
