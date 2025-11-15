<?php

namespace App\Listeners;

use App\Models\Notification;
use App\Models\NotificationSetting;
use App\Events\TicketUpdated;
use App\Models\Users;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class SendTicketNotifications
{
    public function handle(TicketUpdated $event)
    {
        $settings = NotificationSetting::first(); 
        $user = $event->ticket->user;

        if ($settings->email && $user->notify_email) {
            $this->email($event);
        }

        if ($settings->whatsapp && $user->notify_whatsapp) {
            $this->whatsapp($event);
        }

        if ($settings->desktop && $user->notify_desktop) {
            $this->createDatabaseNotification($event, $user);
        }

        if ($event->action == 'assigned') {
            $this->notifyAssignedUsers($event);
        }
    }

    private function email($event)
    {
        Mail::raw(
            "Ticket #{$event->ticket->id} telah {$event->action}",
            fn($m) => $m->to($event->ticket->user->email)
                ->subject("Kawano Ticketing: {$event->action}")
        );
    }

    private function whatsapp($event)
    {
        Http::withHeaders([
            'Authorization' => env('FONNTE_TOKEN'),
        ])->post('https://api.fonnte.com/send', [
            'target' => $event->ticket->user->phone,
            'message' => "Ticket #{$event->ticket->id} telah {$event->action}",
        ]);
    }

    private function createDatabaseNotification($event, $targetUser)
    {
        Notification::create([
            'id' => \Str::uuid(),
            'user_id' => $targetUser->id,
            'title' => "Ticket #{$event->ticket->id} telah {$event->action}",
            'message' => "Ticket #{$event->ticket->id} telah diupdate: {$event->action}",
            'type' => 'ticket_updated',
            'ticket_id' => $event->ticket->id,
            'is_read' => false,
        ]);
    }

    private function notifyAssignedUsers($event)
    {
        $handler = $event->ticket->assigned_to;
        $admins = Users::whereIn('role_id', ['admin', 'superadmin'])->get();

        // Kirim notifikasi ke handler dan admin
        foreach ([$handler, ...$admins] as $user) {
            $this->createDatabaseNotification($event, $user);
        }
    }
}
