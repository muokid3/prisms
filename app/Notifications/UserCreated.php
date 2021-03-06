<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserCreated extends Notification implements ShouldQueue
{
    use Queueable;
    private $password;


    /**
     * Create a new notification instance.
     *
     * @param $password
     */
    public function __construct($password)
    {
        $this->password = $password;

    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
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
            ->subject('PRISMS Account Created')
            ->greeting('Hello '.$notifiable->first_name.'!')
            ->line('Your account on PRISMS has been created as a '.$notifiable->role->name.' for your organisation')
            ->line('Please use the following credentials to log in to your account. Make sure to change your password')
            ->line('Username/E-Mail: '.$notifiable->email)
            ->line('Password: '.$this->password)
            ->action('LOG IN NOW',url('login'))
            ->line('Feel free to contact our support team or your manager should you have any questions.')
            ->line('We look forward to working with you.');
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
