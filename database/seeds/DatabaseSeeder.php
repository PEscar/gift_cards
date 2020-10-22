<?php

use App\Models\Producto;
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

        // Seed productos
        factory(Producto::class)->createMany([
            ['sku' => "11247", 'nombre' => 'Gift Card Coliseo x 1'],
            ['sku' => "11255", 'nombre' => 'Gift Card Coliseo x 2'],
            ['sku' => "11256", 'nombre' => 'Gift Card Coliseo x 3'],
            ['sku' => "11257", 'nombre' => 'Gift Card Coliseo x 4'],
            ['sku' => "11251", 'nombre' => 'Gift Card Menu Aniversario x 1'],
            ['sku' => "11252", 'nombre' => 'Gift Card Menu Aniversario x 2'],
            ['sku' => "11253", 'nombre' => 'Gift Card Menu Aniversario x 3'],
            ['sku' => "11254", 'nombre' => 'Gift Card Menu Aniversario x 4']
        ]);

        $producto = Producto::all()->random();

        // Venta de TEST.
        $venta = factory(Venta::class)->state('pagada')->create(['comentario' => 'Venta TEST creada desde seeder.']);
        $venta->venta_productos()->save(factory(VentaProducto::class)->state('gift_card_valida')->make(['producto_id' => $producto->id]));

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
