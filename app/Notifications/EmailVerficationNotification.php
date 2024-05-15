<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerficationNotification extends Notification
{
    use Queueable;

    public $user_id;
    public $token;


    public function __construct($user_id, $token)
    {
        $this->user_id = $user_id;
        $this->token = $token;
    }

    public function via($notifiable):array
    {
        return ['mail'];
    }

    public function toMail($notifiable):MailMessage
    {
        $token=$this->token;
        return (new MailMessage)
            ->line('This is the verification code.')
            ->line('Your OTP for registration is: ' . $token);
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}