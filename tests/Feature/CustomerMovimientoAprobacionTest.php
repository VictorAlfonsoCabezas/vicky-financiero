<?php

namespace Tests\Feature;

use App\Http\Livewire\CustomerMovimientoAprobacion\CustomerMovimientoAprobacionComponent;
use App\Models\{Bancos, Bovedas, CartolaDetail, CartolaHeader, Company, Customer, CustomerHistorial, CustomerMovimiento, CustomerMovimientoSolicitud, CustomerTipoAhorros, DescargoBovedasHeader, FormasPago, OperacionesDescargoBovedas, TipoAhorros, TipoAhorrosDetalle, TypeTransaction, WhaEnvios};
use App\User;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CustomerMovimientoAprobacionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'approval_test', 'database.connections.approval_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        foreach ([Bancos::class, Bovedas::class, CartolaDetail::class, CartolaHeader::class, Company::class,
            Customer::class, CustomerHistorial::class, CustomerMovimiento::class, CustomerMovimientoSolicitud::class,
            CustomerTipoAhorros::class, DescargoBovedasHeader::class, FormasPago::class, OperacionesDescargoBovedas::class,
            TipoAhorros::class, TipoAhorrosDetalle::class, TypeTransaction::class, WhaEnvios::class] as $class) {
            $model = new $class;
            Schema::create($model->getTable(), function (Blueprint $t) use ($model) {
                $t->increments('id');
                foreach (array_unique($model->getFillable()) as $field) {
                    if ($field === 'id') continue;
                    if ($field === 'status' && !($model instanceof CartolaHeader)) $t->boolean($field)->default(true);
                    else $t->string($field)->nullable();
                }
                $t->timestamps();
            });
        }
        DB::table('company')->insert(['id' => 10, 'comercial_name' => 'Test', 'cartola_a' => 100, 'cartola_b' => 100]);
        $user = new User; $user->id = 1; $user->company_id = 10;
        $user->setRelation('company', Company::find(10));
        $this->actingAs($user);
        DB::table('customer')->insert(['id' => 1, 'company_id' => 10, 'code' => 'CLI001', 'nombres' => 'Ana', 'apellidos' => 'Test', 'numero_documento' => '00123']);
        DB::table('tipo_ahorros')->insert(['id' => 1, 'company_id' => 10, 'name' => 'Ahorros']);
        DB::table('customer_tipo_ahorros')->insert(['id' => 1, 'company_id' => 10, 'customer_id' => 1, 'tipo_ahorros_id' => 1, 'codigo' => 'CTA001']);
        DB::table('bovedas')->insert(['id' => 1, 'company_id' => 10, 'principal' => 1]);
        DB::table('formas_pago')->insert(['id' => 1, 'nombre' => 'Efectivo']);
        DB::table('operaciones_descargo_bovedas')->insert(['id' => 1, 'company_id' => 10, 'nombre_corto' => 'INVA', 'descripcion' => 'Ingreso']);
        foreach (['SOL', 'SE', 'SC'] as $i => $name) DB::table('type_transactions')->insert(['id' => $i + 1, 'company_id' => 10, 'name_corto' => $name, 'name' => $name, 'action' => 'S', 'afecta' => $name === 'SE' ? 'E' : 'C']);
        DB::table('cartola_headers')->insert(['id' => 1, 'company_id' => 10, 'customer_id' => 1, 'customer_tipo_ahorro_id' => 1, 'status' => 'ACTIVA', 'code' => '001']);
    }

    private function requestRow(array $data = [])
    {
        return CustomerMovimientoSolicitud::create(array_merge(['company_id' => 10, 'customer_id' => 1,
            'customer_tipo_ahorro_id' => 1, 'forma_pago_id' => '1', 'valor' => '100.00', 'estado' => 'PENDIENTE',
            'fecha_creacion' => '2026-10-06', 'comprobante' => 'REF001'], $data));
    }

    private function approve($component, $id)
    {
        $component->aceptar($id);
    }

    public function testApprovalCreatesConsistentRecordsAndCannotBeRepeated()
    {
        $row = $this->requestRow(['banco_id' => 999]);
        $component = new CustomerMovimientoAprobacionComponent;
        $this->approve($component, $row->id);
        $this->assertSame('APROBADO', $row->fresh()->estado);
        $this->assertEquals(100, CustomerMovimiento::sum('valor_movimiento'));
        $this->assertNull(CustomerMovimiento::first()->banco_id);
        $this->assertEquals(100, CustomerHistorial::sum('valor_movimiento'));
        $this->assertEquals(100, DescargoBovedasHeader::sum('valor'));
        $this->assertEquals(100, CartolaDetail::sum('valor_transaction'));
        $this->assertSame(1, WhaEnvios::count());
        try { $this->approve($component, $row->id); $this->fail('Duplicate approval accepted'); }
        catch (ValidationException $e) { $this->assertSame(1, CustomerMovimiento::count()); }
    }

    public function testSplitApprovalUsesEachInstallmentValueInHistory()
    {
        TipoAhorrosDetalle::create(['company_id' => 10, 'tipo_ahorros_id' => 1, 'afecta' => 'E', 'valor' => 40]);
        TipoAhorrosDetalle::create(['company_id' => 10, 'tipo_ahorros_id' => 1, 'afecta' => 'C', 'valor' => 60]);
        $row = $this->requestRow();
        $this->approve(new CustomerMovimientoAprobacionComponent, $row->id);
        $this->assertSame('APROBADO', $row->fresh()->estado);
        $this->assertSame(2, CustomerMovimiento::count());
        $this->assertEquals(100, CustomerHistorial::sum('valor_movimiento'));
        $this->assertEquals([40, 60], CustomerHistorial::orderBy('id')->pluck('valor_movimiento')->map(function ($v) { return (float) $v; })->all());
    }

    public function testFailureRollsBackAllApprovalRecords()
    {
        $row = $this->requestRow();
        Schema::drop('cartola_details');
        try { $this->approve(new CustomerMovimientoAprobacionComponent, $row->id); $this->fail('Expected database failure'); }
        catch (\Illuminate\Database\QueryException $e) {
            $this->assertSame('PENDIENTE', $row->fresh()->estado);
            $this->assertSame(0, CustomerMovimiento::count());
            $this->assertSame(0, CustomerHistorial::count());
            $this->assertSame(0, DescargoBovedasHeader::count());
        }
    }

    public function testRejectionRequiresReasonAndCannotOverwriteApproval()
    {
        $row = $this->requestRow();
        $component = new CustomerMovimientoAprobacionComponent;
        $component->seleccionar($row->id); $component->razon_rechazo = '   ';
        try { $component->storeRechazar(); $this->fail('Blank reason accepted'); }
        catch (ValidationException $e) { $this->assertSame('PENDIENTE', $row->fresh()->estado); }
        $component->razon_rechazo = ' Comprobante inválido '; $component->storeRechazar();
        $this->assertSame('RECHAZADO', $row->fresh()->estado);
        $this->assertSame('Comprobante inválido', $row->fresh()->razon_rechazado);
        $this->assertEquals(1, $row->fresh()->user_rechazado);
        $row->estado = 'APROBADO'; $row->save();
        $component->razon_rechazo = 'Otro motivo';
        $this->expectException(ValidationException::class); $component->storeRechazar();
    }

    public function testOtherCompanyRequestIsNotAccessible()
    {
        $row = $this->requestRow(['company_id' => 20]);
        $this->expectException(ModelNotFoundException::class);
        $this->approve(new CustomerMovimientoAprobacionComponent, $row->id);
    }

    public function testNewPassbookBelongsToCurrentCompany()
    {
        CartolaHeader::query()->delete();
        $row = $this->requestRow();
        $this->approve(new CustomerMovimientoAprobacionComponent, $row->id);
        $this->assertEquals(10, CartolaHeader::first()->company_id);
        $this->assertSame(1, CartolaDetail::count());
    }

    public function testFullPassbookCreatesNextPassbookForCurrentCompany()
    {
        DB::table('company')->where('id', 10)->update(['cartola_a' => 1, 'cartola_b' => 1]);
        CartolaDetail::create(['cartola_headers_id' => 1, 'cara' => 'A']);
        CartolaDetail::create(['cartola_headers_id' => 1, 'cara' => 'B']);
        $row = $this->requestRow();
        $this->approve(new CustomerMovimientoAprobacionComponent, $row->id);
        $this->assertSame('CERRADA', CartolaHeader::find(1)->status);
        $this->assertEquals(10, CartolaHeader::where('status', 'ACTIVA')->first()->company_id);
        $this->assertEquals('002', CartolaHeader::where('status', 'ACTIVA')->first()->code);
    }
    public function testMissingVaultLeavesRequestPending()
    {
        Bovedas::query()->delete();
        $row = $this->requestRow();
        $this->approve(new CustomerMovimientoAprobacionComponent, $row->id);
        $this->assertSame('PENDIENTE', $row->fresh()->estado);
        $this->assertSame(0, CustomerMovimiento::count());
    }

    public function testInvalidInitialDepositDoesNotWriteMovements()
    {
        TipoAhorrosDetalle::create(['company_id' => 10, 'tipo_ahorros_id' => 1, 'afecta' => 'C', 'valor' => 150]);
        $row = $this->requestRow();
        $this->approve(new CustomerMovimientoAprobacionComponent, $row->id);
        $this->assertSame('PENDIENTE', $row->fresh()->estado);
        $this->assertSame(0, CustomerMovimiento::count());
    }

    public function testBalancesExcludeOtherCompany()
    {
        CustomerHistorial::create(['company_id' => 20, 'customer_code' => 'CLI001', 'customer_tipo_ahorro_id' => 1, 'type_transaction_action' => 'S', 'valor_movimiento' => 900]);
        $component = new CustomerMovimientoAprobacionComponent;
        $this->assertEquals(0, $component->saldoEnCuenta(1, 1));
    }
    public function testScreenRendersActionsAndFilters()
    {
        $this->requestRow();
        \Livewire\Livewire::test(CustomerMovimientoAprobacionComponent::class)
            ->assertSee('Aprobación de movimientos')->assertSee('Aprobar')->assertSee('Detalles')
            ->set('search', 'NO-EXISTE')->assertSee('No hay solicitudes para estos filtros');
    }

    public function testDetailsHandleMissingBankAndClearPreviousRejectionReason()
    {
        $row = $this->requestRow(['banco_id' => 999]);
        $component = new CustomerMovimientoAprobacionComponent;
        $component->razon_rechazo = 'Motivo anterior';
        $component->abrirModal($row->id);
        $this->assertSame('', $component->banco);
        $this->assertSame('', $component->razon_rechazo);
        $this->assertEquals(100, $component->valor);
    }
    public function testSearchStateFiltersAndPagination()
    {
        $this->requestRow(); $this->requestRow(['estado' => 'RECHAZADO', 'comprobante' => 'RECHAZO02']);
        $this->requestRow(['company_id' => 20]);
        $component = new CustomerMovimientoAprobacionComponent;
        $this->assertSame(1, $component->render()->getData()['movimientos']->total());
        $component->estadoFiltro = ''; $component->search = 'CTA001';
        $this->assertSame(2, $component->render()->getData()['movimientos']->total());
        $component->search = 'RECHAZO02';
        $this->assertSame(1, $component->render()->getData()['movimientos']->total());
        $component->page = 3; $component->updated('search'); $this->assertSame(1, $component->page);
    }
}
