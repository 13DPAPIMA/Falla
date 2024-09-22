<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Style extends Model
{
    use HasFactory;

    protected $fillable = [
        'style',
    ];

    public function clothing()
    {
        return $this->hasMany(Clothing::class, 'style_id');
    }
}
