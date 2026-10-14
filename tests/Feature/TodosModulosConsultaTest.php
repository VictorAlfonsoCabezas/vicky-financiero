<?php

namespace Tests\Feature;

use App\Http\Livewire\TodosModulos\TodosModulosComponent;
use App\Models\{AsientosDetalle, AsientosHeader, CentroCostos, Conceptos, ConfigPlanHeader, CustomerMovimiento, PlanCuentas, Sedes, SedesCentroCostos, TipoConcepto, TypeTransaction};
use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TodosModulosConsultaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'modulos_test', 'database.connections.modulos_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '']]);
        foreach ([AsientosDetalle::class, AsientosHeader::class, CentroCostos::class, Conceptos::class, ConfigPlanHeader::class, CustomerMovimiento::class, PlanCuentas::class, Sedes::class, SedesCentroCostos::class, TipoConcepto::class, TypeTransaction::class] as $class) {
            $model = new $class;
            Schema::create($model->getTable(), function (Blueprint $table) use ($model) {
                $table->increments('id');
                foreach (array_unique($model->getFillable()) as $field) {
                    if ($field !== 'id') $table->string($field)->nullable();
                }
                $table->timestamps();
            });
        }
        $user = new User;
        $user->id = 1;
        $user->company_id = 10;
        $this->actingAs($user);
        DB::table('type_transactions')->insert(['id' => 1, 'company_id' => 10, 'name' => 'Depósito', 'status' => 1]);
        for ($id = 1; $id <= 6; $id++) {
            DB::table('customer_movimientos')->insert(['id' => $id, 'company_id' => $id === 6 ? 20 : 10,
                'customer_name' => 'Cliente ' . $id, 'customer_ruc' => 'DOC' . $id, 'type_transaction_id' => 1,
                'type_transaction_name' => 'Depósito', 'valor_movimiento' => 100,
                'date_created' => '2026-10-13', 'hour_created' => '10:00:00', 'status' => $id === 5 ? 0 : 1]);
        }
        foreach ([2, 3, 4] as $id) {
            DB::table('asientos_header')->insert(['id' => $id, 'company_id' => 10, 'customer_movimiento_id' => $id,
                'fecha_contable' => '2026-10-13', 'descripcion' => $id === 4 ? '[PENDIENTE CONTABLE] Revisar | Prueba' : 'Prueba', 'status' => 1]);
            foreach ([1 => 100, 0 => $id === 2 ? 80 : 100] as $debe => $valor) {
                DB::table('asientos_detalle')->insert(['company_id' => 10, 'asientos_header_id' => $id,
                    'debe_haber' => $debe, 'valor' => $valor, 'status' => 1]);
            }
        }
        DB::table('asientos_header')->insert(['id' => 99, 'company_id' => 10, 'fecha_contable' => '2026-10-13',
            'descripcion' => '[PENDIENTE CONTABLE] Sin configuración', 'status' => 1]);
    }

    private function componente()
    {
        $component = new TodosModulosComponent;
        $component->fechaInicio = '2026-10-01';
        $component->fechaFin = '2026-10-31';
        $component->mount();
        return $component;
    }

    public function testAllModulesShowTotalsAndPendingGeneralEntriesByDefault()
    {
        $data = $this->componente()->render()->getData();
        $this->assertSame(5, $data['modulos']->total());
        $this->assertEquals(3, $data['resumen']->pendientes);
        $this->assertEquals(1, $data['resumen']->cuadrados);
        $this->assertEquals(1, $data['resumen']->sin_asiento);
        $this->assertFalse($data['modulos']->getCollection()->contains('customer_name', 'Cliente 6'));
    }

    public function testStateSearchAndModuleFiltersKeepCorrectSummary()
    {
        $component = $this->componente();
        $component->estadoContable = 'pendiente';
        $data = $component->render()->getData();
        $this->assertSame(3, $data['modulos']->total());
        $this->assertEquals(5, $data['resumen']->total);
        $component->buscarMovimiento = 'DOC4';
        $this->assertSame(1, $component->render()->getData()['modulos']->total());
        $component->buscarMovimiento = '';
        $component->modulo_id = 1;
        $this->assertSame(2, $component->render()->getData()['modulos']->total());
        $component->estadoContable = 'sin_asiento';
        $this->assertSame(1, $component->render()->getData()['modulos']->total());
    }

    public function testChangingFiltersResetsPagination()
    {
        $component = $this->componente();
        $component->page = 3;
        $component->updated('buscarMovimiento');
        $this->assertEquals(1, $component->page);
        $component->modulo_id = 1;
        $component->limpiarFiltros();
        $this->assertEquals(0, $component->modulo_id);
        $this->assertSame('', $component->estadoContable);
    }

    public function testReviewWithoutAccountsRendersItsReasonAndBootstrapPanels()
    {
        \Livewire\Livewire::test(TodosModulosComponent::class)
            ->set('fechaInicio', '2026-10-01')->set('fechaFin', '2026-10-31')
            ->call('abrirModal', 99)
            ->assertSee('panel panel-inverse', false)->assertSee('widget widget-stats', false)
            ->assertSee('Sin configuración')->assertSee('aún no tiene cuentas')->assertDontSee('fa-chair', false);
    }

    public function testReviewCannotOpenAnotherCompanyEntry()
    {
        DB::table('asientos_header')->insert(['id' => 100, 'company_id' => 20, 'status' => 1]);
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $this->componente()->abrirModal(100);
    }
}
