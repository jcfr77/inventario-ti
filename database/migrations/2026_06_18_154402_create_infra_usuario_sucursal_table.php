<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('
            CREATE TABLE infra_usuario_sucursal (
                ID_USUARIO  INT(11) NOT NULL,
                ID_SUCURSAL INT(11) NOT NULL,
                PRIMARY KEY (ID_USUARIO, ID_SUCURSAL),
                CONSTRAINT fk_us_usuario  FOREIGN KEY (ID_USUARIO)  REFERENCES infra_usuario(ID_USUARIO)  ON DELETE CASCADE,
                CONSTRAINT fk_us_sucursal FOREIGN KEY (ID_SUCURSAL) REFERENCES infra_sucursal(ID_SUCURSAL) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ');
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS infra_usuario_sucursal');
    }
};
