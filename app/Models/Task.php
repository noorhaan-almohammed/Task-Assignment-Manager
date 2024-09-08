<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $perPage = 5;

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class );
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function priority()
    {
        return $this->belongsTo(Priority::class);
    }
    // protected $appends = ['user_name', 'status_name', 'priority_name'];


    // public function getStatusNameAttribute() {
    //     return $this->status ? $this->status->name : null;
    // }

    // public function getPriorityNameAttribute() {
    //     return $this->priority ? $this->priority->name : null;
    // }

    // public function getUserNameAttribute() {
    //     return $this->user ? $this->user->name : null;
    // }

}
