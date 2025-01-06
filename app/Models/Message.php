<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'talk_id',
        'send_user_id',
        'read_count',
        'message'
    ];

    public function talk()
    {
        return $this->belongsTo(Talk::class, 'talk_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'send_user_id');
    }
}
