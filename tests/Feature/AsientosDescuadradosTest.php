<?php

namespace Tests\Feature;

use App\Http\Middleware\AdvertirAsientosDescuadrados;
use App\Models\{AsientosDetalle, AsientosHeader, Company, Conceptos, ConfigPlanDetalle, ConfigPlanHeader, CreditFolderDetail, CreditFolderHeader, Customer, CustomerHistorial, CustomerMovimiento, CustomerMovimientoSolicitud, DescargoBovedasHeader, Gastos, GastosFormaPagos, GastosPlanCuentas, PlanCuentas, Prestamos, TypeTransaction, Utilidad, WhaEnvios};
use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AsientosDescuadradosTest extends TestCase
{
    /** @dataProvider recalculosDesdeTodosModulos */
    public function testTodosModulosPersisteObservacionAlRecalcular($masivo, $observacionAnterior)
    {
        $datos = $this->configurarMovimiento();
        $datos['code'] = 'MOV-RECALCULO-01';
        $datos['customer_name'] = 'Cliente de recálculo';
        $datos['observation'] = 'Abono en efectivo';
        TypeTransaction::where('id', 1)->update(['name' => 'Abono de ahorro']);
        $movimiento = CustomerMovimiento::create($datos);
        $id = AsientosHeader::first()->id;
        DB::table('asientos_header')->where('id', $id)->update(['observacion' => $observacionAnterior]);
        DB::table('customer_movimientos')->where('id', $movimiento->id)->update(['valor_movimiento' => 250]);

        $componente = new \App\Http\Livewire\TodosModulos\TodosModulosComponent;
        if ($masivo) {
            $componente->fechaInicio = '2026-10-01';
            $componente->fechaFin = '2026-10-31';
            $componente->calcularTodosAsientos();
        } else {
            $componente->recalcularAsiento($movimiento->id);
        }

        $asiento = AsientosHeader::findOrFail($id);
        $this->assertSame(1, AsientosHeader::count());
        $this->assertStringContainsString('Cliente: Cliente de recálculo', $asiento->observacion);
        $this->assertStringContainsString('Abono de ahorro por 250.00', $asiento->observacion);
        $this->assertStringContainsString('MOV-RECALCULO-01', $asiento->observacion);
    }

    public function recalculosDesdeTodosModulos()
    {
        return [[false, null], [false, 'Observación anterior'], [true, null], [true, 'Observación anterior']];
    }

    public function testTodosModulosCompletaObservacionDeAsientoGeneralExistente()
    {
        $this->configurarMovimiento();
        $asiento = AsientosHeader::create(['company_id' => 10, 'manual' => 0,
            'descripcion' => 'Ajuste general de prueba', 'fecha_contable' => '2026-10-13',
            'fecha_creacion' => '2026-10-13', 'user_created' => 1, 'status' => 1]);
        DB::table('asientos_header')->where('id', $asiento->id)->update(['observacion' => null]);
        $componente = new \App\Http\Livewire\TodosModulos\TodosModulosComponent;
        $componente->recalcularAsientoGeneral($asiento->id, 1);
        $texto = $asiento->fresh()->observacion;
        $this->assertStringContainsString('Usuario:', $texto);
        $this->assertStringContainsString('Ajuste general de prueba', $texto);
        $this->assertStringContainsString('Sin movimiento asociado', $texto);
    }

    public function testObservacionIdentificaClienteOperacionYMovimiento()
    {
        DB::table('customer_movimientos')->insert(['id' => 50, 'company_id' => 10,
            'code' => 'MOV-050', 'customer_id' => 8, 'customer_name' => 'María López',
            'type_transaction_name' => 'Abono de ahorro', 'customer_tipo_ahorro_id' => 1,
            'valor_movimiento' => 150, 'observation' => 'Depósito en efectivo']);
        $asiento = AsientosHeader::create(['company_id' => 10, 'customer_movimiento_id' => 50,
            'descripcion' => 'ASIENTO AUTOMATICO']);
        $texto = $asiento->fresh()->observacion;
        foreach (['Ahorros - Cliente: María López', 'Abono de ahorro por 150.00', 'Depósito en efectivo', 'MOV-050 (ID 50)'] as $parte) {
            $this->assertStringContainsString($parte, $texto);
        }
        $this->assertSame('ASIENTO AUTOMATICO', $asiento->descripcion);
        DB::table('customer_movimientos')->where('id', 50)->update(['valor_movimiento' => 200]);
        $asiento->save();
        $this->assertStringContainsString('200.00', $asiento->fresh()->observacion);
    }

    public function testObservacionManualIdentificaUsuarioYSinMovimiento()
    {
        User::insert(['id' => 1, 'company_id' => 10, 'firstname' => 'Carlos', 'lastname' => 'Pérez']);
        $asiento = AsientosHeader::create(['company_id' => 10, 'manual' => 1,
            'user_created' => 1, 'descripcion' => 'Ajuste del saldo de caja']);
        $texto = $asiento->fresh()->observacion;
        $this->assertStringContainsString('Asientos manuales - Usuario: Carlos Pérez', $texto);
        $this->assertStringContainsString('Ajuste del saldo de caja', $texto);
        $this->assertStringContainsString('Movimiento: Sin movimiento asociado', $texto);
    }

    public function testObservacionGeneralConservaContextoDeOrigen()
    {
        $asiento = new AsientosHeader(['company_id' => 10, 'user_created' => 1]);
        $asiento->contextoObservacion = ['modulo' => 'Créditos', 'customer_name' => 'Ana Ruiz',
            'operacion' => 'Se contabilizaron intereses por 45.00 del crédito CR-25'];
        $asiento->save();
        $texto = $asiento->fresh()->observacion;
        $this->assertStringContainsString('Créditos - Cliente: Ana Ruiz', $texto);
        $this->assertStringContainsString('crédito CR-25', $texto);
        $recargado = $asiento->fresh();
        $recargado->status = 1;
        $recargado->save();
        $this->assertSame($texto, $recargado->fresh()->observacion);
    }

    public function testObservacionNoConsultaMovimientoDeOtraEmpresa()
    {
        DB::table('customer_movimientos')->insert(['id' => 50, 'company_id' => 99,
            'code' => 'PRIVADO', 'customer_name' => 'Cliente privado']);
        $asiento = AsientosHeader::create(['company_id' => 10, 'customer_movimiento_id' => 50]);
        $this->assertStringNotContainsString('PRIVADO', $asiento->observacion);
        $this->assertStringNotContainsString('Cliente privado', $asiento->observacion);
    }

    public function testObservacionPagoAgrupadoIncluyeTodosLosCodigos()
    {
        TypeTransaction::create(['id' => 9, 'company_id' => 10, 'name_corto' => 'PC', 'name' => 'Pago de crédito']);
        foreach ([51 => 100, 52 => 25] as $id => $valor) {
            DB::table('customer_movimientos')->insert(['id' => $id, 'company_id' => 10,
                'code' => 'MOV-' . $id, 'customer_name' => 'Ana Ruiz', 'type_transaction_id' => 9,
                'credit_folder_details_id' => 7, 'valor_movimiento' => $valor, 'status' => 1]);
        }
        $asiento = AsientosHeader::create(['company_id' => 10, 'customer_movimiento_id' => 51]);
        $this->assertStringContainsString('MOV-51 (ID 51), MOV-52 (ID 52)', $asiento->observacion);
        $this->assertStringContainsString('Total de movimientos de la cuota: 125.00', $asiento->observacion);
    }

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'asientos_test', 'database.connections.asientos_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        foreach ([\App\Models\OperacionesDescargoBovedas::class, \App\Models\RegistroFormasPago::class] as $clase) {
            $modelo = new $clase;
            Schema::create($modelo->getTable(), function (Blueprint $table) use ($modelo) {
                $table->increments('id');
                foreach ($modelo->getFillable() as $campo) {
                    if ($campo !== 'id') $table->string($campo)->nullable();
                }
                $table->timestamps();
            });
        }
        foreach ([AsientosDetalle::class, AsientosHeader::class, Company::class, Conceptos::class, ConfigPlanDetalle::class, ConfigPlanHeader::class, CreditFolderDetail::class, CreditFolderHeader::class, Customer::class, CustomerHistorial::class, CustomerMovimiento::class, CustomerMovimientoSolicitud::class, DescargoBovedasHeader::class, Gastos::class, GastosFormaPagos::class, GastosPlanCuentas::class, PlanCuentas::class, Prestamos::class, TypeTransaction::class, Utilidad::class, WhaEnvios::class, User::class] as $class) {
            $model = new $class;
            Schema::create($model->getTable(), function (Blueprint $table) use ($model) {
                $table->increments('id');
                foreach (array_unique(array_merge($model->getFillable(), $model instanceof Gastos ? ['fecha_emision'] : [])) as $field) {
                    if ($field !== 'id') $table->string($field)->nullable();
                }
                $table->timestamps();
            });
        }
        $user = new User;
        $user->id = 1;
        $user->company_id = 10;
        $this->actingAs($user);
        request()->attributes->set('asientos_descuadrados', []);
        ConfigPlanHeader::create(['id' => 1, 'company_id' => 10, 'operaciones_descargo_bovedas_id' => 1, 'status' => 1]);
        foreach ([1 => true, 2 => false] as $id => $debe) {
            PlanCuentas::create(['id' => $id, 'company_id' => 10, 'banco_id' => $debe ? null : 2]);
            ConfigPlanDetalle::create(['company_id' => 10, 'config_plan_header_id' => 1,
                'plan_cuentas_id' => $id, 'debe_haber' => $debe ? 1 : 0, 'campo_foraneo' => 'valor', 'status' => 1]);
        }
        DB::table('descargo_bovedas_header')->insert(['id' => 1, 'company_id' => 10,
            'operaciones_descargo_bovedas_id' => 1, 'estado' => 'FINALIZADO', 'status' => 1,
            'bancos_id' => 1, 'valor' => 100, 'fecha_creacion' => '2026-10-13']);
    }

    public function testUnbalancedEntryIsSavedAndRecalculationDoesNotDuplicateIt()
    {
        $result = AsientosHeader::recalcularAsientosBovedas(1);
        $this->assertSame('200', $result['code']);
        $this->assertTrue($result['warning']);
        $this->assertSame(1, AsientosHeader::count());
        $this->assertSame(1, AsientosDetalle::count());
        $this->assertFalse(AsientosHeader::estaCuadrado($result['asiento_id']));
        $this->assertStringContainsString('Diferencia: 100.00', $result['msg']);
        AsientosHeader::recalcularAsientosBovedas(1);
        $this->assertSame(1, AsientosHeader::count());
        $this->assertCount(1, request()->attributes->get('asientos_descuadrados'));
    }

    public function testTransactionCommitsWithItsUnbalancedAutomaticEntry()
    {
        Company::create(['id' => 10, 'contabilidad' => 1, 'fecha_inicio_contable' => '2026-01-01']);
        Conceptos::create(['id' => 1, 'company_id' => 10, 'nombre' => 'DIARIO']);
        Conceptos::create(['id' => 2, 'company_id' => 10, 'nombre' => 'EGRESO']);
        TypeTransaction::create(['id' => 1, 'company_id' => 10, 'name_corto' => 'PRUEBA']);
        ConfigPlanHeader::where('id', 1)->update(['type_transaction_id' => 1, 'concepto_id' => 1]);
        ConfigPlanDetalle::query()->update(['campo_foraneo' => 'valor_movimiento']);
        DB::transaction(function () {
            $movimiento = CustomerMovimiento::create(['company_id' => 10, 'type_transaction_id' => 1,
                'valor_movimiento' => 100, 'banco_id' => 1, 'date_created' => '2026-10-13',
                'hour_created' => '12:00:00', 'status' => 1]);
            $result = AsientosHeader::recalcularAsientos($movimiento->id);
            $this->assertSame('200', $result['code']);
            $this->assertTrue($result['warning']);
        });
        $this->assertSame(1, CustomerMovimiento::count());
        $this->assertSame(1, AsientosHeader::count());
        $this->assertSame(1, AsientosDetalle::count());
        $this->assertEquals(100, AsientosHeader::totales(AsientosHeader::first()->id)['debe']);
    }

    public function testLivewireGetsWarningAlongsideExistingSuccessEvent()
    {
        $request = request();
        $request->headers->set('X-Livewire', 'true');
        $response = (new AdvertirAsientosDescuadrados)->handle($request, function () {
            AsientosHeader::recalcularAsientosBovedas(1);
            return new JsonResponse(['effects' => ['dispatches' => [['event' => 'alerta', 'data' => ['color' => 'success']]]]]);
        });
        $events = $response->getData(true)['effects']['dispatches'];
        $this->assertCount(2, $events);
        $this->assertSame('asiento-descuadrado', $events[1]['event']);
    }

    public function testRolledBackEntryDoesNotNotify()
    {
        $request = request();
        $request->headers->set('X-Livewire', 'true');
        $response = (new AdvertirAsientosDescuadrados)->handle($request, function () {
            DB::beginTransaction();
            AsientosHeader::recalcularAsientosBovedas(1);
            DB::rollBack();
            return new JsonResponse(['effects' => []]);
        });
        $this->assertArrayNotHasKey('dispatches', $response->getData(true)['effects']);
    }

    public function testRedirectKeepsWarningForNextPage()
    {
        $request = request();
        $request->setLaravelSession(app('session.store'));
        (new AdvertirAsientosDescuadrados)->handle($request, function () {
            AsientosHeader::recalcularAsientosBovedas(1);
            return redirect('/asientos');
        });
        $this->assertCount(1, $request->session()->get('asientos_descuadrados'));
    }

    public function testBalancedEntryHasNoWarning()
    {
        PlanCuentas::where('id', 2)->update(['banco_id' => 1]);
        $result = AsientosHeader::recalcularAsientosBovedas(1);
        $this->assertSame('200', $result['code']);
        $this->assertArrayNotHasKey('warning', $result);
        $this->assertTrue(AsientosHeader::estaCuadrado(AsientosHeader::first()->id));
        $this->assertEmpty(request()->attributes->get('asientos_descuadrados'));
    }

    private function configurarMovimiento()
    {
        Company::create(['id' => 10, 'contabilidad' => 1, 'fecha_inicio_contable' => '2026-01-01']);
        Conceptos::create(['id' => 1, 'company_id' => 10, 'nombre' => 'DIARIO']);
        Conceptos::create(['id' => 2, 'company_id' => 10, 'nombre' => 'EGRESO']);
        TypeTransaction::create(['id' => 1, 'company_id' => 10, 'name_corto' => 'PRUEBA']);
        ConfigPlanHeader::where('id', 1)->update(['type_transaction_id' => 1, 'concepto_id' => 1]);
        ConfigPlanDetalle::query()->update(['campo_foraneo' => 'valor_movimiento']);
        return ['company_id' => 10, 'type_transaction_id' => 1, 'valor_movimiento' => 100,
            'banco_id' => 1, 'date_created' => '2026-10-13', 'hour_created' => '12:00:00', 'status' => 1];
    }

    /** @dataProvider configuracionesFaltantes */
    public function testMissingConfigurationCreatesPendingEntryAndKeepsTransaction($caso)
    {
        $datos = $this->configurarMovimiento();
        if ($caso === 'conceptos') Conceptos::query()->delete();
        if ($caso === 'cabecera') ConfigPlanHeader::query()->delete();
        if ($caso === 'detalle') ConfigPlanDetalle::query()->delete();
        if ($caso === 'tipo') TypeTransaction::query()->delete();
        if ($caso === 'codigo') TypeTransaction::query()->update(['name_corto' => null]);
        if ($caso === 'cuenta') PlanCuentas::where('id', 2)->delete();
        if ($caso === 'campo') ConfigPlanDetalle::query()->update(['campo_foraneo' => 'campo_inexistente']);
        $movimiento = DB::transaction(function () use ($datos) { return CustomerMovimiento::create($datos); });
        $resultado = AsientosHeader::recalcularAsientos($movimiento->id);
        $this->assertSame('200', $resultado['code']);
        $this->assertTrue($resultado['warning']);
        $this->assertSame(1, CustomerMovimiento::count());
        $this->assertSame(1, AsientosHeader::count());
        $this->assertTrue(AsientosHeader::first()->pendiente_contable);
    }

    public function configuracionesFaltantes()
    {
        return array_map(function ($caso) { return [$caso]; }, ['conceptos', 'cabecera', 'detalle', 'tipo', 'codigo', 'cuenta', 'campo']);
    }

    public function testRecalculationRepairsPendingEntryWithoutDuplicatingIt()
    {
        $datos = $this->configurarMovimiento();
        ConfigPlanDetalle::query()->update(['status' => 0]);
        $movimiento = CustomerMovimiento::create($datos);
        $id = AsientosHeader::first()->id;
        ConfigPlanDetalle::query()->update(['status' => 1]);
        PlanCuentas::where('id', 2)->update(['banco_id' => 1]);
        $resultado = AsientosHeader::recalcularAsientos($movimiento->id);
        $this->assertSame('200', $resultado['code']);
        $this->assertArrayNotHasKey('warning', $resultado);
        $this->assertSame(1, AsientosHeader::count());
        $this->assertFalse(AsientosHeader::first()->pendiente_contable);
        $this->assertTrue(AsientosHeader::estaCuadrado($id));
    }

    public function testVaultMissingConfigurationCreatesPendingEntry()
    {
        ConfigPlanHeader::query()->delete();
        $resultado = AsientosHeader::recalcularAsientosBovedas(1);
        $this->assertSame('200', $resultado['code']);
        $this->assertTrue(AsientosHeader::first()->pendiente_contable);
        $this->assertSame('FINALIZADO', DescargoBovedasHeader::find(1)->estado);
        $this->assertStringContainsString('Bóvedas - Usuario:', AsientosHeader::first()->observacion);
        $this->assertStringContainsString('registro ID 1', AsientosHeader::first()->observacion);
    }

    public function testApprovedExpenseKeepsPendingAccounting()
    {
        $this->configurarMovimiento();
        $gasto = new Gastos;
        $gasto->forceFill(['company_id' => 10, 'estado' => 'APROBADO',
            'fecha_emision' => '2026-10-13', 'user_aprueba_id' => 1, 'valor' => 100])->save();
        $this->assertSame('APROBADO', $gasto->fresh()->estado);
        $this->assertStringContainsString('Gastos - Usuario:', AsientosHeader::first()->observacion);
        $this->assertStringContainsString('Gasto ID ' . $gasto->id, AsientosHeader::first()->observacion);
        $this->assertTrue(AsientosHeader::first()->pendiente_contable);
        $this->assertEquals($gasto->id, AsientosHeader::first()->gasto_id);
    }

    public function testInactiveAccountingDoesNotTurnIntoPaymentError()
    {
        $datos = $this->configurarMovimiento();
        Company::where('id', 10)->update(['contabilidad' => 0]);
        $movimiento = CustomerMovimiento::create($datos);
        $resultado = AsientosHeader::recalcularAsientos($movimiento->id);
        $this->assertSame('200', $resultado['code']);
        $this->assertTrue($resultado['skipped']);
        $this->assertSame(0, AsientosHeader::count());
    }

    public function testManualEntryCanBeSavedUnbalancedAndCorrected()
    {
        $this->configurarMovimiento();
        auth()->user()->setRelation('company', Company::find(10));
        $component = new \App\Http\Livewire\Asientos\AsientosComponent;
        $component->fecha = '2026-10-13';
        $component->concepto_id = 1;
        $component->descripcion = 'Asiento en revision';
        $component->asientos = [
            ['cuenta_id' => 1, 'sedes_centro_costos' => 1, 'debe_haber' => true, 'debe' => 100, 'haber' => 0],
            ['cuenta_id' => 2, 'sedes_centro_costos' => 1, 'debe_haber' => false, 'debe' => 0, 'haber' => 80],
        ];
        $component->store();
        $id = AsientosHeader::first()->id;
        $this->assertSame(2, AsientosDetalle::count());
        $this->assertFalse(AsientosHeader::estaCuadrado($id));
        $component->id_seleccionado = $id;
        $component->asientos[1]['haber'] = 100;
        $component->store();
        $this->assertSame(1, AsientosHeader::count());
        $this->assertTrue(AsientosHeader::estaCuadrado($id));
    }

    public function testInterestAccountingWithoutConfigurationKeepsAnEntryForReview()
    {
        $this->configurarMovimiento();
        DB::table('credit_folder_details')->insert(['company_id' => 10, 'code_folder_header' => 'CR001', 'interes_periodo' => 25]);
        $credito = new CreditFolderHeader(['code' => 'CR001', 'customer_name' => 'Ana']);
        $tipo = new Prestamos;
        $method = new \ReflectionMethod(\App\Http\Livewire\Creditos\CreditosComponet::class, 'contabilizarInteresesGeneradosCredito');
        $method->setAccessible(true);
        $component = new \App\Http\Livewire\Creditos\CreditosComponet;
        $method->invoke($component, $credito, $tipo, Company::find(10), '2026-10-13');
        $this->assertTrue(AsientosHeader::first()->pendiente_contable);
        $method->invoke($component, $credito, $tipo, Company::find(10), '2026-10-13');
        $this->assertSame(1, AsientosHeader::count());
    }

    public function testUtilitiesBatchWithoutConfigurationIsApprovedWithPendingEntries()
    {
        $this->configurarMovimiento();
        request()->setLaravelSession(app('session.store'));
        ConfigPlanHeader::query()->delete();
        TypeTransaction::where('id', 1)->update(['name_corto' => 'EUS', 'action' => 'S', 'afecta' => 'C']);
        Customer::create(['id' => 1, 'company_id' => 10, 'code' => 'CLI001', 'nombres' => 'Ana']);
        CustomerMovimientoSolicitud::create(['id' => 1, 'company_id' => 10, 'customer_id' => 1, 'estado' => 'PENDIENTE', 'valor' => 100]);
        $component = new \App\Http\Livewire\Utilidades\UtilidadesComponent;
        $component->aceptarMasivo('1');
        $this->assertSame('APROBADO', CustomerMovimientoSolicitud::find(1)->estado, json_encode(session()->all()));
        $this->assertEquals(100, CustomerMovimiento::sum('valor_movimiento'));
        $this->assertSame(2, AsientosHeader::count());
        foreach (AsientosHeader::all() as $asiento) $this->assertTrue($asiento->pendiente_contable);
        $this->assertSame(0, DB::transactionLevel());
    }

    public function testWebMiddlewareDeliversWarningDuringTheSameLivewireRequest()
    {
        \Illuminate\Support\Facades\Route::middleware('web')->post('/testing-asiento-pendiente', function () {
            AsientosHeader::recalcularAsientosBovedas(1);
            return response()->json(['effects' => [], 'serverMemo' => []]);
        });
        $this->postJson('/testing-asiento-pendiente', [], ['X-Livewire' => 'true'])
            ->assertOk()->assertJsonPath('effects.dispatches.0.event', 'asiento-descuadrado');
    }

    public function testPendingReasonIsKeptEvenWhenExistingAmountsBalance()
    {
        PlanCuentas::where('id', 2)->update(['banco_id' => 1]);
        AsientosHeader::recalcularAsientosBovedas(1);
        $asiento = AsientosHeader::first();
        $request = request();
        $request->headers->set('X-Livewire', 'true');
        $response = (new AdvertirAsientosDescuadrados)->handle($request, function () use ($asiento) {
            AsientosHeader::advertirRevision($asiento, 'La configuracion requiere revision.');
            return new JsonResponse(['effects' => []]);
        });
        $this->assertTrue(AsientosHeader::estaCuadrado($asiento->id));
        $this->assertSame('asiento-descuadrado', $response->getData(true)['effects']['dispatches'][0]['event']);
    }

    public function testPendingReasonFitsTheExistingDescriptionColumn()
    {
        AsientosHeader::recalcularAsientosBovedas(1);
        $asiento = AsientosHeader::first();
        $asiento->descripcion = str_repeat('Descripcion ', 25);
        AsientosHeader::advertirRevision($asiento, str_repeat('Motivo largo ', 30));
        $this->assertLessThanOrEqual(255, mb_strlen($asiento->fresh()->descripcion));
        $this->assertTrue($asiento->fresh()->pendiente_contable);
    }

    public function testEntryFromAnotherCompanyCannotBeRecalculated()
    {
        $datos = $this->configurarMovimiento();
        $datos['company_id'] = 20;
        DB::table('customer_movimientos')->insert(array_merge($datos, ['id' => 99]));
        $this->assertSame('404', AsientosHeader::recalcularAsientos(99)['code']);
        $this->assertSame(0, AsientosHeader::count());
    }

    public function testManualEntryFromModulesIsKeptUnbalanced()
    {
        $datos = $this->configurarMovimiento();
        Company::where('id', 10)->update(['contabilidad' => 0]);
        $movimiento = CustomerMovimiento::create($datos);
        Company::where('id', 10)->update(['contabilidad' => 1]);
        auth()->user()->setRelation('company', Company::find(10));
        $component = new \App\Http\Livewire\TodosModulos\TodosModulosComponent;
        $component->movimiento_manual_id = $movimiento->id;
        $component->fecha_manual = '2026-10-13';
        $component->concepto_id = 1;
        $component->descripcion = 'Revision manual desde modulos';
        $component->asientos = [
            ['cuenta_id' => 1, 'sedes_centro_costos' => 1, 'debe_haber' => true, 'debe' => 100, 'haber' => 0],
            ['cuenta_id' => 2, 'sedes_centro_costos' => 1, 'debe_haber' => false, 'debe' => 0, 'haber' => 50],
        ];
        $component->storeManual();
        $this->assertSame(1, AsientosHeader::count());
        $this->assertFalse(AsientosHeader::estaCuadrado(AsientosHeader::first()->id));
    }

    public function testModifiedViewsCompileAndInitialWarningsRenderSafely()
    {
        foreach (['layouts.js_library', 'livewire.asientos.asientos-component',
            'livewire.libro-mayor.libro-mayor-component', 'livewire.todos-modulos.todos-modulos-component'] as $view) {
            $path = app('view')->getFinder()->find($view);
            $compiled = app('blade.compiler')->compileString(file_get_contents($path));
            $temporary = tempnam(sys_get_temp_dir(), 'asientos_blade_');
            try {
                file_put_contents($temporary, $compiled);
                exec(escapeshellarg(PHP_BINARY) . ' -l ' . escapeshellarg($temporary), $output, $status);
                $this->assertSame(0, $status, $view . ': ' . implode('\n', $output));
            } finally {
                unlink($temporary);
            }
        }
        request()->setLaravelSession(app('session.store'));
        request()->attributes->set('asientos_descuadrados', [1 => 'Revisar </script> asiento']);
        $html = view('layouts.js_library')->render();
        $this->assertStringContainsString('window.advertenciasAsientos', $html);
        $this->assertStringNotContainsString('Revisar </script>', $html);
    }

    public function testGeneralEntryWithoutDetailsStaysPendingAndPreservesOtherEntries()
    {
        $datos = $this->configurarMovimiento();
        $movimiento = CustomerMovimiento::create($datos);
        $otroAsiento = AsientosHeader::first();
        $general = AsientosHeader::create(['company_id' => 10, 'manual' => 0,
            'descripcion' => 'Asiento general en revision', 'fecha_contable' => '2026-10-13',
            'fecha_creacion' => '2026-10-13', 'user_created' => 1, 'status' => 1]);
        $resultado = AsientosHeader::recalcularAsientos($general->id, 'customer_movimientos', true, TypeTransaction::find(1));
        $this->assertSame('200', $resultado['code']);
        $this->assertTrue($general->fresh()->pendiente_contable);
        $this->assertEquals($movimiento->id, $otroAsiento->fresh()->customer_movimiento_id);
        $this->assertSame(2, AsientosHeader::count());
        $this->assertSame(1, AsientosDetalle::where('asientos_header_id', $otroAsiento->id)->count());
    }
}
