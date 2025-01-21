<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\ApplicationRepository;
use App\Repositories\MessageRepository;
use App\Repositories\RecruitmentRepository;
use App\Repositories\TalkRepository;
use App\Repositories\TalkUserRepository;
use App\Utils\MailUtil;
use Illuminate\Support\Facades\DB;
use App\Utils\NotificationUtil;
use App\Models\NotificationToken;

/**
 * 応募情報に関する処理を実行するサービス
 * @version 1.0.0
 * @author S.Yamamoto
 */
class ApplicationService
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
     * 募集,トーク情報の登録を実行する
     * @param mixed $data
     * @return mixed
     */
    public function createApplication($data)
    {
        return DB::transaction(function () use ($data){
            //応募処理
            $application = $this->applicationRepository->createApplication($data);
            $talk = $this->talkRepository->createTalk($application->id);
            $this->talkUserRepository->createTalkUser($talk->id,$data['recruited_user_id']);
            $this->talkUserRepository->createTalkUser($talk->id,$data['apply_user_id']);

            //システムメッセージの作成
            $recruitment = $this->recruitmentRepository->getRecruitmentById($data['recruitment_id']);
            $message = "「" . $recruitment->title . "」\n募集に応募がありました。";
            $this->messageRepository->createMessage($talk->id, "system" , $message);

            // 応募のメール通知
            $user = User::find($data['recruited_user_id']);
            $subject = '新規の応募がありました';
            $title = $recruitment->title;
            $message = "「" . $title . "」\n新規の応募がありました。\n" . "確認してください。";
            $url = config('frontend.url') . '/message';

            $this->sendApplyMail($user->email, $subject, $message, $url);
            $this->sendNotification($user->id, $title, $subject);
        });
    }

    /**
     * 応募の承認・否認を実行する
     * @param mixed $data
     * @return mixed
     */
    public function updateApproval($data)
    {
        return DB::transaction(function () use ($data): void{
            $this->applicationRepository->updateApproval($data['id'],$data['approval']);
            $approval = $data['approval'] ? '承認' : '否認';

            $application = $this->applicationRepository->getApplication($data['id']);

            $subject = '応募が' . $approval . 'されました';
            $title = $application->recruitment->title;
            $message = "「" . $title . "」\n応募が". $approval ."されました。\n" . "確認してください。";
            $url = config('frontend.url') . '/message';

            $this->sendApplyMail($application->apply_user->email, $subject, $message, $url);
            $this->sendNotification($application->apply_user_id, $title, $subject);
        });
    }

    private function sendApplyMail($to, $subject, $message, $url){
        $data = [
            'subject' => $subject,
            'message' => $message,
            'url' => $url,
        ];

        MailUtil::sendNotificationMail($to, $data);
    }

    private function sendNotification($userId, $title , $content){
        $tokens = NotificationToken::where('user_id', $userId)->get();

        if ($tokens->isEmpty()) {
            return;
        }

        foreach ($tokens as $token) {
            NotificationUtil::handle($token->token, $title, $content, '/message');
        }
    }
}

