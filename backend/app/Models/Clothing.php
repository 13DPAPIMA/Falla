<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clothing extends Model
{
    use HasFactory;

    protected $fillable = [
        'style_id',
        'photo_id',
        'type_id',
        'temperature_range_id',
        'material_id',
        'gender',
        'color',
        'water_resistant',
    ];

    public function style()
    {
        return $this->belongsTo(Style::class);
    }

    public function photo()
    {
        return $this->belongsTo(ClothingPhoto::class, 'photo_id');
    }

    public function type()
    {
        return $this->belongsTo(ClothingType::class);
    }

    public function temperatureRange()
    {
        return $this->belongsTo(TemperatureRange::class);
    }

    public function material()
    {
        return $this->belongsTo(ClothingMaterial::class);
    }
}
