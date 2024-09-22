<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WardrobeItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'wardrobe_id',
        'clothing_id',
    ];

    public function wardrobe()
    {
        return $this->belongsTo(Wardrobe::class);
    }

    public function clothing()
    {
        return $this->belongsTo(Clothing::class);
    }
}
