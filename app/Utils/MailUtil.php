<?php
namespace App\Utils;

use Illuminate\Support\Facades\Mail;
use App\Mail\NotificationMail;

class MailUtil
{
    /**
     * メールを送信する
     *
     * @param string $to 送信先メールアドレス
     * @param array $data メール内容（subject, message など）
     * @return void
     */
    public static function sendNotificationMail(string $to, array $data)
    {
        Mail::to($to)->send(new NotificationMail($data));
    }
}
