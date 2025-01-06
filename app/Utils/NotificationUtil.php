<?php
namespace App\Utils;

use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client;

class NotificationUtil
{

    private static function getAccessToken()
    {
        $keyFilePath = storage_path('app/firebase/yakyumare-c100ff971738.json'); // サービスアカウントのJSONファイルのパス
        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];

        $credentials = new ServiceAccountCredentials($scopes, $keyFilePath);
        return $credentials->fetchAuthToken()['access_token'];
    }


    /**
     * FCMを使用して通知を送信する
     *
     * @param string $token 受信者のトークン
     * @param string $content 通知の内容
     * @param string $link 通知をクリックしたときのリンク
     * @return void
     */
    public static function handle($token, $title, $content, $link)
    {
        $client = new Client();
        $fcm_endpoint = config('frontend.fcmSendUrl'); // プロジェクトIDを指定

        $accessToken = self::getAccessToken();

        $headers = [
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json'
        ];

        $fields = [
            'message' => [
                'token' => $token,
                'data' => [
                    'title' => $title,
                    'body' => $content,
                    'click_action' => config('frontend.url') . $link,
                ],
            ]
        ];

        $client->post($fcm_endpoint, [
            'headers' => $headers,
            'json' => $fields
        ]);
    }
}
