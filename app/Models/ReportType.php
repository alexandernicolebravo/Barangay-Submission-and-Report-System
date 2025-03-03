<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'frequency','deadline'];

    public static function frequencies()
    {
        return ['weekly', 'monthly', 'quarterly', 'semestral', 'annual'];
    }

    public function getFormattedNameAttribute()
    {
        return ucfirst($this->name);
    }

}
