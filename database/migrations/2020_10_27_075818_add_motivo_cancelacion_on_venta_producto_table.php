<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMotivoCancelacionOnVentaProductoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('venta_producto', function($table) {
            $table->string('motivo_cancelacion')->after('producto_id')->nullable();
            $table->date('fecha_cancelacion')->after('motivo_cancelacion')->nullable();
            $table->foreignId('usuario_cancelacion')->after('fecha_cancelacion')->nullable();

            $table->foreign('usuario_cancelacion')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('venta_producto', function($table) {
            $table->dropColumn('motivo_cancelacion');
            $table->date('fecha_cancelacion')->after('motivo_cancelacion')->nullable();
        });
    }
}
