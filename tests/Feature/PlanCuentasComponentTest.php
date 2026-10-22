<?php

namespace Tests\Feature;

use App\Http\Livewire\PlanCuentas\PlanCuentasComponent;
use App\Models\{PlanCuentas, Bancos, AsientosDetalle, ConfigPlanDetalle, GastosPlanCuentas};
use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class PlanCuentasComponentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'plan_test', 'database.connections.plan_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        foreach ([PlanCuentas::class, Bancos::class, AsientosDetalle::class, ConfigPlanDetalle::class, GastosPlanCuentas::class] as $clase) {
            $modelo = new $clase;
            Schema::create($modelo->getTable(), function (Blueprint $table) use ($modelo) {
                $table->increments('id');
                foreach (array_unique($modelo->getFillable()) as $campo) {
                    if ($campo !== 'id') $table->string($campo)->nullable();
                }
                $table->timestamps();
            });
        }
        foreach (['prestamos' => 'plan_cuenta_id', 'proveedores' => 'plan_cuenta_id', 'credit_folder_headers' => 'cuenta_contable_id', 'utilidads' => 'cuenta_pasivo_id'] as $tabla => $campo) {
            Schema::create($tabla, function (Blueprint $table) use ($campo) {
                $table->increments('id'); $table->integer('company_id'); $table->integer($campo)->nullable();
            });
        }
        $usuario = new User;
        $usuario->id = 1; $usuario->company_id = 10;
        $this->actingAs($usuario);
    }

    public function testFiltrosResumenYOrdenJerarquicoRespetanEmpresa()
    {
        foreach (['1', '1.02', '1.10', '2'] as $indice => $codigo) {
            $niveles = explode('.', $codigo);
            PlanCuentas::create(['company_id' => 10, 'codigo' => $codigo, 'nombre' => 'Cuenta ' . $codigo,
                'nivel1' => $niveles[0], 'nivel2' => $niveles[1] ?? null,
                'gasto' => $indice === 1 ? 1 : 0, 'creado_usuario' => $indice === 1 ? 1 : 0]);
        }
        PlanCuentas::create(['company_id' => 20, 'codigo' => '9', 'nombre' => 'CUENTA AJENA']);
        $componente = new PlanCuentasComponent;
        $datos = $componente->render()->getData();
        $this->assertSame(['1', '1.02', '1.10', '2'], $datos['cuentas']->pluck('codigo')->all());
        $this->assertEquals(4, $datos['resumen']->total);
        $componente->filtroUso = 'gasto'; $componente->filtroOrigen = 'personalizada';
        $this->assertSame(['1.02'], $componente->render()->getData()['cuentas']->pluck('codigo')->all());
        $componente->page = 3; $componente->updated('search');
        $this->assertSame(1, $componente->page);
        Livewire::test(PlanCuentasComponent::class)->assertSee('Catálogo de cuentas')->assertSee('Estado de resultados')->assertDontSee('CUENTA AJENA');
    }

    public function testCrearEditarYValidarCuentaCompleta()
    {
        Bancos::create(['id' => 5, 'company_id' => 10, 'nombre' => 'Banco de prueba', 'tipo_cuenta_id' => 1, 'numero_cuenta' => '123', 'status' => 1]);
        $componente = Livewire::test(PlanCuentasComponent::class)->set('nombre', 'préstamos')->set('codigo', '1.02.03')
            ->set('prestamo', true)->set('gasto', true)->set('utilidad', true)->set('estado_resultados', true)
            ->set('tiempo_inicio', 5)->set('tiempo_fin', 30)->set('banco_id', 5)->set('accion_cuenta', 'SUMA')
            ->call('nuevoPlan')->assertHasNoErrors();
        $cuenta = PlanCuentas::first();
        $this->assertSame('PRÉSTAMOS', $cuenta->nombre);
        $this->assertEquals(3, $cuenta->nivel3);
        $this->assertEquals(5, $cuenta->banco_id);
        $componente->set('codigo', '2.01')->set('accion_cuenta', 'RESTA')->call('actualizarPlan')->assertHasNoErrors();
        $this->assertNull($cuenta->fresh()->nivel3);
        $this->assertSame('RESTA', $cuenta->fresh()->accion_cuenta);
        $componente->call('reiniciarNuevo')->set('nombre', 'Duplicada')->set('codigo', '2.01')->call('nuevoPlan')->assertHasErrors('codigo');
        $componente->set('codigo', '1.123')->call('nuevoPlan')->assertHasErrors('codigo');
        $componente->set('codigo', '3.01')->set('tiempo_inicio', 20)->set('tiempo_fin', 5)->call('nuevoPlan')->assertHasErrors('tiempo_fin');
    }

    public function testCuentaAjenaNoSePuedeAbrir()
    {
        $ajena = PlanCuentas::create(['company_id' => 20, 'codigo' => '1', 'nombre' => 'Ajena']);
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        (new PlanCuentasComponent)->verPlan($ajena->id);
    }

    public function testEditarCuentaHistoricaConservaCodigoYNiveles()
    {
        $cuenta = PlanCuentas::create(['company_id' => 10, 'codigo' => '2.5.0310', 'nombre' => 'Histórica',
            'nivel1' => '2', 'nivel2' => '5', 'nivel3' => '03']);
        Livewire::test(PlanCuentasComponent::class)->call('verPlan', $cuenta->id)->set('nombre', 'Histórica actualizada')
            ->call('actualizarPlan')->assertHasNoErrors();
        $this->assertSame('2.5.0310', $cuenta->fresh()->codigo);
        $this->assertSame('03', $cuenta->fresh()->nivel3);
        $this->assertSame('HISTÓRICA ACTUALIZADA', $cuenta->fresh()->nombre);
    }

    public function testEliminacionProtegeCuentasEnUsoYDeSistema()
    {
        $sistema = PlanCuentas::create(['company_id' => 10, 'codigo' => '1', 'creado_usuario' => 0]);
        $usada = PlanCuentas::create(['company_id' => 10, 'codigo' => '2', 'creado_usuario' => 1]);
        $libre = PlanCuentas::create(['company_id' => 10, 'codigo' => '3', 'nombre' => 'Libre', 'creado_usuario' => 1]);
        DB::table('prestamos')->insert(['company_id' => 10, 'plan_cuenta_id' => $usada->id]);
        $componente = new PlanCuentasComponent;
        $componente->eliminarPlan($sistema->id); $componente->eliminarPlan($usada->id);
        $this->assertSame(3, PlanCuentas::count());
        $componente->confirmarEliminar($libre->id);
        $this->assertEquals($libre->id, $componente->id_eliminar);
        $componente->cancelarEliminar();
        $this->assertNull($componente->id_eliminar);
        $componente->eliminarPlan($libre->id);
        $this->assertNull(PlanCuentas::find($libre->id));
    }
}
