<?php

namespace App\Mail;

use App\Models\Users;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserPasswordMail extends Mailable
{
    use Queueable, SerializesModels;
    public $user;
    public $password;
    public $type;
    /**
     * Create a new message instance.
     */
    public function __construct(Users $user, $password, $type = 'new')
    {
        //
        $this->user = $user;
        $this->password = $password;
        $this->type = $type;
    }

    public function build()
    {
        $subject = $this->type === 'new'
            ? "Welcome to " . config('app.name') . "!"
            : "Your " . config('app.name') . " password has been reset";
        return $this->subject($subject)
            ->view('emails.user_password')
            ->with([
                'name' => $this->user->name,
                'email' => $this->user->email,
                'password' => $this->password,
                'type' => $this->type,
            ]);
    }
}
