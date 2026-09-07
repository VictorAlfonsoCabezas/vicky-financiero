<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropDescargoBovedasDetalleTable extends Migration
{
    public function up()
    {
        Schema::dropIfExists('descargo_bovedas_detalle');
    }
}
