<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TalkUser extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    // プライマリキー設定
    protected $primaryKey = ['talk_id', 'user_id'];
    // increment無効化
    public $incrementing = false;

    protected $fillable = ['talk_id', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id'); // 'user_id'を外部キーとして指定
    }
}
