<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductoIdOnVentaProductoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('venta_producto', function($table) {
            $table->foreignId('producto_id')->after('venta_id')->nullable();
            $table->foreign('producto_id')->references('id')->on('productos');

            $table->dropColumn('sku');
            $table->dropColumn('descripcion');
            $table->dropColumn('tipo_producto');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ventas', function($table) {
            $table->dropColumn('producto_id');
        });
    }
}
