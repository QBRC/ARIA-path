<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasFactory;
    protected $table='batches';
    protected $fillable = [
        'name',
        'project',
        'group_id',
        'description',
        'is_delete',
    ];

    public function images()
    {
        return $this->hasMany(Image::class,'batch_id');
    }

    public function annotations()
    {
        return $this->hasManyThrough(Annotation::class,Image::class, 'batch_id', 'image_id', 'id', 'id');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'permissions', 'batch_id', 'group_id')
            ->wherePivot('is_delete', 0);
    }

    public function providerName()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
}
