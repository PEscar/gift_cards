<?php

namespace App\Notifications;

use App\Models\Venta;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FailedGiftCardMailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $venta;
    protected $exception;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Venta $venta, \Exception $exception)
    {
        $this->venta = $venta;
        $this->exception = $exception;
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
        $mail = (new MailMessage)->line('Envío de GC fallido');

        $mail->line('El envío de GC de la venta: ' . $this->venta->id . ' ha fallado.');
        $mail->line('Error:');
        $mail->line($this->exception->getMessage());

        return $mail;
    }
}
