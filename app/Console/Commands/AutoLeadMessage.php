<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FacebookLeads;
use App\Models\Automation;
use Modules\Wpbox\Models\Message;
use Modules\Wpbox\Models\Contact;
use Carbon\Carbon;

class AutoLeadMessage extends Command
{
    protected $signature = 'automation:send-leads-to-whatsapp';
    protected $description = 'Send WhatsApp messages to users from Facebook leads based on automations';

    public function handle()
    {
        $sentCount = 0;

        // 🧠 Step 1: Get all configured ad_ids from automation
        $configuredAdIds = Automation::pluck('ad_id')->filter()->unique()->toArray();

        if (empty($configuredAdIds)) {
            $this->warn('❌ No automations configured. Nothing to send.');
            return;
        }

        // 🔍 Step 2: Fetch leads for those ad_ids which are not yet sent
        $leads = FacebookLeads::whereNull('whatsapp_sent_at')
                    ->whereIn('ad_id', $configuredAdIds)
                    ->take(50)
                    ->get();

        if ($leads->isEmpty()) {
            $this->info('ℹ️ No new leads to send.');
        }

        foreach ($leads as $lead) {
            $automation = Automation::with('template')->where('ad_id', $lead->ad_id)->first();

            if (!$automation || !$automation->template) {
                $this->warn("❌ Skipping: Invalid automation or missing template for ad_id {$lead->ad_id}");
                continue;
            }

            if (empty($lead->phone_number)) {
                $this->warn("❌ Skipping: No phone number for lead ID {$lead->id}");
                continue;
            }

            // 🧾 Ensure contact exists
            $contact = Contact::firstOrCreate(
                ['phone' => $lead->phone_number],
                ['name' => $lead->full_name ?? 'Facebook Lead', 'subscribed' => 1]
            );

            // 📨 Queue message
            Message::create([
                'phone' => $lead->phone_number,
                'template_id' => $automation->template_id,
                'campaign_id' => $automation->campaign_id ?? null,
                'contact_id' => $contact->id,
                'status' => 0,
                'scchuduled_at' => Carbon::now(),
                'variables' => json_encode([]), // Optional: Map vars here
                'message' => null,
                'type' => 'template',
            ]);

            // ✅ Mark as sent
            $lead->update(['whatsapp_sent_at' => Carbon::now()]);
            $sentCount++;

            $this->info("✅ Queued message to {$lead->phone_number}");
        }

        $this->line("--------------------------------------------------");
        $this->info("✅ Total WhatsApp messages queued: {$sentCount}");
        $this->info("🏁 Finished processing leads.");
    }
}
