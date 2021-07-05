<?php


namespace App\Listeners;
use App\Events\InviteEvent;
use App\Mail\InviteMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;


class InviteListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(InviteEvent $event)
    {
        Mail::to($event->user->email)
            ->send(new InviteMail($event->user));
    }

}
