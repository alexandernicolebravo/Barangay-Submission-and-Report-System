<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklyReport extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'month',
        'week_number',
        'num_of_clean_up_sites',
        'num_of_participants',
        'num_of_barangays',
        'total_volume',
        'kalinisan_file_path',
        'fields',
    ];

    protected $casts =[
        'fields'=>'array'
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }

}
