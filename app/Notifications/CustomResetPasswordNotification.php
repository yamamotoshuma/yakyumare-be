<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;

class CustomResetPasswordNotification extends ResetPasswordNotification
{
    public function toMail($notifiable)
    {
        $url = url(config('frontend.url') . '/user/resetPassword?token=' . $this->token . '&email=' . urlencode($notifiable->email));

        return (new MailMessage)
                    ->subject('やきゅまーれ | パスワードリセットについて')
                    ->view('emails.reset', ['url' => $url]);
    }
}
