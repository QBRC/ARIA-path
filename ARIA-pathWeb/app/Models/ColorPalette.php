<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ColorPalette extends Model
{
    use HasFactory;

    protected $fillable = [
        'image_id',
        'user_id',
        'color_palette'
    ];

    protected $casts = [
        'color_palette' => 'array',  // Ensure color_palette is cast as an array
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
