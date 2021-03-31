<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\VentaProducto;
use Faker\Generator as Faker;

$factory->define(VentaProducto::class, function (Faker $faker) {
    return [
        //
		'venta_id' => $faker->randomNumber(4),
		'producto_id' => $faker->randomNumber(4),
		'fecha_vencimiento' => null,
		'fecha_asignacion' => null,
		'fecha_consumicion' => null,
		'cantidad' => 1,
		'codigo_gift_card' => null,
		'precio' => 0,
    ];
});

$factory->state(VentaProducto::class, 'gift_card_valida', function (Faker $faker) {
    return [
        //
        'fecha_vencimiento' => \Illuminate\Support\Carbon::now()->addDays(env('VENCIMIENTO_GIFT_CARDS', 30))->toDate(),
		'codigo_gift_card' => \Str::random(8),
    ];
});