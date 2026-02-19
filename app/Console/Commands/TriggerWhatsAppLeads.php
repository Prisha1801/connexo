<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TriggerWhatsAppLeads extends Command
{
    protected $signature = 'whatsapp:trigger';

    protected $description = 'Trigger WhatsApp lead sending via internal URL';

    public function handle()
    {
        $secret = env('CRON_SECRET_KEY');

        $response = Http::get(url("/trigger-leads?key={$secret}"));

        $this->info("Trigger response: " . $response->body());
    }
}
