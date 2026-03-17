<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Contracts\Activity;
use Auth;

class Group extends Model
{
    use HasFactory, LogsActivity;

    protected $table='groups';

    protected $fillable = [
        'name',
        'pi',
        'org',
        'is_delete',
    ];

    public function users()
    {
        return $this->hasMany(User::class,'group_id');
    }

    public function batches()
    {
        return $this->belongsToMany(Batch::class, 'permissions', 'group_id', 'batch_id');
    }

    public function showBatches()
    {
        return $this->belongsToMany(Batch::class, 'permissions', 'group_id', 'batch_id')
            ->withTimestamps()
            ->withPivot(['is_delete'])
            ->wherePivot('is_delete', 0)
            ->where('batches.id', '<>', 1); // Don't display default
    }


    public function availBatches()
    {
        return $this->belongsToMany(Batch::class, 'permissions', 'group_id', 'batch_id')
            ->withTimestamps()
            ->withPivot(['is_delete'])
            ->wherePivot('is_delete', 0)
            ->orWhere('id', 1);
    }


//    protected static $logAttributes = ['name', 'is_delete'];
//    protected static $logFillable = true;
//    protected static $logOnlyDirty = true;
//
//    protected static $logName = 'Project';
//    protected static $recordEvents = ['created','updated','deleted'];
//
//    public function tapActivity(Activity $activity, string $eventName)
//    {
//        $attr=json_decode($activity->properties);
//        if ($eventName == 'updated'){
//            if(isset($attr->attributes->is_delete) && $attr->attributes->is_delete == 0 && $attr->old->is_delete == 1){
//                $activity->description = "restore";
//            }elseif (isset($attr->attributes->is_delete) && $attr->attributes->is_delete == 1 && $attr->old->is_delete == 0){
//                $activity->description = "soft-delete";
//            }
//
//            $temp=[];
//            foreach(get_object_vars($attr->attributes) as $k=>$v){
//                if ($k != 'is_delete'){
//                    array_push($temp, ucfirst($k).": ".$attr->old->$k." &rarr; ".$v);
//                }
//            }
//        }elseif($eventName == 'created'){
//            $temp=[];
//            foreach(get_object_vars($attr->attributes) as $k=>$v){
//                if ($k != 'is_delete'){
//                    array_push($temp, ucfirst($k).": ".$v);
//                }
//            }
//        }
//
//        $activity->show_changes=implode(", ", $temp);
//    }
}
