<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\AutomationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TriggerWhatsApp extends Command
{
    protected $signature = 'trigger:whatsapp';
    protected $description = 'Trigger WhatsApp automation via scheduler';

    public function handle()
    {
        try {
            $controller = new AutomationController();
            $controller->triggerLeadWhatsApp(app(Request::class));
            Log::info('✅ WhatsApp automation triggered successfully at ' . now());
        } catch (\Exception $e) {
            Log::error('❌ Error triggering WhatsApp automation: ' . $e->getMessage());
        }
    }
}
