<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Producto;
use Faker\Generator as Faker;

$factory->define(Producto::class, function (Faker $faker) {
    return [
        //
        'sku' => $faker->randomNumber(6),
		'nombre' => $faker->name,
		'descripcion' => null,
		'tipo_producto' => Producto::TIPO_GIFTCARD,
		'visible' => 1,
    ];
});