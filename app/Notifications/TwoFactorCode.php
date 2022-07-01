<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TwoFactorCode extends Notification
{
    use Queueable;
    private $type;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($type)
    {
        //
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        if ($this->type == 'email'){
            return ['mail'];
        }
        return ['sms'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Email verification')
                    ->greeting('Confirm Your Email Address')
                    ->line('Welcome to MULA FINANCE!')
                    ->line("Your account is almost ready, we just need to confirm that it's really you.
                    You can confirm your account by entering the code in the app")
                    ->line('The code will expire in 10 minutes')
                    ->line($notifiable->two_factor_code);

    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
