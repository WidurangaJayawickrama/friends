<?php


namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;


class InviteMail extends Mailable
{
    use Queueable;
    use SerializesModels;


    public $user;
    public $url;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->url = URL::temporarySignedRoute(
            'friends.accept', now()->addMinutes(30), ['id' =>Auth::user()->id]
        );
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Friend Invitation')
            ->markdown('emails.invitation');
    }


}
