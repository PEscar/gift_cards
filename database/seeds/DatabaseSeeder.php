<?php

use App\Models\Sede;
use App\Models\Venta;
use App\Models\VentaProducto;
use App\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // User Pedro
        $user = factory(User::class)->create(['email' => 'pedroscarselletta@gmail.com', 'name' => 'Pedro Scarselletta']);

        // Role Admin & Assign Role
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Nivel1']);
        $user->setNivel('Admin');

        // Venta de TEST.
        $venta = factory(Venta::class)->state('pagada')->create(['comentario' => 'Venta TEST creada desde seeder.']);
        $venta->venta_productos()->save(factory(VentaProducto::class)->state('gift_card_valida')->make());

        Sede::create(['nombre' => 'Madero 1']);
        Sede::create(['nombre' => 'Madero 2']);
        Sede::create(['nombre' => 'Madero 3']);
        Sede::create(['nombre' => 'Madero 5']);
        Sede::create(['nombre' => 'Libertador']);
        Sede::create(['nombre' => 'Dolce']);
        Sede::create(['nombre' => 'Riobamba']);
        Sede::create(['nombre' => 'Botanico']);
        Sede::create(['nombre' => 'Recoleta']);
        Sede::create(['nombre' => 'San Isidro']);
        Sede::create(['nombre' => 'Pilar']);

        $user->setSedes(Sede::all()->pluck('id')->toArray());
    }
}
