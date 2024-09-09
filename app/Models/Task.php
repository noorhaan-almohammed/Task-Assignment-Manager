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
    public function scopePriority($query, $priority)
    {
        return $query->where('priority_id', $priority);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status_id', $status);
    }

    public function scopeWithDeleted($query)
    {
        return $query->withTrashed();
    }

    public function scopeOnlyDeleted($query)
    {
        return $query->onlyTrashed();
    }

    public function scopeWithoutDeleted($query)
    {
        return $query->withoutTrashed();
    }
    protected $appends = ['user_name', 'status_name', 'priority_name'];


    public function getStatusNameAttribute() {
        return $this->status ? $this->status->name : null;
    }

    public function getPriorityNameAttribute() {
        return $this->priority ? $this->priority->name : null;
    }

    public function getUserNameAttribute() {
        return $this->user ? $this->user->name : null;
    }

    public function getDueDateAttribute($value)
    {
        return $value ? \Carbon\Carbon::parse($value)->format('d-m-Y H:i') : null;
    }

    public function getCompleteDateAttribute($value)
    {
        return $value ? \Carbon\Carbon::parse($value)->format('d-m-Y H:i') : null;
    }

    public function getAssignDateAttribute($value)
    {
        return $value ? \Carbon\Carbon::parse($value)->format('d-m-Y H:i') : null;
    }

}
