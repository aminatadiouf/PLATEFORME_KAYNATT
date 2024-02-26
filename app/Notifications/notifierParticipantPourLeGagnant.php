<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class notifierParticipantPourLeGagnant extends Notification
{
    use Queueable,SerializesModels;

    protected  $tontine;
    protected $gestionCycles;
    protected $gagnant;
    protected  $montantTotal;


    

    public function __construct($tontine,$gestionCycles,$gagnant,$montantTotal)
    {
        
        $this->tontine =  $tontine;
        $this->gestionCycles =  $gestionCycles;
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
        /*
        Le tirage au sort de la tontine pour le cycle [numéro du cycle] a été effectué. Le participant [nom du participant] a été sélectionné comme gagnant.
         Veuillez prendre les mesures nécessaires pour annoncer publiquement le résultat du tirage au sort
        
        */
        return (new MailMessage)
                    ->line('Le tirage au sort de la tontine '. $this->tontine->libelle . '!')
                    ->line('pour le cycle noméro '. $this->gestionCycles->nombre_de_cycle .' a été effectué .!')
                    ->line('le/la participant(e) '. $this->gagnant->user->name . ' est le gagnant(e). ')
                    ->line('il/elle a gagné(e) '. $this->montantTotal .' fcfa')

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
