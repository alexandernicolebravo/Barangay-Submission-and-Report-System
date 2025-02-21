<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReportForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'cluster_id',
    ];

    public function barangays()
    {
        return $this->belongsToMany(User::class, 'barangay_report_form', 'report_form_id', 'barangay_id');
    }

    public function cluster()
    {
        return $this->belongsTo(User::class, 'cluster_id');
    }
}
