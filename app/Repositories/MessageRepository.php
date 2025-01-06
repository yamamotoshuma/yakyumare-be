<?php

namespace App\Repositories;

use App\Models\Message;
use Illuminate\Support\Facades\DB;

/**
 * メッセージに関する処理を実行するレポジトリ
 * @version 1.0.0
 * @author S.Yamamoto
 */
class MessageRepository
{
    public function getMessagesByUserId($userId)
    {
        return Message::where('send_user_id', $userId)
                    ->orWhereHas('talk.users', function ($query) use ($userId) {
                        $query->where('user_id', $userId);
                    })
                    ->get();
    }

    public function getMessagesByTalkId($talkId)
    {
        return Message::where('talk_id', $talkId)->get();
    }

    public function getMessagesByTalkIdAndUserId($talkId, $userId)
    {
        return Message::where('talk_id', $talkId)->where('send_user_id', $userId)->get();
    }

    public function updateMessages($messages)
    {
        return DB::transaction(function () use ($messages) {
            foreach ($messages as $message) {
                $message->save();
            }
            return $messages;
        });
    }

    public function createMessage($talkId, $userId, $message)
    {
        return Message::create(['talk_id' => $talkId, 'send_user_id' => $userId, 'message' => $message]);
    }
}
