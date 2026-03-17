<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Log;

class Image extends Model
{
    use HasFactory;

    protected $table='images';

    protected $fillable = [
        'updated_at',
    ];

    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }

    public function annotations()
    {
        return $this->hasMany(Annotation::class, 'image_id','id');
    }

    public function thumbnail()
    {
        $thumbnail='';
//        $batch=$this->batch;
//
//        $path = $batch->project . '/' . $batch->name . '/';
//        // Sub dataset name is optional
//        $path .= is_null($this->sub_dataset_codename) ? '' : $this->sub_dataset_codename . '/';

        foreach (config('app.iv.thumbnail_suffix') as $suffix){
            if (File::exists(public_path('images').'/thumbnail/'.$this->uuid.'.'.$suffix)){
                $thumbnail=url('/images/thumbnail/'.$this->uuid.'.'.$suffix);
                break;
            }
        }

        return $thumbnail;
    }
}
