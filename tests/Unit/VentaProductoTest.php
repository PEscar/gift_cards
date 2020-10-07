<?php

namespace Tests\Unit;

use App\Models\VentaProducto;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class VentaProductoTest extends TestCase
{
	use DatabaseTransactions;

    /**
     * Test que el accesor retorna estado valido correctamente
     *
     * @return void
     */
    public function testEstadoDebeDevolverValida()
    {
        $gc = factory(VentaProducto::class)->state('gift_card_valida')->make();

        $this->assertEquals($gc->estado, VentaProducto::ESTADO_VALIDA);
    }

    /**
     * Test que el accesor retorna estado valido vencida
     *
     * @return void
     */
    public function testEstadoDebeDevolverVencida()
    {
        $gc = factory(VentaProducto::class)->state('gift_card_valida')->make(['fecha_vencimiento' => \Illuminate\Support\Carbon::now()->subDays(1)->toDate()]);

        $this->assertEquals($gc->estado, VentaProducto::ESTADO_VENCIDA);
    }

    /**
     * Test que el accesor retorna estado valido asignada
     *
     * @return void
     */
    public function testEstadoDebeDevolverAsignada()
    {
        $gc = factory(VentaProducto::class)->state('gift_card_valida')->make(['fecha_asignacion' => \Illuminate\Support\Carbon::now()->addDays(1)->toDate(), 'asignacion_id' => 1, 'nro_mesa' => 123123, 'sede_id' => 1]);

        $this->assertEquals($gc->estado, VentaProducto::ESTADO_ASIGNADA);
    }

    /**
     * Test que se genweran codigos para giftcards unicos correctamente
     *
     * @return void
     */
    public function testGenerateGiftCardCode()
    {
        $gc = factory(VentaProducto::class)->state('gift_card_valida')->make(['codigo_gift_card' => null]);

        $this->assertNull($gc->codigo_gift_card);

        $gc->generateGiftCardCode();

        $this->assertNotNull($gc->codigo_gift_card);
        $this->assertDatabaseMissing((new VentaProducto)->getTable(), [
            'codigo_gift_card' => $gc->codigo_gift_card
        ]);
    }

    /**
     * Test function asignar
     *
     * @return void
     */
    public function testAsignar()
    {
        $gc = factory(VentaProducto::class)->state('gift_card_valida')->make();
        $gc->asignar(1, 'mesa123', 45);

        $this->assertEquals($gc->fecha_asignacion->toDateString(), \Illuminate\Support\Carbon::now()->toDateString());
        $this->assertEquals($gc->asignacion_id, 45);
        $this->assertEquals($gc->sede_id, 1);
        $this->assertEquals($gc->nro_mesa, 'mesa123');
    }
}
