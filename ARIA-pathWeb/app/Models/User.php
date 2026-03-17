<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Contracts\Activity;
use Log;
//use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable
{
    use HasFactory, Notifiable, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'group_id',
        'role',
        'password',
        'is_delete',
        'last_login_at',
        'last_login_ip',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $table = 'users';

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }


    public function images()
    {
        $images = [];

        foreach ($this->group->showBatches as $batch) {
            foreach ($batch->images as $image) {
                $images[] = $image;
            }
        }

        return $images;
    }

    public function imagesID()
    {
        $images = [];

        $allIcanAccess=$this->group->showBatches;

        $containsDefault = $allIcanAccess->contains(function ($batch) {
            return $batch->id === 1;
        });

        foreach ($allIcanAccess as $batch) {
            foreach ($batch::with('images')->where('id',$batch->id)->get() as $v) {
                foreach ($v->images as $image){
                    $images[] = $image->id;
                }
            }
        }

        if (!$containsDefault){
            foreach (Image::where('batch_id',1)->get() as $image){
                $images[] = $image->id;
            }
        }

        return $images;
    }


    public function annotations()
    {
        return $this->hasMany(Annotation::class, 'user_id','id');
    }
//    protected static $logFillable = true;
//    protected static $logOnlyDirty = true;
//
//    protected static $logName = 'User';
//    protected static $recordEvents = ['created','updated','deleted'];
//
//    public function tapActivity(Activity $activity, string $eventName)
//    {
//        $attr=json_decode($activity->properties);
//        $temp='';
//
//        if ($eventName == 'updated'){
//            if(isset($attr->attributes->is_delete) && $attr->attributes->is_delete == 0 && $attr->old->is_delete == 1){
//                $activity->description = "restore";
//            }elseif (isset($attr->attributes->is_delete) && $attr->attributes->is_delete == 1 && $attr->old->is_delete == 0){
//                $activity->description = "soft-delete";
//            }
//
//            if(isset($attr->attributes->password) && $attr->attributes->password != $attr->old->password){
//                $temp='Change password';
//            }
//
//            if(isset($attr->attributes->role) && $attr->attributes->role != $attr->old->role){
//                $temp=config('app.iv.user.role')[$attr->old->role]." &rarr; ".config('app.iv.user.role')[$attr->attributes->role];
//            }
//        }elseif($eventName == 'created'){
//            $temp=config('app.iv.user.role')[$attr->attributes->role];
//        }
//
//        $activity->show_changes=$temp;
//    }
}
