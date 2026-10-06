<?php
namespace Tests\Feature;

use App\Http\Livewire\SolicitudPagosLiquidacion\SolicitudPagosLiquidacion;
use App\Models\{AsientosHeader, Bancos, Company, Conceptos, CreditFolderAudit, CreditFolderDetail, CreditFolderHeader, Customer, CustomerHistorial, CustomerMovimiento, Prestamos, RegistroFormasLiquidacion, TypeTransaction};
use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SolicitudPagosLiquidacionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'liquidation_payment_test', 'database.connections.liquidation_payment_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '']]);
        foreach ([AsientosHeader::class, Bancos::class, Company::class, Conceptos::class, CreditFolderAudit::class, CreditFolderDetail::class, CreditFolderHeader::class, Customer::class, CustomerHistorial::class, CustomerMovimiento::class, Prestamos::class, RegistroFormasLiquidacion::class, TypeTransaction::class] as $class) {
            $model = new $class;
            Schema::create($model->getTable(), function (Blueprint $t) use ($model) {
                $t->increments('id');
                foreach (array_unique(array_merge($model->getFillable(), $model instanceof CreditFolderDetail ? ['banco_id', 'numero_comprobante'] : [])) as $field) {
                    if ($field === 'id') continue;
                    if ($field === 'status' && !($model instanceof CreditFolderDetail) && !($model instanceof CreditFolderHeader)) $t->integer($field)->default(1);
                    else $t->string($field)->nullable();
                }
                $t->timestamps();
            });
        }
        Company::create(['id' => 10, 'contabilidad' => 0]);
        $user = new User; $user->id = 1; $user->company_id = 10; $user->username = 'Revisor';
        $user->setRelation('company', Company::find(10)); $this->actingAs($user);
        Customer::create(['id' => 1, 'company_id' => 10, 'code' => 'CLI001', 'nombres' => 'Ana', 'apellidos' => 'Prueba', 'numero_documento' => '00123']);
        CreditFolderHeader::create(['id' => 1, 'company_id' => 10, 'customer_id' => 1, 'code' => 'CR001', 'total_pagando' => 0, 'status' => 'ENTREGADO', 'cuotas_pagar' => 12, 'valor_solicitado' => 200]);
        TypeTransaction::create(['id' => 1, 'company_id' => 10, 'name' => 'Pago crédito', 'name_corto' => 'LIC', 'action' => 'S', 'afecta' => 'C']);
        Bancos::create(['id' => 1, 'company_id' => 10, 'nombre' => 'Banco de prueba']);
        $this->installment(1); $this->installment(2);
    }

    private function installment($id, array $data = [])
    {
        $detalle = new CreditFolderDetail;
        $detalle->forceFill(array_merge(['id' => $id, 'company_id' => 10, 'code_folder_header' => 'CR001', 'numero_cuota' => $id,
            'valor_cuota' => 100, 'capital_amortizado' => 100, 'fondo_desgravamen' => 0, 'interes_periodo' => 0,
            'interes_mora' => 0, 'valor_pagado' => 0, 'status' => 'PENDIENTE'], $data))->save();
        return $detalle;
    }

    private function payment($value = 200, $form = 'TRANSFERENCIA', array $data = [])
    {
        return RegistroFormasLiquidacion::create(array_merge(['company_id' => 10, 'customer_id' => 1, 'liquidacion_id' => 1,
            'forma_pago' => $form, 'forma_pago_id' => $form === 'EFECTIVO' ? 1 : 3, 'banco_id' => $form === 'EFECTIVO' ? null : 1,
            'numero_comprobante' => $form === 'EFECTIVO' ? null : 'TR001', 'valor' => $value, 'status' => 1, 'solicitado' => 3,
            'fecha_solicitud' => '2026-10-06', 'hora_solicitud' => '10:00:00', 'user_name' => 'Cajero'], $data));
    }

    private function approve($component = null)
    {
        ($component ?: new SolicitudPagosLiquidacion)->aprobarSolicitud(1, '2026-10-06', '10:00:00');
    }

    private function reject($component = null)
    {
        $component = $component ?: new SolicitudPagosLiquidacion;
        $component->observacionRechazo = 'Comprobante incorrecto';
        $component->rechazarSolicitud(1, '2026-10-06', '10:00:00');
    }

    public function testApprovalPostsAllPaymentsAndLinksCreditCustomerAndBank()
    {
        $transfer = $this->payment(120); $cash = $this->payment(80, 'EFECTIVO'); $this->approve();
        $this->assertEquals(1, $transfer->fresh()->solicitado); $this->assertEquals(1, $cash->fresh()->solicitado);
        $this->assertSame('PAGADO', CreditFolderHeader::find(1)->status); $this->assertEquals(200, CreditFolderHeader::find(1)->total_pagando);
        $this->assertEquals(2, CreditFolderDetail::where('status', 'PAGADA')->count());
        $this->assertEquals(200, CustomerMovimiento::sum('valor_movimiento')); $this->assertEquals(200, CustomerHistorial::sum('valor_movimiento'));
        $mov = CustomerMovimiento::find($transfer->fresh()->customer_movimientos_id);
        $this->assertEquals(1, $mov->customer_id); $this->assertEquals(1, $mov->credit_folder_header_id);
        $this->assertEquals(1, $mov->banco_id); $this->assertSame('TR001', $mov->comprobante);
        $this->assertNotNull($transfer->fresh()->fecha_aprobacion);
    }

    public function testApprovalCannotBeRepeated()
    {
        $this->payment(); $this->approve();
        try { $this->approve(); $this->fail('Repeated approval accepted'); }
        catch (ValidationException $e) { $this->assertEquals(200, CreditFolderHeader::find(1)->total_pagando); $this->assertSame(1, CustomerMovimiento::count()); }
    }

    public function testRejectionOfTransferOnlyDoesNotRequireLicOrChangeCredit()
    {
        $pago = $this->payment(); TypeTransaction::query()->delete(); $this->reject();
        $this->assertEquals(2, $pago->fresh()->solicitado); $this->assertStringContainsString('Comprobante incorrecto', $pago->fresh()->observacion);
        $this->assertSame('ENTREGADO', CreditFolderHeader::find(1)->status); $this->assertEquals(0, CreditFolderHeader::find(1)->total_pagando);
        $this->assertSame(0, CustomerMovimiento::count());
    }

    public function testMixedRejectionAppliesCashWithoutClosingCreditWithUnpaidInstallments()
    {
        CreditFolderHeader::where('id', 1)->update(['valor_solicitado' => 50]);
        $transfer = $this->payment(130); $cash = $this->payment(70, 'EFECTIVO'); $this->reject();
        $this->assertEquals(2, $transfer->fresh()->solicitado); $this->assertEquals(1, $cash->fresh()->solicitado);
        $this->assertEquals(70, CreditFolderDetail::find(1)->valor_pagado); $this->assertEquals(30, CreditFolderDetail::find(1)->saldo_remanente);
        $this->assertSame('PENDIENTE', CreditFolderDetail::find(1)->status); $this->assertSame('ENTREGADO', CreditFolderHeader::find(1)->status);
        $this->assertEquals(70, CustomerMovimiento::sum('valor_movimiento')); $this->assertEquals(70, CustomerHistorial::sum('valor_movimiento'));
    }

    public function testCashAbonoIsDistributedOverSeveralInstallments()
    {
        $this->payment(50); $this->payment(150, 'EFECTIVO'); $this->reject();
        $this->assertSame('PAGADA', CreditFolderDetail::find(1)->status); $this->assertEquals(50, CreditFolderDetail::find(2)->valor_pagado);
        $this->assertSame('PENDIENTE', CreditFolderDetail::find(2)->status);
    }

    public function testRejectionRequiresReason()
    {
        $this->payment(); $component = new SolicitudPagosLiquidacion; $component->observacionRechazo = '  ';
        $this->expectException(ValidationException::class); $component->rechazarSolicitud(1, '2026-10-06', '10:00:00');
    }

    public function testFailureRollsBackApproval()
    {
        $pago = $this->payment(); Schema::drop('customer_historials');
        try { $this->approve(); $this->fail('Expected failure'); }
        catch (ValidationException $e) {
            $this->assertEquals(3, $pago->fresh()->solicitado); $this->assertSame('PENDIENTE', CreditFolderDetail::find(1)->status);
            $this->assertEquals(0, CreditFolderHeader::find(1)->total_pagando); $this->assertSame(0, CustomerMovimiento::count());
        }
    }

    public function testFailureRollsBackRejectionAndCashAbono()
    {
        $transfer = $this->payment(100); $cash = $this->payment(100, 'EFECTIVO'); Schema::drop('customer_historials');
        try { $this->reject(); $this->fail('Expected failure'); }
        catch (ValidationException $e) {
            $this->assertEquals(3, $transfer->fresh()->solicitado); $this->assertEquals(3, $cash->fresh()->solicitado);
            $this->assertEquals(0, CreditFolderDetail::find(1)->valor_pagado); $this->assertSame(0, CustomerMovimiento::count());
        }
    }

    public function testAccountingFailureRollsBackPayment()
    {
        Company::where('id', 10)->update(['contabilidad' => 1, 'fecha_inicio_contable' => '2020-01-01']);
        auth()->user()->setRelation('company', Company::find(10)); $pago = $this->payment();
        try { $this->approve(); $this->fail('Accounting error accepted'); }
        catch (ValidationException $e) { $this->assertEquals(3, $pago->fresh()->solicitado); $this->assertSame('PENDIENTE', CreditFolderDetail::find(1)->status); $this->assertSame(0, CustomerMovimiento::count()); }
    }

    public function testStaleSettlementAmountIsNotApproved()
    {
        $pago = $this->payment(150);
        try { $this->approve(); $this->fail('Wrong amount accepted'); }
        catch (ValidationException $e) { $this->assertEquals(3, $pago->fresh()->solicitado); $this->assertSame(0, CustomerMovimiento::count()); }
    }

    public function testInterestDiscountUsesOriginalSettlementRule()
    {
        CreditFolderDetail::where('id', 1)->update(['interes_periodo' => 10, 'fondo_desgravamen' => 2]);
        CreditFolderDetail::where('id', 2)->update(['numero_cuota' => 7, 'interes_periodo' => 10, 'fondo_desgravamen' => 2]);
        $this->payment(214); $this->approve();
        $this->assertEquals(112, CreditFolderDetail::find(1)->valor_pagado); $this->assertEquals(102, CreditFolderDetail::find(2)->valor_pagado);
    }

    public function testDuplicateBankProofIsRefused()
    {
        $this->installment(3, ['status' => 'PAGADA', 'banco_id' => 1, 'numero_comprobante' => 'TR001']); $pago = $this->payment();
        try { $this->approve(); $this->fail('Duplicate proof accepted'); }
        catch (ValidationException $e) { $this->assertEquals(3, $pago->fresh()->solicitado); $this->assertSame(0, CustomerMovimiento::count()); }
    }

    public function testRequestFromAnotherCompanyCannotBeApproved()
    {
        $pago = $this->payment(200, 'TRANSFERENCIA', ['company_id' => 20]);
        try { $this->approve(); $this->fail('Foreign request accepted'); }
        catch (ValidationException $e) { $this->assertEquals(3, $pago->fresh()->solicitado); $this->assertSame(0, CustomerMovimiento::count()); }
    }

    public function testForeignCreditDetailsAreNotAccessible()
    {
        CreditFolderHeader::where('id', 1)->update(['company_id' => 20]);
        $this->expectException(ModelNotFoundException::class); (new SolicitudPagosLiquidacion)->cargarDatosLiquidacion(1, '2026-10-06', '10:00:00');
    }

    public function testOnlySelectedBatchIsProcessed()
    {
        $old = $this->payment(200, 'TRANSFERENCIA', ['hora_solicitud' => '09:00:00', 'solicitado' => 2]);
        $current = $this->payment(); $this->approve();
        $this->assertEquals(2, $old->fresh()->solicitado); $this->assertNull($old->fresh()->customer_movimientos_id); $this->assertEquals(1, $current->fresh()->solicitado);
    }

    public function testListSearchStateGroupingAndCompanyScope()
    {
        $this->payment(100); $this->payment(100, 'EFECTIVO', ['user_name' => 'Otro cajero']);
        $this->payment(500, 'TRANSFERENCIA', ['company_id' => 20]); $component = new SolicitudPagosLiquidacion;
        $rows = $component->render()->getData()['liquidaciones']; $this->assertSame(1, $rows->total()); $this->assertEquals(200, $rows->first()->valor_total);
        $component->buscarCedula = 'CR001'; $this->assertSame(1, $component->render()->getData()['liquidaciones']->total());
        $component->estadoFiltro = '1'; $this->assertSame(0, $component->render()->getData()['liquidaciones']->total());
        $component->estadoFiltro = '3'; $this->assertSame(1, $component->render()->getData()['liquidaciones']->total());
    }

    public function testHistoryIncludesMixedRejectedBatchWithoutSplittingIt()
    {
        $this->payment(100); $this->payment(100, 'EFECTIVO'); $this->reject(); $component = new SolicitudPagosLiquidacion;
        $component->estadoFiltro = '2'; $rows = $component->render()->getData()['liquidaciones'];
        $this->assertSame(1, $rows->total()); $this->assertEquals(200, $rows->first()->valor_total);
        $component->cargarDatosLiquidacion(1, '2026-10-06', '10:00:00'); $this->assertEquals(2, $component->registro['estado_solicitud']);
    }

    public function testPendingCashOnlyIsNotShownAsApproved()
    {
        $this->payment(200, 'EFECTIVO'); $component = new SolicitudPagosLiquidacion;
        $this->assertEquals(3, $component->render()->getData()['liquidaciones']->first()->estado_solicitud);
    }

    public function testLivewireRendersModalFooterAndRejectionValidation()
    {
        $this->payment();
        \Livewire\Livewire::test(SolicitudPagosLiquidacion::class)->assertSee('Liquidaciones registradas')
            ->call('cargarDatosLiquidacion', 1, '2026-10-06', '10:00:00')->assertSee('Aprobar liquidación')
            ->call('abrirFormularioRechazo')->assertSee('Cancelar rechazo')->assertSee('Confirmar rechazo')
            ->call('rechazarSolicitud', 1, '2026-10-06', '10:00:00')->assertHasErrors('observacionRechazo');
    }

    public function testModalKeepsClientAndActionsAcrossLivewireRequests()
    {
        $this->payment();
        \Livewire\Livewire::test(SolicitudPagosLiquidacion::class)
            ->call('cargarDatosLiquidacion', 1, '2026-10-06', '10:00:00')
            ->call('abrirFormularioRechazo')->assertSee('Ana')->assertSee('CR001')
            ->set('mostrarFormularioRechazo', false)->assertSee('Aprobar liquidación')
            ->call('aprobarSolicitud', 1, '2026-10-06', '10:00:00')->assertHasNoErrors()
            ->assertDispatchedBrowserEvent('close-modal-liquidacion');
        $this->assertSame('PAGADO', CreditFolderHeader::find(1)->status);
    }

    public function testModalRejectsWithReasonAfterHydration()
    {
        $payment = $this->payment();
        \Livewire\Livewire::test(SolicitudPagosLiquidacion::class)
            ->call('cargarDatosLiquidacion', 1, '2026-10-06', '10:00:00')->call('abrirFormularioRechazo')
            ->set('observacionRechazo', 'Transferencia no recibida')
            ->call('rechazarSolicitud', 1, '2026-10-06', '10:00:00')->assertHasNoErrors();
        $this->assertEquals(2, $payment->fresh()->solicitado);
    }

    public function testBankFromAnotherCompanyIsRefused()
    {
        Bancos::where('id', 1)->update(['company_id' => 20]); $payment = $this->payment();
        try { $this->approve(); $this->fail('Foreign bank accepted'); }
        catch (ValidationException $e) { $this->assertEquals(3, $payment->fresh()->solicitado); $this->assertSame(0, CustomerMovimiento::count()); }
    }

    public function testTransferRequiresBankProof()
    {
        $payment = $this->payment(200, 'TRANSFERENCIA', ['numero_comprobante' => null]);
        try { $this->approve(); $this->fail('Missing proof accepted'); }
        catch (ValidationException $e) { $this->assertEquals(3, $payment->fresh()->solicitado); $this->assertSame(0, CustomerMovimiento::count()); }
    }

    public function testCompanyBalanceExcludesOtherCompanies()
    {
        CustomerHistorial::create(['company_id' => 20, 'status' => 1, 'customer_movimiento_code' => 'OTHER001', 'type_transaction_action' => 'S', 'valor_movimiento' => 5000]);
        $this->payment(); $this->approve();
        $this->assertEquals(200, CustomerMovimiento::first()->saldo_general);
    }

    public function testExcessCashAbonoCannotBeLostOnRejection()
    {
        $transfer = $this->payment(10); $cash = $this->payment(210, 'EFECTIVO');
        try { $this->reject(); $this->fail('Excess payment silently lost'); }
        catch (ValidationException $e) {
            $this->assertEquals(3, $transfer->fresh()->solicitado); $this->assertEquals(3, $cash->fresh()->solicitado);
            $this->assertEquals(0, CreditFolderDetail::find(1)->valor_pagado);
        }
    }
}
