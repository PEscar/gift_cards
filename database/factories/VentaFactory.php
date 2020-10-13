<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Venta;
use Faker\Generator as Faker;

$factory->define(Venta::class, function (Faker $faker) {
    return [
        //
        'external_id' => $faker->randomNumber(4),
		'date' => $faker->date(),
		'source_id' => random_int(0, 1),
		'pagada' => Venta::PAGADA_NO,
		'client_email' => $faker->email,
    ];
});

$factory->state(Venta::class, 'pagada', function (Faker $faker) {
    return [
		'pagada' => Venta::PAGADA_SI,
    ];
});