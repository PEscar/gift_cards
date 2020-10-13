<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\VentaProducto;
use Faker\Generator as Faker;

$factory->define(VentaProducto::class, function (Faker $faker) {
    return [
        //
		'venta_id' => $faker->randomNumber(4),
		'sku' => $faker->ean8,
		'descripcion' => $faker->text(10),
		'tipo_producto' => VentaProducto::TIPO_NORMAL,
		'fecha_vencimiento' => null,
		'fecha_asignacion' => null,
		'fecha_consumicion' => null,
		'cantidad' => 1,
		'codigo_gift_card' => null,
    ];
});

$factory->state(VentaProducto::class, 'gift_card_valida', function (Faker $faker) {
    return [
        //
        'tipo_producto' => VentaProducto::TIPO_GIFTCARD,
        'fecha_vencimiento' => \Illuminate\Support\Carbon::now()->addDays(env('VENCIMIENTO_GIFT_CARDS', 30))->toDate(),
		'codigo_gift_card' => \Str::random(8),
    ];
});