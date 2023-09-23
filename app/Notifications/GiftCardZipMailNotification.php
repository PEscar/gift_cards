<?php

namespace App\Notifications;

use App\Models\Venta;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class GiftCardZipMailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $venta;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Venta $venta)
    {
        $venta->trying_send = true;
        $venta->save();
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
        $mail = (new MailMessage)->line('Aquí tienes tu Gift Card !');

        $url = \URL::signedRoute('voucher.download', ['id' => $notifiable->id]);

        $mail->line(new HtmlString('Pincha <a href="' . $url . '">acá</a> para descargar tu voucher.'));

        $mail->line('O copia y pega la siguiente dirección en tu navegador:');

        $mail->line($url);

        $mail->line('Gracias por confiar en nosotros!');

        $mail->bcc(['pedroscarselletta@gmail.com', 'tienda.copy@laparolaccia.com']);

        $salutation = $this->venta->firma == 1 ? 'La Parolaccia' : 'LA BISTECCA';

        $mail->salutation($salutation);

        $mail->markdown('notifications::email', ['firma' => $this->venta->firma, 'logo_url' => $this->venta->firma == 1 ? config('app.url') : 'http://laparolaccia-online.com/gift_cards_test/public/img/logo_bistecca.png']);

        return $mail;
    }

    public function failed(\Exception $e)
    {
        \Log::info('failed GiftCardZipMailNotification: ' . $this->venta->id);
        // User::find(1)->notify(new FailedGiftCardMailNotification($this->venta, $e));
        $this->venta->fecha_error = date('Y-m-d H:i:s');
        $this->venta->error_envio = $e->getMessage();
        $this->venta->trying_send = false;
        $this->venta->save();
    }
}
