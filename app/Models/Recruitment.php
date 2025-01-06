<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recruitment extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'string';

    protected $dates = ['deleted_at'];

    public $incrementing = false;

    protected $fillable = [
        'id',
        'type_id',
        'title',
        'content',
        'event_date',
        'deadline',
        'prefecture',
        'city',
        'place',
        'capacity',
        'active',
        'user_id'
    ];

    public function type()
    {
        return $this->belongsTo(RecruitmentType::class, 'type_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}

