<?php

namespace App\Jobs;

use App\Models\Venta;
use App\Notifications\GiftCardZipMailNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendGiftCardZipMailNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $venta;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 480;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Venta $venta)
    {
        $this->venta = $venta;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        return $this->venta->notify(new GiftCardZipMailNotification($this->venta));
    }
}
