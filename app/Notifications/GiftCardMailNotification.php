<?php

namespace App\Notifications;

use App\User;
use App\Models\Venta;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GiftCardMailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $venta;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 480;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Venta $venta)
    {
        $this->venta = $venta;
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
        $pdfs = $notifiable->generatePdfs();

        if ( count($pdfs) > 1 )
        {
            $mail = (new MailMessage)->line('Aquí tienes tus Gift Card !');
        }
        else
        {
            $mail = (new MailMessage)->line('Aquí tienes tu Gift Card !');
        }

        foreach ($pdfs as $key => $elem) {

            $mail->attachData($elem['pdf']->output(), $elem['pdf_filename']);
        }

        $mail->line('Gracias por confiar en nosotros!');

        $mail->bcc(['pedroscarselletta@gmail.com', 'tienda.copy@laparolaccia.com']);

        return $mail;
                    
    }

    public function failed(\Exception $e)
    {
        \Log::info('failed GiftCardMailNotification: ' . $this->venta->id);
        $this->venta->fecha_error = date('Y-m-d H:i:s');
        $this->venta->error_envio = $e->getMessage();
        $this->venta->save();
        // User::find(1)->notify(new FailedGiftCardMailNotification($this->venta, $e));
    }
}
