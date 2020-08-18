<?php

namespace App\Notifications;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GiftCardMailNotification extends Notification
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
        $renderer = new ImageRenderer(
            new RendererStyle(400),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        // dd($notifiable);
        $mail = (new MailMessage)->line('Aquí tienes tu Gift Card !');

        foreach ($notifiable->venta_productos as $key => $ventaProduct) {

            // Si es gift card
            if ( $ventaProduct->tipo_producto == 1 )
            {
                $mail->line(route('giftcards.show', ['codigo' => $ventaProduct->codigo_gift_card]));

                $mail ->line(new \Illuminate\Support\HtmlString($writer->writeString(route('giftcards.show', ['codigo' => $ventaProduct->codigo_gift_card]))));

                $mail ->line(new \Illuminate\Support\HtmlString($writer->writeString(route('giftcards.show', ['codigo' => $ventaProduct->codigo_gift_card]))));
            }

        }

        $mail->line('Gracias por confiar en nosotros!');

        return $mail;
                    
    }
}
