<?php

use App\Models\Venta;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipoNotificacionOnVentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ventas', function($table) {
            $table->smallInteger('tipo_notificacion')->after('comentario')->default(Venta::TIPO_NOTIFICACION_PDF_ATTACH);
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
            $table->dropColumn('tipo_notificacion');
        });
    }
}
