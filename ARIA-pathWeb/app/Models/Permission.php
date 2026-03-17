<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Contracts\Activity;

class Permission extends Model
{
    use HasFactory, LogsActivity;

    protected $table='permissions';

    protected $fillable = [
        'group_id',
        'batch_id',
        'is_delete',
    ];

//    protected static $logFillable = true;
////    protected static $logOnlyDirty = true;
//
//    protected static $logName = 'Permission';
//    protected static $recordEvents = ['created','updated','deleted'];
//
//    public function tapActivity(Activity $activity, string $eventName)
//    {
//        $attr=json_decode($activity->properties);
//        $activity->item_name = User::find($attr->attributes->user_id)->name." @ ".Project::find($attr->attributes->project_id)->name;
//        if ($eventName == 'created'){
//            $temp=[];
//
//            foreach(config('app.iv.project.permission') as $k=>$v){
//                if ($k<=$attr->attributes->permission_id && $k!=0){
//                    array_push($temp, $v);
//                }
//            }
//            $activity->show_changes=implode(", ", $temp);
//        }elseif ($eventName == 'updated'){
//            $temp1=[];
//            $temp2=[];
//
//            foreach(config('app.iv.project.permission') as $k=>$v){
//                if ($k<=$attr->attributes->permission_id && $k!=0){
//                    array_push($temp1, $v);
//                }
//                if ($k<=$attr->old->permission_id && $k!=0){
//                    array_push($temp2, $v);
//                }
//            }
//            $activity->show_changes=implode(", ", $temp2)." &rarr; ".implode(", ", $temp1);
//        }
//    }
}
