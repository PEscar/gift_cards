<?php

namespace App\Console;

use App\Models\Venta;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        Venta::importOrderFromTiendaNubeByDate();

        $ventas = Venta::envioPendiente()->notTryingSend()->get();
        \Log::info($ventas->count() . ' GC pendientes de envio');
        $ventas->each(function ($item, $key) {
            $item->entregarGiftcards(true);
            \Log::info('Venta con ID/TN: ' . $item->id . '/' . $item->external_id . ' resend');
            $item->save(); // Guarda en BD reenvio = true
        });
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
