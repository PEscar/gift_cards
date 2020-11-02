<?php

namespace App\Jobs;

use App\Models\Venta;
use App\Models\VentaProducto;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StoreVentaMayorista implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    protected $venta;
    protected $ventaProductos;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 0;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($venta, $ventaProductos)
    {
        $this->venta = $venta;
        $this->ventaProductos = $ventaProductos;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->venta->save();

        foreach ($this->ventaProductos as $key => $ventaProducto) {

            $ventaProducto->generateGiftCardCode();
            $this->venta->venta_productos()->save($ventaProducto);
        }

        if ( $this->venta->pagada )
        {
            $this->venta->update(['pagada' => true]);

            if ( $this->venta->tieneGiftcards() )
            {
                $this->venta->entregarGiftcards();
            }
        }
    }
}
