<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InsertNivelAcademicoData extends Migration
{
    public function up()
    {
        DB::table('nivel_academico')->insert([
            [
                'id' => 1,
                'nombre' => 'PRIMARIA COMPLETA',
                'defecto' => false,
                'status' => true,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            [
                'id' => 2,
                'nombre' => 'PRIMARIA INCOMPLETA',
                'defecto' => false,
                'status' => true,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            [
                'id' => 3,
                'nombre' => 'SECUNDARIA COMPLETA',
                'defecto' => false,
                'status' => true,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            [
                'id' => 4,
                'nombre' => 'SECUNDARIA INCOMPLETA',
                'defecto' => false,
                'status' => true,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            [
                'id' => 5,
                'nombre' => 'BACHILLERATO COMPLETO',
                'defecto' => false,
                'status' => true,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            [
                'id' => 6,
                'nombre' => 'BACHILLERATO INCOMPLETO',
                'defecto' => false,
                'status' => true,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            [
                'id' => 7,
                'nombre' => 'TÉCNICO COMPLETO',
                'defecto' => false,
                'status' => true,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            [
                'id' => 8,
                'nombre' => 'TÉCNICO INCOMPLETO',
                'defecto' => false,
                'status' => true,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            [
                'id' => 9,
                'nombre' => 'TECNOLÓGICO COMPLETO',
                'defecto' => false,
                'status' => true,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            [
                'id' => 10,
                'nombre' => 'TECNOLÓGICO INCOMPLETO',
                'defecto' => false,
                'status' => true,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            [
                'id' => 11,
                'nombre' => 'UNIVERSITARIO COMPLETO',
                'defecto' => false,
                'status' => true,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            [
                'id' => 12,
                'nombre' => 'UNIVERSITARIO INCOMPLETO',
                'defecto' => false,
                'status' => true,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            [
                'id' => 13,
                'nombre' => 'ANALFABETO/(A)',
                'defecto' => false,
                'status' => true,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],

        ]);
    }

    public function down()
    {
        //
    }
}
