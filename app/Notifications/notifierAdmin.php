<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class notifierAdmin extends Notification
{
    use Queueable,SerializesModels;

    protected  $tontine;
    protected $gestionCycles;
    protected $gagnant;

    protected $admin;
    

    public function __construct($tontine,$gestionCycles,$gagnant,$admin)
    {
        $this->admin =  $admin;

        $this->tontine =  $tontine;
        $this->gestionCycles =  $gestionCycles;
        $this->gagnant = $gagnant;
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
        /*
        Le tirage au sort de la tontine pour le cycle [numéro du cycle] a été effectué. Le participant [nom du participant] a été sélectionné comme gagnant.
         Veuillez prendre les mesures nécessaires pour annoncer publiquement le résultat du tirage au sort
        
        */
        return (new MailMessage)
                    ->line('Bonjour'. $this->admin->name_admin .'!')
                    ->line('Le tirage au sort de la tontine' .  $this->tontine->libelle . '!')
                    ->line('pour le cycle noméro' . $this->gestionCycles->nombre_de_cycle . 'a été effectué !')
                    ->line('le participant' . $this->gagnant->user->name . 'a gagné ')
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
