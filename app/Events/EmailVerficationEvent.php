<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmailVerficationEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $user_id;
    
    public function __construct($user_id)
    {
        $this->user_id=$user_id;
    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}


// public function handle(EmailVerficationEvent $event)
// {
//     $user = User::find($event->user_id);

//     if ($user) {
//         $token = Str::random(6);
//         $expiration = now()->addMinutes(3);

//         $user->email_verification_otp = $token;
//         $user->otp_verification_at = $expiration;
//         $user->save();

//         $user->notify(new EmailVerficationNotification($event->user_id, $token));
//     }

// }