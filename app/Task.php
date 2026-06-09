<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'title', 'description', 'priority', 'due_date', 'is_completed'
    ];

    protected $casts = [
        'due_date' => 'date',
        'reminder_at' => 'datetime',
        'is_completed' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function getLikesCountAttribute()
    {
        return $this->likes()->where('type', 'like')->count();
    }

    public function getDislikesCountAttribute()
    {
        return $this->likes()->where('type', 'dislike')->count();
    }
}