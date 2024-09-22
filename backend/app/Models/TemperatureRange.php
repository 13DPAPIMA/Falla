<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemperatureRange extends Model
{
    use HasFactory;

    protected $fillable = [
        'temperature_range',
    ];

    public function clothing()
    {
        return $this->hasMany(Clothing::class, 'temperature_range_id');
    }
}
