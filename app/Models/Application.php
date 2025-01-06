<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['id', 'recruitment_id', 'recruited_user_id', 'apply_user_id', 'approval'];

    protected $dates = ['deleted_at'];

    protected $keyType = 'string';

    protected $casts = [
        'id' => 'string'
    ];

    public $incrementing = false;

    public function recruitment()
    {
        return $this->belongsTo(Recruitment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function apply_user() // apply_userリレーションを追加
    {
        return $this->belongsTo(User::class, 'apply_user_id');
    }
}

