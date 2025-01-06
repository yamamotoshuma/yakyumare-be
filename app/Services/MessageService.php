<?php

namespace App\Services;

use App\Models\NotificationToken;
use App\Models\User;
use App\Repositories\ApplicationRepository;
use App\Repositories\MessageRepository;
use App\Repositories\RecruitmentRepository;
use App\Repositories\TalkRepository;
use App\Repositories\TalkUserRepository;
use App\Utils\MailUtil;
use App\Utils\NotificationUtil;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * トーク情報に関する処理を実行するサービス
 * @version 1.0.0
 * @author S.Yamamoto
 */
class MessageService
{
    protected $applicationRepository;
    protected $talkRepository;
    protected $talkUserRepository;
    protected $recruitmentRepository;
    protected $messageRepository;

    public function __construct(ApplicationRepository $applicationRepository,TalkRepository $talkRepository, TalkUserRepository $talkUserRepository, RecruitmentRepository $recruitmentRepository, MessageRepository $messageRepository)
    {
        $this->applicationRepository = $applicationRepository;
        $this->talkRepository = $talkRepository;
        $this->talkUserRepository = $talkUserRepository;
        $this->recruitmentRepository = $recruitmentRepository;
        $this->messageRepository = $messageRepository;
    }

    /**
     * メッセージ情報一覧取得
     * @param array $filters
     * @param mixed $perPage
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public function getMessagesByTalkId($talkId)
    {
        $messages = $this->messageRepository->getMessagesByTalkId($talkId);

        return $messages->map(function ($message) {
            return [
                'id' => $message->id,
                'senderId' => $message->send_user_id,
                'content' => $message->message,
                'createdDate' => $message->created_at->format('Y-m-d H:i:s'),
                'readCount' => $message->read_count,
            ];
        });
    }

    public function sendMessage($talkId, $userId, $message)
    {
        return DB::transaction(function () use ($talkId, $userId, $message){
            $user = Auth::user();

            $createdMessage = $this->messageRepository->createMessage($talkId, $userId, $message);
            // 通知処理
            $talkUser = $this->talkUserRepository->getTalkUser($talkId, $userId);
            $this->sendMessageMail($talkUser->user->email);
            $this->sendNotification($talkUser->user->id, $user->name, $message);

            return $createdMessage;
        });
    }

    private function sendMessageMail($to){
        $data = [
            'subject' => '新着のメッセージがあります',
            'message' => "新着のメッセージが届きました。\n確認してください。",
            'url' => config('frontend.url') . '/message',
        ];

        MailUtil::sendNotificationMail($to, $data);
    }

    private function sendNotification($userId, $sendUserName , $message){
        $tokens = NotificationToken::where('user_id', $userId)->get();

        if ($tokens->isEmpty()) {
            return;
        }

        foreach ($tokens as $token) {
            NotificationUtil::handle($token->token, $sendUserName, $message, '/message');
        }
    }


    public function setReadCount($talkId, $userId){
        $messages = $this->messageRepository->getMessagesByTalkIdAndUserId($talkId, $userId);
        $updateFlag = false;
        foreach ($messages as $message) {
            if($message->read_count == 0){
                $message->read_count = 1;
                $updateFlag = true;
            }
        }
        if($updateFlag){
            $this->messageRepository->updateMessages($messages);
        }
        return $updateFlag;
    }
}

