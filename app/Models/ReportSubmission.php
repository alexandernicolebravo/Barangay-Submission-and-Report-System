<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status', // active, completed, rejected
    ];

    public function fileSubmissions()
    {
        return $this->hasMany(FileSubmission::class, 'report_submission_id');
    }
}
