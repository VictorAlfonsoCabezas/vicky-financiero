<?php

namespace Tests\Feature;

use App\Http\Livewire\ConfiguracionCuenta\ConfiguracionCuentaComponent;
use App\Models\{AsientosHeader, Conceptos, ConfigPlanDetalle, ConfigPlanHeader, CustomerMovimiento, CreditFolderDetail, DescargoBovedasHeader, OperacionesDescargoBovedas, PlanCuentas, TypeTransaction};
use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};
use Livewire\Livewire;
use Tests\TestCase;

class ConfiguracionCuentaComponentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'config_cuenta_test', 'database.connections.config_cuenta_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        foreach ([AsientosHeader::class, Conceptos::class, ConfigPlanDetalle::class, ConfigPlanHeader::class,
            CustomerMovimiento::class, CreditFolderDetail::class, DescargoBovedasHeader::class,
            OperacionesDescargoBovedas::class, PlanCuentas::class, TypeTransaction::class] as $clase) {
            $modelo = new $clase;
            Schema::create($modelo->getTable(), function (Blueprint $table) use ($modelo) {
                $table->increments('id');
                foreach (array_unique($modelo->getFillable()) as $campo) {
                    if ($campo !== 'id') $table->string($campo)->nullable();
                }
                $table->timestamps();
            });
        }
        $usuario = new User; $usuario->id = 1; $usuario->company_id = 10;
        $this->actingAs($usuario);
        TypeTransaction::create(['id' => 1, 'company_id' => 10, 'name' => 'Depósitos', 'status' => 1]);
        Conceptos::create(['id' => 1, 'company_id' => 10, 'nombre' => 'DIARIO', 'status' => 1]);
        PlanCuentas::create(['id' => 1, 'company_id' => 10, 'codigo' => '1.01', 'nombre' => 'CAJA']);
        PlanCuentas::create(['id' => 2, 'company_id' => 10, 'codigo' => '2.01', 'nombre' => 'AHORROS']);
    }

    private function configurar($mismaCuenta = false)
    {
        return Livewire::test(ConfiguracionCuentaComponent::class)->call('abrirModal', 0)
            ->set('type_transaction_id', '1-1')->set('concepto_id', 1)
            ->call('agregarCuenta', 1)->call('agregarCuenta', $mismaCuenta ? 1 : 2)->call('actualizarDebHaber', 1);
    }

    public function testCreaConfiguracionYConservaCadaDetalleAlEditar()
    {
        $componente = $this->configurar(true)->call('store')->assertHasNoErrors()->assertDispatchedBrowserEvent('cc-cerrar-modal');
        $header = ConfigPlanHeader::first();
        $filas = ConfigPlanDetalle::orderBy('id')->get();
        $this->assertCount(2, $filas);
        $this->assertEquals(1, $filas[0]->debe_haber);
        $this->assertEquals(0, $filas[1]->debe_haber);
        $filas[0]->update(['campo_foraneo' => 'saldo_general', 'sede_id' => 7, 'operacion_id' => 9]);
        $filas[1]->update(['campo_foraneo' => 'valor_movimiento', 'tabla' => 'credit_folder_details', 'campo' => 'interes_periodo']);
        $componente->call('abrirModal', $header->id)->call('store')->assertHasNoErrors();
        $this->assertSame(2, ConfigPlanDetalle::count());
        $this->assertSame('saldo_general', $filas[0]->fresh()->campo_foraneo);
        $this->assertEquals(7, $filas[0]->fresh()->sede_id);
        $this->assertEquals(9, $filas[0]->fresh()->operacion_id);
        $this->assertSame('interes_periodo', $filas[1]->fresh()->campo);
    }

    public function testNoGuardaConfiguracionSinAmbosLados()
    {
        $this->configurar()->call('actualizarDebHaber', 1)->call('store')->assertHasErrors('asientos');
        $this->assertSame(0, ConfigPlanHeader::count());
    }

    public function testBovedasUtilizaValorComoCampoInicial()
    {
        OperacionesDescargoBovedas::create(['id' => 3, 'company_id' => 10, 'nombre' => 'Transferencia', 'status' => 1]);
        $this->configurar()->set('type_transaction_id', '2-3')->call('store')->assertHasNoErrors();
        $this->assertNull(ConfigPlanHeader::first()->type_transaction_id);
        $this->assertEquals(3, ConfigPlanHeader::first()->operaciones_descargo_bovedas_id);
        $this->assertSame(['valor'], ConfigPlanDetalle::pluck('campo_foraneo')->unique()->values()->all());
    }

    public function testParametrosSeGuardanYRechazanCamposInvalidos()
    {
        $componente = $this->configurar()->call('store');
        $fila = ConfigPlanDetalle::first();
        $componente->call('abrirModalAsiento', $fila->config_plan_header_id)
            ->call('guardarConfigCampoForaneo', $fila->id, 'saldo_general')->assertHasNoErrors()
            ->call('guardarConfigTabla', $fila->id, 'credit_folder_details')
            ->call('guardarConfigCampo', $fila->id, 'interes_periodo')->assertHasNoErrors();
        $this->assertSame('saldo_general', $fila->fresh()->campo_foraneo);
        $this->assertSame('interes_periodo', $fila->fresh()->campo);
        $componente->call('guardarConfigCampoForaneo', $fila->id, 'campo_inexistente')->assertHasErrors('parametros');
        $this->assertSame('saldo_general', $fila->fresh()->campo_foraneo);
        $componente->call('guardarConfigTabla', $fila->id, '')->assertHasNoErrors();
        $this->assertNull($fila->fresh()->campo);
    }

    public function testListaConfiguracionesCompletasYFiltraPorCuentaSinDividirlas()
    {
        $this->configurar()->call('store');
        ConfigPlanHeader::create(['company_id' => 20, 'type_transaction_id' => 1, 'concepto_id' => 1]);
        $component = new ConfiguracionCuentaComponent;
        $component->buscarConfiguracion = 'CAJA';
        $datos = $component->render()->getData();
        $this->assertSame(1, $datos['configuraciones']->total());
        $this->assertCount(2, $datos['configuraciones']->first()->detalles);
        $this->assertEquals(1, $datos['resumen']->total);
        Livewire::test(ConfiguracionCuentaComponent::class)->assertSee('Debe y Haber definidos')->assertSee('AHORROS');
    }

    public function testNoPermiteAbrirUnaConfiguracionAjena()
    {
        $header = ConfigPlanHeader::create(['company_id' => 20]);
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        (new ConfiguracionCuentaComponent)->abrirModal($header->id);
    }

    public function testEliminaConfiguracionLibreYProtegeAsientosAsociados()
    {
        $this->configurar()->call('store');
        $header = ConfigPlanHeader::first();
        $id = DB::table('asientos_header')->insertGetId(['company_id' => 10, 'config_plan_header_id' => $header->id]);
        $component = new ConfiguracionCuentaComponent;
        $component->elminarConfigCompleta($header->id);
        $this->assertNotNull($header->fresh());
        DB::table('asientos_header')->where('id', $id)->delete();
        $component->elminarConfigCompleta($header->id);
        $this->assertSame(0, ConfigPlanHeader::count());
        $this->assertSame(0, ConfigPlanDetalle::count());
    }
}
