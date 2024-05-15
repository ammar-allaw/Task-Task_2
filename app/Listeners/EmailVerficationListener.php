<?php

namespace App\Listeners;

use App\Events\EmailVerficationEvent;
use App\Models\User;
use App\Notifications\EmailVerficationNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Str;


class EmailVerficationListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(EmailVerficationEvent $event): void
    {
        // $user = User::find($event->user_id);
        $user=User::where('id',$event->user_id)->first();
        if ($user) {
            $token = Str::random(6);
            $expiration = now()->addMinutes(3);

            // Update user properties
            $user->email_verification_otp = $token;
            $user->otp_verification_at = $expiration;
            $user->save();

            // Send email verification mail
            $user->notify(new EmailVerficationNotification($user->id,$token));
            // $user->notify(new EmailVerficationNotification($user->id, $token));
        }
    }
}
