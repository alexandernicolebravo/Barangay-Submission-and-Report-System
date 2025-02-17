<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangayFile extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'form_id', 'file_name', 'file_path'];

    public function clusterForm()
    {
        return $this->belongsTo(ClusterForm::class, 'form_id');
    }
}

