<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Utils\MailUtil;

class ContactController extends Controller
{
    //
    /**
     * 募集に紐づくコメント一覧を取得する
     * @param \Illuminate\Http\Request $request 募集情報のID
     * @return mixed|\Illuminate\Http\JsonResponse コメント一覧
     */
    public function index(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|max:255',
            'name' => 'nullable|string|max:20',
            'title' => 'required|string|max:30',
            'content' => 'required|string|max:255',
        ]);

        $message = $this->createMessage($data);
        $this->sendMessageMail('お問い合わせが完了しました',$message,$data['email']);
        $this->sendMessageMail('問い合わせがありました | やきゅまーれ',$message,config('mail.admin_mail'));

        return response()->json(null,201);
    }

    private function sendMessageMail($title ,$message, $to){
        $data = [
            'subject' => $title,
            'message' => $message,
            'url' => null,
        ];

        MailUtil::sendNotificationMail($to, $data);
    }

    private function createMessage($data){
        $message = "以下内容でお問い合わせが完了しました。\n \n■メールアドレス\n" .
            $data["email"] ."\n■お名前\n". $data["name"] .
            "\n■タイトル\n". $data["title"] ."\n■内容\n". $data["content"];
        return $message;

    }
}
