<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wardrobe extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wardrobeItems()
    {
        return $this->hasMany(WardrobeItem::class);
    }

    public function clothings()
    {
        return $this->belongsToMany(Clothing::class, 'wardrobe_items', 'wardrobe_id', 'clothing_id');
    }
}
