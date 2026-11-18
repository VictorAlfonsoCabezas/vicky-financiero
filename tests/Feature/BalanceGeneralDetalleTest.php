<?php

namespace Tests\Feature;

use App\Http\Livewire\BalanceGeneral\BalanceGeneralComponent;
use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class BalanceGeneralDetalleTest extends TestCase
{
    public function testElDetalleCuadraConElSaldoYPaginaSoloLosAsientosDeLaSubcuenta()
    {
        config([
            'database.default' => 'balance_test',
            'database.connections.balance_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => ''],
        ]);

        Schema::create('plan_cuentas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->string('codigo');
            $table->string('nombre');
            $table->boolean('estado_resultados')->default(false);
            for ($nivel = 1; $nivel <= 7; $nivel++) {
                $table->integer('nivel' . $nivel)->nullable();
            }
        });
        Schema::create('conceptos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre');
        });
        Schema::create('asientos_header', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->dateTime('fecha_contable');
            $table->string('descripcion');
            $table->integer('concepto_id')->default(1);
        });
        Schema::create('asientos_detalle', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('asientos_header_id');
            $table->integer('plan_cuentas_id');
            $table->boolean('debe_haber');
            $table->decimal('valor', 12, 2);
        });

        DB::table('plan_cuentas')->insert([
            ['id' => 1, 'company_id' => 10, 'codigo' => '1', 'nombre' => 'Activos', 'nivel1' => 1, 'nivel2' => null],
            ['id' => 2, 'company_id' => 10, 'codigo' => '1.1', 'nombre' => 'Caja', 'nivel1' => 1, 'nivel2' => 1],
        ]);
        DB::table('conceptos')->insert(['id' => 1, 'nombre' => 'Diario']);
        DB::table('asientos_header')->insert([
            ['id' => 1, 'company_id' => 10, 'fecha_contable' => '2024-01-31 23:59:59', 'descripcion' => 'Apertura de caja'],
            ['id' => 2, 'company_id' => 10, 'fecha_contable' => '2024-02-01 00:00:00', 'descripcion' => 'Fuera del periodo'],
            ['id' => 3, 'company_id' => 20, 'fecha_contable' => '2024-01-20 00:00:00', 'descripcion' => 'Otra empresa'],
        ]);
        DB::table('asientos_detalle')->insert(array_fill(0, 16, [
            'asientos_header_id' => 1, 'plan_cuentas_id' => 2, 'debe_haber' => true, 'valor' => 10,
        ]));
        DB::table('asientos_detalle')->insert([
            ['asientos_header_id' => 1, 'plan_cuentas_id' => 1, 'debe_haber' => false, 'valor' => 25],
            ['asientos_header_id' => 2, 'plan_cuentas_id' => 2, 'debe_haber' => true, 'valor' => 300],
            ['asientos_header_id' => 3, 'plan_cuentas_id' => 2, 'debe_haber' => true, 'valor' => 500],
        ]);

        $user = new User();
        $user->id = 1;
        $user->company_id = 10;
        $this->actingAs($user);

        $component = Livewire::test(BalanceGeneralComponent::class)
            ->set('fechaInicio', '2024-01-01')
            ->set('fechaFin', '2024-01-31')
            ->call('abrirDetalleBalance', 1)
            ->assertDispatchedBrowserEvent('balance-detalle-abrir')
            ->assertSee('Movimientos directos');

        $detalle = $component->viewData('detalle');
        $this->assertEquals(160, $detalle['totales']->debe);
        $this->assertEquals(25, $detalle['totales']->haber);
        $this->assertEquals(135, $detalle['totales']->neto);
        $this->assertEquals($component->viewData('planCuentas')->first()->saldo, $detalle['totales']->neto);
        $this->assertEquals(135, $detalle['subcuentas']->sum('neto'));

        $component->call('seleccionarSubcuenta', 2)
            ->assertSee('Diario')
            ->assertSee('Apertura de caja')
            ->call('gotoPage', 2, 'detallePage');
        $this->assertCount(1, $component->viewData('detalle')['movimientos']);
        $this->assertEquals(16, $component->viewData('detalle')['movimientos']->total());
        $this->assertEquals(135, $component->viewData('detalle')['totales']->neto);
        $this->assertEquals(1, $component->viewData('planCuentas')->currentPage());

        $component->call('cerrarDetalleBalance');
        $this->assertNull($component->viewData('detalle'));
    }
}
