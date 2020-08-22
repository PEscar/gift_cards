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
            new RendererStyle(140, 0, null),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        // dd($notifiable);
        $mail = (new MailMessage)->line('Aquí tienes tu Gift Card !');

        foreach ($notifiable->venta_productos as $key => $ventaProduct) {

            // Si es gift card
            if ( $ventaProduct->tipo_producto == 1 )
            {
                $qr_code = new \Illuminate\Support\HtmlString($writer->writeString(route('giftcards.show', ['codigo' => $ventaProduct->codigo_gift_card])));

                $img_src = asset('img/giftcard.jpeg');
                
                $mail ->line(new \Illuminate\Support\HtmlString('<div class="thumbnail">
                      <img src="' . $img_src . '">
                      <div class="caption caption-producto">
                          <p>' . $ventaProduct->descripcion . '</p>
                      </div>
                      <div class="caption caption-vencimiento">
                          <p>' . strtoupper(date('d/M/Y', strtotime($ventaProduct->created_at))) . '</p>
                      </div>
                      <div class="caption caption-codigo">
                          <p>' . $ventaProduct->codigo_gift_card . '</p>
                      </div>
                      <div class="caption caption-qr">
                          <p>' . $qr_code . '</p>
                      </div>
                  </div>'));
            }

        }

        $mail->line(new \Illuminate\Support\HtmlString('&nbsp;'));
        $mail->line('Gracias por confiar en nosotros!');

        return $mail;
                    
    }
}
