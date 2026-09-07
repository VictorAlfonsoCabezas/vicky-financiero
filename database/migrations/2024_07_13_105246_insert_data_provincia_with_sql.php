<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InsertDataProvinciaWithSql extends Migration
{
    public function up()
    {
        DB::statement("
            INSERT INTO provincia (nombre, defecto, country_id, status) VALUES
            ('Pichincha', 1, 1, 1),
            ('Guayas', 0, 1, 1),
            ('Azuay', 0, 1, 1),
            ('Bolívar', 0, 1, 1),
            ('Manabí', 0, 1, 1),
            ('Cañar', 0, 1, 1),
            ('Carchi', 0, 1, 1),
            ('Chimborazo', 0, 1, 1),
            ('Cotopaxi', 0, 1, 1),
            ('El Oro', 0, 1, 1),
            ('Esmeraldas', 0, 1, 1),
            ('Galápagos', 0, 1, 1),
            ('Imbabura', 0, 1, 1),
            ('Loja', 0, 1, 1),
            ('Los Ríos', 0, 1, 1),
            ('Morona-Santiago', 0, 1, 1),
            ('Napo', 0, 1, 1),
            ('Orellana', 0, 1, 1),
            ('Pastaza	Puyo', 0, 1, 1),
            ('Santa Elena', 0, 1, 1),
            ('Santo Domingo de los Tsáchilas', 0, 1, 1),
            ('Sucumbíos', 0, 1, 1),
            ('Tungurahua', 0, 1, 1),
            ('Zamora-Chinchipe', 0, 1, 1);
        ");
    }

    public function down()
    {
        //
    }
}
