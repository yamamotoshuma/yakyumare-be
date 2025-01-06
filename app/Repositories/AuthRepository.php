<?php

namespace App\Repositories;

use App\Models\User;

class AuthRepository
{
    public function findById($id)
    {
        return User::find($id);
    }

    public function findByEmail($email)
    {
        return User::where('email', $email)->first();
    }

    public function save(User $user)
    {
        return $user->save();
    }
}
