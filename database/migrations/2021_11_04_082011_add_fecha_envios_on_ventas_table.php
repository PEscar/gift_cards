<?php

use App\Models\Venta;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFechaEnviosOnVentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ventas', function($table) {
            $table->datetime('fecha_envio')->after('fecha_error')->nullable()->default(null);
            $table->boolean('reenvio')->after('fecha_error')->default(false);
        });

        Venta::pagadas()->each(function ($item, $key) {
            $item->fecha_envio = $item->created_at->format('Y-m-d H:i:s');
            $item->save();
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
            $table->dropColumn('fecha_envio');
            $table->dropColumn('reenvio');
        });
    }
}
