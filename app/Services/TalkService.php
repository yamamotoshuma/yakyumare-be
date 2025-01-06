<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\ApplicationRepository;
use App\Repositories\MessageRepository;
use App\Repositories\RecruitmentRepository;
use App\Repositories\TalkRepository;
use App\Repositories\TalkUserRepository;
use App\Utils\MailUtil;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PgSql\Lob;

/**
 * トーク情報に関する処理を実行するサービス
 * @version 1.0.0
 * @author S.Yamamoto
 */
class TalkService
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
     * トーク情報一覧取得
     * @param array $filters
     * @param mixed $perPage
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public function getAllTalks()
    {
        $user = Auth::user();

        if (!$user) {
            throw new ModelNotFoundException('ユーザーが見つかりません。');
        }

        $userId = $user->id;

        // talksをEager Loadingして取得
        $talks = $this->talkRepository->getTalksByUserId($userId);

        return $talks->map(function ($talk) use ($userId) {
            $unreadCount = $talk->unread_count ?? 0; // 未読メッセージ数
            $otherUser = $talk->users->firstWhere('id', '!=', $userId);
            $lastMessage = optional($talk->messages->first())->message;

            return [
                'id' => $talk->id,
                'applicationId' => $talk->application_id,
                'application' => $talk->application,
                'lastMessage' => $lastMessage,
                'user' => $otherUser,
                'unreadCount' => $unreadCount,
            ];
        });
    }
    private function sendApplyMail($to){
        $data = [
            'subject' => '応募がありました',
            'message' => '新しい応募が届きました。確認してください。',
        ];

        MailUtil::sendNotificationMail($to, $data);
    }
}

