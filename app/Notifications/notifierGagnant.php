<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class notifierGagnant extends Notification
{
    use Queueable,SerializesModels;

    /**
     * Create a new notification instance.
     */

    protected $gagnant;

    protected  $montantTotal;
    public function __construct($gagnant,$montantTotal)
    {
        $this->gagnant = $gagnant;
        $this->montantTotal=$montantTotal;
    }
    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
       

        return (new MailMessage)
        ->line('Félicitations ' . $this->gagnant->user->name . '!')
        ->line(' vous êtes le/la gagnant(e) de la tontine ')
        ->line(' vous avez gagné(e) '. $this->montantTotal .' fcfa')
        ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
