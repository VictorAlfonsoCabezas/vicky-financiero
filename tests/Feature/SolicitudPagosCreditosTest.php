<?php
namespace Tests\Feature;

use App\Http\Livewire\SolicitudPagosCreditos\SolicitudPagosCreditos;
use App\Models\{AsientosHeader, Bancos, Company, Conceptos, CreditFolderAudit, CreditFolderDetail, CreditFolderHeader, Customer, CustomerHistorial, CustomerMovimiento, Prestamos, RegistroFormasPago, TypeTransaction};
use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SolicitudPagosCreditosTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'credit_payment_test', 'database.connections.credit_payment_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '']]);
        foreach ([AsientosHeader::class, Bancos::class, Company::class, Conceptos::class, CreditFolderAudit::class, CreditFolderDetail::class, CreditFolderHeader::class, Customer::class, CustomerHistorial::class, CustomerMovimiento::class, Prestamos::class, RegistroFormasPago::class, TypeTransaction::class] as $class) {
            $model = new $class;
            Schema::create($model->getTable(), function (Blueprint $t) use ($model) {
                $t->increments('id');
                foreach (array_unique($model->getFillable()) as $field) {
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
        CreditFolderHeader::create(['id' => 1, 'company_id' => 10, 'customer_id' => 1, 'code' => 'CR001', 'total_pagando' => 0, 'status' => 'APROBADO']);
        TypeTransaction::create(['id' => 1, 'company_id' => 10, 'name' => 'Pago crédito', 'name_corto' => 'PC', 'action' => 'S', 'afecta' => 'C']);
        Bancos::create(['id' => 1, 'company_id' => 10, 'nombre' => 'Banco de prueba']);
        $this->installment(1);
    }

    private function installment($id, array $data = [])
    {
        return CreditFolderDetail::create(array_merge(['id' => $id, 'company_id' => 10, 'code_folder_header' => 'CR001', 'numero_cuota' => $id,
            'valor_cuota' => 100, 'interes_mora' => 0, 'saldo_anterior_cuota' => 0, 'faltante_anterior_cuota' => 0,
            'faltante_prox_cuota' => 0, 'valor_pagado' => 0, 'status' => 'PENDIENTE'], $data));
    }

    private function payment($value = 100, $form = 'TRANSFERENCIA', array $data = [])
    {
        return RegistroFormasPago::create(array_merge(['company_id' => 10, 'customer_id' => 1, 'letra_id' => 1, 'prestamo_id' => 1,
            'forma_pago' => $form, 'forma_pago_id' => $form === 'EFECTIVO' ? 1 : 3, 'banco_id' => $form === 'EFECTIVO' ? null : 1,
            'valor' => $value, 'status' => 3, 'solicitado' => 3, 'date_create' => '2026-10-06', 'user_name' => 'Cajero'], $data));
    }

    public function testApprovePostsPaymentHistoryAndCannotRepeat()
    {
        $payment = $this->payment(); $component = new SolicitudPagosCreditos;
        $component->aprobarSolicitud(1);
        $this->assertSame('PAGADA', CreditFolderDetail::find(1)->status);
        $this->assertEquals(100, CreditFolderHeader::find(1)->total_pagando);
        $this->assertEquals(1, $payment->fresh()->status);
        $this->assertEquals(100, CustomerMovimiento::sum('valor_movimiento'));
        $this->assertEquals(100, CustomerHistorial::sum('valor_movimiento'));
        $this->assertSame(1, CustomerMovimiento::count());
        try { $component->aprobarSolicitud(1); $this->fail('Repeated payment accepted'); }
        catch (ValidationException $e) { $this->assertEquals(100, CreditFolderHeader::find(1)->total_pagando); }
    }

    public function testRejectTransferReturnsInstallmentToPending()
    {
        $payment = $this->payment(); $component = new SolicitudPagosCreditos;
        $component->rechazarSolicitud(1);
        $this->assertEquals(2, $payment->fresh()->status);
        $this->assertSame('PENDIENTE', CreditFolderDetail::find(1)->status);
        $this->assertSame(0, CustomerMovimiento::count());
    }

    public function testMixedRejectionRetainsOnlyCashAndCarriesShortfall()
    {
        $transfer = $this->payment(70); $cash = $this->payment(30, 'EFECTIVO'); $this->installment(2);
        (new SolicitudPagosCreditos)->rechazarSolicitud(1);
        $this->assertEquals(2, $transfer->fresh()->status); $this->assertEquals(1, $cash->fresh()->status);
        $this->assertEquals(30, CustomerMovimiento::sum('valor_movimiento'));
        $this->assertEquals(30, CreditFolderHeader::find(1)->total_pagando);
        $this->assertEquals(70, CreditFolderDetail::find(2)->faltante_anterior_cuota);
    }

    public function testApprovalFailureRollsBackRequestAndInstallment()
    {
        $payment = $this->payment(); Schema::drop('customer_historials');
        try { (new SolicitudPagosCreditos)->aprobarSolicitud(1); $this->fail('Expected failure'); }
        catch (ValidationException $e) {
            $this->assertSame('PENDIENTE', CreditFolderDetail::find(1)->status);
            $this->assertEquals(3, $payment->fresh()->status);
            $this->assertEquals(3, $payment->fresh()->solicitado);
            $this->assertEquals(0, CreditFolderHeader::find(1)->total_pagando);
            $this->assertSame(0, CustomerMovimiento::count());
        }
    }

    public function testAccountingErrorRollsBackApproval()
    {
        Company::where('id', 10)->update(['contabilidad' => 1, 'fecha_inicio_contable' => '2020-01-01']);
        auth()->user()->setRelation('company', Company::find(10));
        $payment = $this->payment();
        try { (new SolicitudPagosCreditos)->aprobarSolicitud(1); $this->fail('Missing accounting configuration accepted'); }
        catch (ValidationException $e) {
            $this->assertEquals(3, $payment->fresh()->status);
            $this->assertSame('PENDIENTE', CreditFolderDetail::find(1)->status);
            $this->assertSame(0, CustomerMovimiento::count());
        }
    }

    public function testMoraUsesFreshInstallmentAndResetsBetweenSelections()
    {
        CreditFolderDetail::where('id', 1)->update(['interes_mora' => 10]); $this->payment(110); $this->installment(2);
        $component = new SolicitudPagosCreditos; $component->interesMoraOriginal = 999;
        $component->aprobarSolicitud(1);
        $this->assertEquals(10, CreditFolderDetail::find(1)->interes_mora);
        $this->assertEquals(0, CreditFolderDetail::find(2)->saldo_anterior_cuota);
    }

    public function testWaivingMoraPreservesMoneyAndAdvancesSurplus()
    {
        CreditFolderDetail::where('id', 1)->update(['interes_mora' => 10]); $this->payment(110); $this->installment(2);
        $component = new SolicitudPagosCreditos; $component->aplicarInteresMora = false; $component->aprobarSolicitud(1);
        $this->assertEquals(0, CreditFolderDetail::find(1)->interes_mora);
        $this->assertEquals(10, CreditFolderDetail::find(2)->saldo_anterior_cuota);
        $this->assertEquals(110, CreditFolderHeader::find(1)->total_pagando);
        $this->assertEquals(110, CustomerMovimiento::sum('valor_movimiento'));
    }

    public function testForeignCompanyCannotApproveOrLoadDetails()
    {
        $this->installment(2, ['company_id' => 20]);
        $this->expectException(ModelNotFoundException::class);
        (new SolicitudPagosCreditos)->aprobarSolicitud(2);
    }

    public function testScreenFiltersAndDetailsIgnoreHistoricalPayments()
    {
        $this->payment(90); $this->payment(30, 'EFECTIVO', ['status' => 1, 'solicitado' => 1]);
        $this->payment(500, 'TRANSFERENCIA', ['company_id' => 20]);
        $component = new SolicitudPagosCreditos;
        $this->assertSame(1, $component->render()->getData()['letras']->total());
        $component->cargarDatosPago(1); $this->assertEquals(90, $component->totalRecibido);
        $component->buscarCedula = 'NO EXISTE'; $this->assertSame(0, $component->render()->getData()['letras']->total());
        $component->page = 2; $component->updatingEstadoFiltro(); $this->assertSame(1, $component->page);
    }

    public function testFailedMixedRejectionRollsBackBothPaymentMethods()
    {
        $transfer = $this->payment(70); $cash = $this->payment(30, 'EFECTIVO');
        Schema::drop('customer_historials');
        try { (new SolicitudPagosCreditos)->rechazarSolicitud(1); $this->fail('Expected failure'); }
        catch (ValidationException $e) {
            $this->assertEquals(3, $transfer->fresh()->status);
            $this->assertEquals(3, $cash->fresh()->status);
            $this->assertEquals(3, $transfer->fresh()->solicitado);
            $this->assertSame('PENDIENTE', CreditFolderDetail::find(1)->status);
        }
    }

    public function testForeignDetailIsNotAccessible()
    {
        $this->installment(2, ['company_id' => 20]);
        $this->expectException(ModelNotFoundException::class);
        (new SolicitudPagosCreditos)->cargarDatosPago(2);
    }

    public function testSelectingAnotherInstallmentResetsLateInterestChoice()
    {
        CreditFolderDetail::where('id', 1)->update(['interes_mora' => 10]);
        $this->payment(110); $this->installment(2);
        $this->payment(100, 'TRANSFERENCIA', ['letra_id' => 2]);
        $component = new SolicitudPagosCreditos;
        $component->cargarDatosPago(1); $this->assertTrue($component->aplicarInteresMora);
        $component->cargarDatosPago(2); $this->assertFalse($component->aplicarInteresMora);
        $this->assertEquals(0, $component->interesMoraOriginal);
    }

    public function testRejectedPaymentsAppearInHistoryFilter()
    {
        $this->payment(); $component = new SolicitudPagosCreditos;
        $component->rechazarSolicitud(1); $component->estadoFiltro = '2';
        $this->assertSame(1, $component->render()->getData()['letras']->total());
        $component->cargarDatosPago(1); $this->assertEquals(2, $component->registro->status);
    }
    public function testApprovedPaymentsRemainVisibleInHistory()
    {
        $this->payment(); $component = new SolicitudPagosCreditos;
        $component->aprobarSolicitud(1); $component->estadoFiltro = '1';
        $this->assertSame(1, $component->render()->getData()['letras']->total());
        $component->cargarDatosPago(1); $this->assertEquals(1, $component->registro->status);
    }

    public function testOwnProofCanBeOpened()
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $path = 'uploads/comprobantes_pagos/test-proof.txt';
        \Illuminate\Support\Facades\Storage::disk('public')->put($path, 'Test proof');
        CreditFolderDetail::where('id', 1)->update(['path' => $path]);
        $response = (new \App\Http\Controllers\SolicitudPagosCreditos\SolicitudPagosCreditosController)->comprobante(1);
        $this->assertSame('Test proof', file_get_contents($response->getFile()->getPathname()));
    }

    public function testMissingOrUnsafeProofIsRejected()
    {
        CreditFolderDetail::where('id', 1)->update(['path' => '../secret.txt']);
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        (new \App\Http\Controllers\SolicitudPagosCreditos\SolicitudPagosCreditosController)->comprobante(1);
    }
    public function testLivewireRendersReviewModalAndConfirmationControls()
    {
        $this->payment();
        \Livewire\Livewire::test(SolicitudPagosCreditos::class)->assertSee('Revisar pago')
            ->call('cargarDatosPago', 1)->assertSee('Aprobar pago')->assertSee('Rechazar pagos no efectivos');
    }
}
