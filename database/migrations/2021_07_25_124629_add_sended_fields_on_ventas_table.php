<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSendedFieldsOnVentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ventas', function($table) {
            $table->text('error_envio')->after('fecha_ultima_edicion')->nullable();
            $table->datetime('fecha_error')->after('fecha_ultima_edicion')->nullable()->default(null);
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
            $table->dropColumn('error_envio');
            $table->dropColumn('fecha_error');
        });
    }
}
