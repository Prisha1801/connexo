<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeadsFetched implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $newLeadsCount;

    public function __construct($message, $newLeadsCount = 0)
    {
        $this->message = $message;
        $this->newLeadsCount = $newLeadsCount;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('fb-leads');
    }

    public function broadcastAs(): string
    {
        return 'leads.fetched';
    }
}
