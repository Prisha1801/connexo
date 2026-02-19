<?php
// namespace App\Events;

// use Illuminate\Broadcasting\Channel;
// use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
// use Illuminate\Queue\SerializesModels;

// class WhatsAppTriggerRequested implements ShouldBroadcast
// {
//     use SerializesModels;

//     public string $secret;

//     public function __construct(string $secret)
//     {
//         $this->secret = $secret;
//     }

//     public function broadcastOn(): Channel
//     {
//         return new Channel('whatsapp-channel');
//     }

//     public function broadcastAs(): string
//     {
//         return 'trigger.whatsapp.send';
//     }
// }


namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class WhatsAppTriggerRequested implements ShouldBroadcast
{
    use SerializesModels;

    public string $secret;

    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('whatsapp-channel');
    }

    public function broadcastAs(): string
    {
        return 'trigger.whatsapp.send';
    }
}



