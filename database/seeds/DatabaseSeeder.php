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
        $user = factory(User::class)->create(['email' => 'pedroscarselletta@gmail.com']);

        // Role Admin & Assign Role
        Role::create(['name' => 'Admin']);
        $user->assignRole('Admin');

        // Venta de TEST.
        $venta = factory(Venta::class)->create(['comentario' => 'Venta TEST creada desde seeder.']);
        $venta->venta_productos()->save(factory(VentaProducto::class)->make());

        Sede::create(['nombre' => 'Madero1']);
        Sede::create(['nombre' => 'Madero2']);
        Sede::create(['nombre' => 'Madero3']);
        Sede::create(['nombre' => 'Madero5']);
        Sede::create(['nombre' => 'Libertador']);
        Sede::create(['nombre' => 'Dolce']);
        Sede::create(['nombre' => 'Riobamba']);
        Sede::create(['nombre' => 'Botanico']);
        Sede::create(['nombre' => 'Recoleta']);
        Sede::create(['nombre' => 'San Isidro']);
        Sede::create(['nombre' => 'Pilar']);

        $user->sedes()->sync(Sede::all()->pluck('id')->toArray());
    }
}
