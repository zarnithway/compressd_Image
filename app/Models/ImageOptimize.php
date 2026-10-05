<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImageOptimize extends Model
{
    protected $fillable = [
        'path',
        'size',
        'type',
    ];
}
