<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClothingPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'photo_url',
    ];

    public function clothing()
    {
        return $this->hasMany(Clothing::class, 'photo_id');
    }
}
