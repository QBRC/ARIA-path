<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Annotation extends Model
{
    use HasFactory;

    protected $table='annotations';

    protected $fillable = [
        'user_id',
        'image_id',
        'name',
        'note',
        'is_delete',
    ];

    protected $casts = [
        'note' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function image()
    {
        return $this->belongsTo(Image::class, 'image_id');
    }

}
