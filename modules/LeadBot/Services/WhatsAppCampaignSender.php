<?php

namespace Modules\LeadBot\Services;

use Modules\Wpbox\Models\Message;
use Modules\Wpbox\Traits\Whatsapp;

class WhatsAppCampaignSender
{
    use Whatsapp;

    public function send(Message $message): void
    {
        $this->sendCampaignMessageToWhatsApp($message);
    }
}

