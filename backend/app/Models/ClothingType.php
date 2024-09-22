<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClothingType extends Model
{
    use HasFactory;


    protected $fillable = [
        'type',
    ];

    public function clothing()
    {
        return $this->hasMany(Clothing::class, 'type_id');
    }
}
