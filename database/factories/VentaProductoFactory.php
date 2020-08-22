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
		'tipo_producto' => random_int(0, 1),
		'fecha_vencimiento' => $faker->date(),
		'cantidad' => 1,
		'codigo_gift_card' => $faker->unique()->isbn10,
    ];
});
