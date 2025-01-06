<?php

namespace App\Http\Controllers;

use App\Services\ApplicationService;
use App\Repositories\ApplicationRepository;
use App\Services\MessageService;
use App\Services\TalkService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\MessageSent;
use App\Events\SetRead;

/**
 * メッセージ情報に関する処理を実行するコントローラー
 * @version 1.0.0
 * @author S.Yamamoto
 */
class MessageController extends Controller
{
    protected $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    /**
     * ユーザーに紐づくトーク一覧を取得する
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getMessagesByTalkId($talkId)
    {
        if (!$talkId) {
            return response()->json(['message' => 'トークIDは必須です。'], 400);
        }

        $messages = $this->messageService->getMessagesByTalkId($talkId);
        return response()->json($messages, 200);
    }

    public function sendMessage(Request $request)
    {
        $validatedData = $request->validate([
            'talk_id' => 'required',
            'user_id' => 'required',
            'message' => 'required|string|max:255'
        ]);

        $message = $this->messageService->sendMessage(
            $validatedData['talk_id'],
            $validatedData['user_id'],
            $validatedData['message']
        );

        event(new MessageSent($message));
        return response()->json(null, 201);
    }

    /**
     * メッセージを既読にする
     * @param mixed $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
     public function setReadCount(Request $request)
     {
        $data = $request->validate([
            'talk_id' => 'required',
            'user_id' => 'required',
        ]);

        $updateFlag = $this->messageService->setReadCount($data['talk_id'], $data['user_id']);
        if($updateFlag){
            event(new SetRead($data['talk_id']));
        }
        return response()->json(null, 200);
     }
}

