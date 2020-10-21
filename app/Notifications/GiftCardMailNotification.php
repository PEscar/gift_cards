<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GiftCardMailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        $mail = (new MailMessage)->line('Aquí tienes tu Gift Card !');

        $pdfs = $notifiable->generatePdfs();

        foreach ($pdfs as $key => $elem) {

            $mail->attachData($elem['pdf']->output(), $elem['pdf_filename']);
        }

        $mail->line('Gracias por confiar en nosotros!');

        $mail->bcc(['pedroscarselletta@gmail.com', 'tienda.copy@laparolaccia.com']);

        return $mail;
                    
    }
}
