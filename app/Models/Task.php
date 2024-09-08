<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory  , SoftDeletes;
    // protected $fillable = [
    //     'title',
    //     'description',
    //     'user_id',
    //     'status_id',
    //     'priority_id',
    //     'execute_time',
    //     'rate',
    // ];
    protected $guarded =['due_date','delivired_date'];
    protected $appends = ['user_name' , 'status_name', 'priority_name'];

    public $timestamps = true;
    const CREATED_AT = 'assign_date';
    const UPDATED_AT = 'updated_on';
    protected $perPage = 5;
    public function getStatusNameAttribute()
    {
        return $this->status ? $this->status->name : null;
    }

    public function getPriorityNameAttribute()
    {
        return $this->priority ? $this->priority->name : null;
    }
    public function getUserNameAttribute()
    {
        return $this->priority ? $this->user->name : null;
    }
    // Define relationship to the Status model
    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function priority()
    {
        return $this->belongsTo(Priority::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // protected static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($model) {
    //         if (is_null($model->assign_date)) {
    //             $model->assign_date = now()->format('Y-m-d');
    //         }
    //     });
    // }
}
