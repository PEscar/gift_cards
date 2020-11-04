<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUsuarioEdicionOnVentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ventas', function($table) {
            $table->foreignId('usuario_edicion_id')->after('tipo_notificacion')->nullable();
            $table->foreign('usuario_edicion_id')->references('id')->on('users');
            $table->date('fecha_ultima_edicion')->nullable();
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
            $table->dropColumn('usuario_edicion_id');
            $table->dropColumn('fecha_ultima_edicion');
        });
    }
}
