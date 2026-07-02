<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $this->call([
            RolSeeder::class,
            PermisoSeeder::class,
            RolPermisoSeeder::class,
            SucursalSeeder::class,
            TipoProductoSeeder::class,
            MarcaProductoSeeder::class,
            TipoMovimientoSeeder::class,
            EstadoSeeder::class,
            UsuarioSeeder::class,
        ]);

        Schema::enableForeignKeyConstraints();
    }
}
