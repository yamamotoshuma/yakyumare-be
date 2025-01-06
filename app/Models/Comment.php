<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['recruitment_id', 'user_id', 'content'];

    public function recruitment()
    {
        return $this->belongsTo(Recruitment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

