<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVentaProductoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('venta_producto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id');
            $table->string('sku')->comment('codigo de producto');
            $table->string('descripcion')->nullable()->comment('descripción del producto');
            $table->smallInteger('tipo_producto')->comment('0: prod. normal. 1: gift card');
            $table->date('fecha_vencimiento')->nullable()->comment('!= null para gift cards');
            $table->date('fecha_canje')->nullable()->comment('!= null para gift cards canjeadas');
            $table->foreignId('entrega_id')->nullable()->comment('id de usuario que realizo la entrega');
            $table->smallInteger('cantidad');
            $table->string('codigo_gift_card')->nullable();

            $table->timestamps();

            $table->foreign('venta_id')->references('id')->on('ventas');
            $table->foreign('entrega_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('venta_producto');
    }
}
