<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClusterForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'accepted_file_types',
        'max_file_size',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
