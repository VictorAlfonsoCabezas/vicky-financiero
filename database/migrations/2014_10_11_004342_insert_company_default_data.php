<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InsertCompanyDefaultData extends Migration
{
    public function up()
    {
        /*DB::table('company')->insert([
            [
                'id' => '1',
                'ruc' => '1700000000',
                'company_name' => "Empresa Nueva",
                'company_type' => 2,
                'comercial_name' => "Nombre comercial",
                'company_description' => "Descripcion comercial",
                'address' => "Condado",
                'phone' => "593999999999",
                'email' => "email@codev.com",
                'status' => true,
                'created_at' => date('Y-m-d H:m:s'),
                'updated_at' => date('Y-m-d H:m:s'),
            ]
        ]);*/
    }

    public function down()
    {
        /*Schema::table('company', function (Blueprint $table) {
            DB::table('company')
                ->where('id', '1')
                ->delete();
        });*/
    }
}
