<?php

return [
    'url' => env('FRONT_URL', 'http://localhost:5173'), // デフォルト値を設定
    'fcmSendUrl' => env('FCM_SEND_URL','https://fcm.googleapis.com/v1/projects/yakyumare/messages:send'),
];
