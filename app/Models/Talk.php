<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Talk extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    protected $fillable = ['id','application_id'];

    protected $casts = [
        'id' => 'string'
    ];

    public $incrementing = false;

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'talk_users', 'talk_id', 'user_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'talk_id', 'id');
    }
}
