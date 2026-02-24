<?php

// namespace App\Listeners;

// use App\Events\WhatsAppTriggerRequested;
// use App\Http\Controllers\AutomationController;
// use Illuminate\Http\Request;

// class TriggerWhatsAppOnEvent
// {
//     /**
//      * Handle the event.
//      */
//     public function handle(WhatsAppTriggerRequested $event): void
//     {
//         // Dynamically call controller
//         $controller = app(AutomationController::class);

//         // Simulated secure request
//         $request = Request::create('/trigger-leads', 'GET', [
//             'key' => $event->secret,
//         ]);

//         // Call WhatsApp logic
//         $controller->triggerLeadWhatsApp($request);
//     }
// }



namespace App\Listeners;

use App\Events\WhatsAppTriggerRequested;
use App\Http\Controllers\AutomationController;
use Illuminate\Http\Request;

class TriggerWhatsAppOnEvent
{
    public function handle(WhatsAppTriggerRequested $event): void
    {
        $controller = app(AutomationController::class);

        $request = Request::create('/trigger-leads', 'GET', [
            'key' => $event->secret,
        ]);

        $controller->triggerLeadWhatsApp($request);
    }
}
