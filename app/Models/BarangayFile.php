<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangayFile extends Model {
    use HasFactory;
    protected $fillable = ['barangay_id', 'report_id', 'file_name', 'file_path', 'status'];

    public function report() {
        return $this->belongsTo(Report::class);
    }
}
