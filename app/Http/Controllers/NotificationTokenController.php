<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\NotificationToken;
use Illuminate\Support\Facades\Auth;

class NotificationTokenController extends Controller
{
    //
    public function store(Request $request)
    {
        // バリデーション
        $request->validate([
            'token' => 'required|string',
        ]);

        $user = Auth::user();

        if (!$user) {
            throw new ModelNotFoundException('ユーザーが見つかりません。');
        }

        // トークンをデータベースに保存
        $notificationToken = NotificationToken::where('user_id', $user->id)->where('token', $request->token)->first();

        if (!$notificationToken) {
            NotificationToken::create([
                'user_id' => $user->id,
                'token' => $request->token
            ]);
        }

        return response()->json(['message' => 'Token saved successfully.'], 201);
    }
}
