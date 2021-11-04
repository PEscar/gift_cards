<?php

namespace App\Providers;

use App\Models\Venta;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        Queue::after(function (JobProcessed $event) {

            // Si no ha fallado y es un envio de GC
            if ( !$event->job->hasFailed() && in_array($event->job->payload()['displayName'], ['App\Notifications\GiftCardMailNotification', 'App\Notifications\GiftCardZipMailNotification']) )
            {
                $commands = json_decode($event->job->getRawBody())->data->command;
                $unserialized_commands = unserialize($commands);
                $id_venta = $unserialized_commands->notifiables[0]->id;

                \Log::info('se ha enviado una gc correctamentaae: !' . $id_venta);
                $venta = Venta::findOrFail($id_venta);
                $venta->fecha_envio = date('Y-m-d H:i:s');
                $venta->save();
            }
        });
    }
}
