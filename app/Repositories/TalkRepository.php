<?php

namespace App\Repositories;

use App\Models\Talk;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * トーク情報に関する処理を実行するレポジトリ
 * @version 1.0.0
 * @author S.Yamamoto
 */
class TalkRepository
{
    public function getTalksByUserId($userId)
    {
        return Talk::with([
            'messages' => function ($query) {
                $query->orderBy('created_at', 'desc'); // 最新のメッセージを取得
            },
            'users',
            'application', // applicationを紐づけ
        ])
        ->withCount(['messages as unread_count' => function ($query) use ($userId) {
            $query->where('read_count', 0) // read_countが0のメッセージをカウント
                  ->where('send_user_id', '!=', 'system')
                  ->where('send_user_id', '!=', $userId);
        }])
        ->whereHas('users', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->get()
        ->sortByDesc(function ($talk) {
            // 各トークごとの最新メッセージでソート
            return optional($talk->messages->first())->created_at;
        })
        ->values();
    }

    public function createTalk($id)
    {
        // 連番IDの生成
        $lastTalk = Talk::orderBy('id', 'desc')->first();
        $nextId = $lastTalk ? intval(substr($lastTalk->id, 3)) + 1 : 1; // YKMの後の数字を取得
        $customId = 'TLK' . str_pad($nextId, 10, '0', STR_PAD_LEFT); // YKM0000000001形式

        return Talk::create(['id' => $customId, 'application_id' => $id]);
    }
}
