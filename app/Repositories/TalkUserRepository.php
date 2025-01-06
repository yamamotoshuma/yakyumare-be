<?php

namespace App\Repositories;

use App\Models\Talk;
use App\Models\TalkUser;
use Illuminate\Support\Facades\DB;

/**
 * トークユーザー情報に関する処理を実行するレポジトリ
 * @version 1.0.0
 * @author S.Yamamoto
 */
class TalkUserRepository
{
    public function getTalkUser($talkId, $userId)
    {
        return TalkUser::with('user') // 'user'リレーションを事前にロード
            ->where('talk_id', $talkId) // talk_idが一致する
            ->where('user_id', '!=', $userId) // 指定されたuserId以外のユーザーを取得
            ->first(); // 最初の結果を返す
    }

    public function createTalkUser($talkId,$userId)
    {
        return TalkUser::create(['talk_id' => $talkId, 'user_id' => $userId]);
    }
}
