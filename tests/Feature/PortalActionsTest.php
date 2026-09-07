<?php

namespace Tests\Feature;

use App\User;
use App\Models\{Customer, CustomerTipoAhorros, CustomerMovimiento, CustomerHistorial, Company, TypeTransaction, CartolaHeader, CartolaDetail, CustomerMovimientoSolicitud, Bancos, CreditFolderHeader, CreditFolderDetail, CreditFiles, RegistroFormasPago, FormasPago, Prestamos, RecurrenciaPrestamos, TerminosUso, TerminosUsoClientes};
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema, Storage, Hash};
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Tests\TestCase;

class PortalActionsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'portal_actions', 'database.connections.portal_actions' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        foreach ([Customer::class, CustomerTipoAhorros::class, CustomerMovimiento::class, CustomerHistorial::class,
            Company::class, TypeTransaction::class, CartolaHeader::class, CartolaDetail::class,
            CustomerMovimientoSolicitud::class, Bancos::class, CreditFolderHeader::class, CreditFolderDetail::class,
            CreditFiles::class, RegistroFormasPago::class, FormasPago::class, Prestamos::class,
            RecurrenciaPrestamos::class, TerminosUso::class, TerminosUsoClientes::class, \App\Models\CreditFolderAudit::class] as $modelClass) {
            $model = new $modelClass();
            Schema::create($model->getTable(), function (Blueprint $t) use ($model) {
                $t->increments('id');
                foreach (array_unique(array_map('trim', $model->getFillable())) as $field) {
                    if (!in_array($field, ['id', 'created_at', 'updated_at'])) $t->string($field)->nullable();
                }
                $t->timestamps();
            });
        }
        Schema::create('users', function (Blueprint $t) {
            $t->increments('id'); $t->integer('company_id'); $t->boolean('status');
            foreach (['username', 'firstname', 'lastname', 'password', 'token', 'remember_token'] as $field) $t->string($field)->nullable();
            $t->timestamps();
        });
        Schema::table('credit_folder_details', function (Blueprint $t) {
            $t->integer('banco_id')->nullable(); $t->string('numero_comprobante')->nullable();
        });
        DB::table('users')->insert(['id' => 1, 'company_id' => 1, 'status' => true, 'username' => 'cliente',
            'firstname' => 'Ana', 'lastname' => 'Uno', 'password' => bcrypt('secreto123'), 'token' => 'legacy']);
        Company::create(['id' => 1, 'company_name' => 'Caja']);
        foreach ([[1, 1, 1, 'Ana'], [2, 1, 2, 'Bea'], [3, 2, 3, 'Otra caja']] as $data) {
            Customer::create(['id' => $data[0], 'company_id' => $data[1], 'user_id' => $data[2],
                'nombres' => $data[3], 'apellidos' => 'Socio', 'code' => 'C' . $data[0], 'status' => true]);
            CustomerTipoAhorros::create(['id' => $data[0], 'company_id' => $data[1], 'customer_id' => $data[0], 'codigo' => 'A' . $data[0], 'status' => true]);
            Bancos::create(['id' => $data[0], 'company_id' => $data[1], 'nombre' => 'Banco', 'tipo_cuenta_id' => 1, 'status' => true]);
            CreditFolderHeader::create(['id' => $data[0], 'company_id' => $data[1], 'customer_id' => $data[0], 'code' => 'CR' . $data[0], 'status' => 'ENTREGADO', 'valor_solicitado' => 100]);
            CreditFolderDetail::create(['id' => $data[0], 'company_id' => $data[1], 'code_folder_header' => 'CR' . $data[0],
                'valor_cuota' => 50, 'status' => 'PENDIENTE', 'date_vencimiento' => date('Y-m-d', strtotime('+10 days'))]);
        }
        foreach ([[1, 'IN', 'S'], [2, 'TRE', 'R'], [3, 'TRR', 'S']] as $type) TypeTransaction::create([
            'id' => $type[0], 'company_id' => 1, 'name_corto' => $type[1], 'name' => $type[1], 'action' => $type[2], 'afecta' => 'C', 'status' => true]);
        CustomerMovimiento::create(['code' => 'INITIAL', 'company_id' => 1, 'customer_id' => 1, 'customer_tipo_ahorro_id' => 1,
            'type_transaction_id' => 1, 'type_transaction_name' => 'IN', 'type_transaction_action' => 'S', 'valor_movimiento' => 100, 'status' => true, 'date_created' => date('Y-m-d')]);
        $this->actingAs(User::find(1));
    }

    private function transferInput()
    {
        return ['cuenta' => 1, 'destino' => 'A2', 'valor' => '25.10', 'descripcion' => 'Prueba',
            'operacion' => (string) Str::uuid(), 'confirmar' => 1];
    }

    public function testTransferPostsBothLegsAndRetryDoesNotDebitTwice()
    {
        $input = $this->transferInput();
        $this->post('/mi-cuenta/transferencias', $input)->assertRedirect('/mi-cuenta/transferencias')->assertSessionHasNoErrors();
        $this->post('/mi-cuenta/transferencias', $input)->assertRedirect('/mi-cuenta/transferencias')->assertSessionHasNoErrors();
        $this->assertSame(3, CustomerMovimiento::count());
        $this->assertSame(2, CustomerHistorial::count());
        $this->assertSame(2, CartolaDetail::count());
        $service = app(\App\Services\PortalTransfer::class);
        $this->assertSame(7490, $service->balance(CustomerTipoAhorros::find(1)));
        $this->assertSame(2510, $service->balance(CustomerTipoAhorros::find(2)));
        $this->get('/mi-cuenta/transferencias')->assertOk()->assertSee('74.90');
        $this->get('/mi-cuenta/cuentas/1/movimientos')->assertOk()->assertSee('Prueba');
    }

    public function testTransferRejectsForeignAccountsOverdraftAndInvalidAmounts()
    {
        $input = $this->transferInput();
        $this->post('/mi-cuenta/transferencias', array_merge($input, ['cuenta' => 2]))->assertNotFound();
        $this->post('/mi-cuenta/transferencias', array_merge($input, ['destino' => 'A3']))->assertNotFound();
        foreach (['101', '-5', '0', '1.001'] as $amount) {
            $this->post('/mi-cuenta/transferencias', array_merge($input, ['valor' => $amount]))->assertSessionHasErrors('valor');
        }
        $this->assertSame(1, CustomerMovimiento::count());
        $this->get('/mi-cuenta/cuentas/2/movimientos')->assertNotFound();
        $this->get('/mi-cuenta/prestamos/2')->assertNotFound();
        $this->post('/livewire/message/customer-transferencias.customer-transferencias-component', [])->assertForbidden();
    }

    public function testTransferRollsBackWhenSecondLedgerWriteFails()
    {
        CartolaDetail::creating(function () { throw new \RuntimeException('Test rollback'); });
        try {
            app(\App\Services\PortalTransfer::class)->send(Customer::find(1), User::find(1), $this->transferInput());
            $this->fail('Expected simulated persistence failure');
        } catch (\RuntimeException $error) {
            $this->assertSame('Test rollback', $error->getMessage());
        } finally { CartolaDetail::flushEventListeners(); }
        $this->assertSame(1, CustomerMovimiento::count());
        $this->assertSame(0, CustomerHistorial::count());
        $this->assertSame(0, CartolaHeader::count());
    }

    public function testAccreditationCanBeCreatedEditedWithoutReplacingFileAndDeletedOnlyWhilePending()
    {
        Storage::fake('local');
        $input = ['cuenta' => 1, 'banco' => 1, 'valor' => '10.00', 'numero_deposito' => '1234',
            'archivo' => UploadedFile::fake()->create('deposito.pdf', 20, 'application/pdf')];
        $this->post('/mi-cuenta/acreditaciones', $input)->assertSessionHasNoErrors()->assertRedirect('/mi-cuenta/acreditaciones');
        $row = CustomerMovimientoSolicitud::first();
        $this->assertEquals(1, $row->customer_id);
        $this->assertSame('PENDIENTE', $row->estado);
        $path = $row->archivo;
        unset($input['archivo']); $input['id'] = $row->id; $input['valor'] = '12.00';
        $this->post('/mi-cuenta/acreditaciones', $input)->assertSessionHasNoErrors();
        $this->assertSame($path, $row->fresh()->archivo);
        $this->get('/mi-cuenta/acreditaciones?editar=' . $row->id)->assertOk();
        $row->estado = 'APROBADA'; $row->save();
        $this->delete('/mi-cuenta/acreditaciones/' . $row->id)->assertNotFound();
        $this->post('/mi-cuenta/acreditaciones', $input)->assertNotFound();
        $row->estado = 'PENDIENTE'; $row->customer_id = 2; $row->save();
        $this->delete('/mi-cuenta/acreditaciones/' . $row->id)->assertNotFound();
        $this->get('/mi-cuenta/acreditaciones/' . $row->id . '/comprobante')->assertNotFound();
        $row->customer_id = 1; $row->save();
        $this->delete('/mi-cuenta/acreditaciones/' . $row->id)->assertRedirect('/mi-cuenta/acreditaciones');
        $this->assertSame(0, CustomerMovimientoSolicitud::count());
    }

    public function testCreditProofRequiresOwnPendingInstallmentAndNeverMarksItPaid()
    {
        Storage::fake('public');
        FormasPago::create(['nombre' => 'TRANSFERENCIA']);
        $input = ['banco' => 1, 'numero_comprobante' => 'ABC123', 'fecha_comprobante' => date('Y-m-d'),
            'hora_comprobante' => '12:00', 'archivo' => UploadedFile::fake()->create('pago.pdf', 20, 'application/pdf')];
        $this->post('/mi-cuenta/prestamos/1/cuotas/2/pago', $input)->assertNotFound();
        $this->withoutExceptionHandling();
        $this->post('/mi-cuenta/prestamos/1/cuotas/1/pago', $input)->assertSessionHasNoErrors()->assertRedirect('/mi-cuenta/prestamos/1');
        $this->withExceptionHandling();
        $this->assertSame('STAND BY', CreditFolderDetail::find(1)->status);
        $this->assertEquals(50, RegistroFormasPago::first()->valor);
        $this->assertEquals(3, RegistroFormasPago::first()->solicitado);
        $this->post('/mi-cuenta/prestamos/1/cuotas/1/pago', $input)->assertSessionHasErrors('archivo');
        $this->assertSame(1, RegistroFormasPago::count());
        $this->get('/mi-cuenta/prestamos/1')->assertOk()->assertSee('Comprobante en revisión');
        $this->get('/mi-cuenta/prestamos/1/cuotas/1/comprobante')->assertOk()->assertHeader('content-disposition');
        $this->get('/mi-cuenta/prestamos/2/cuotas/2/comprobante')->assertNotFound();
        $row = CreditFolderDetail::find(1); $row->path = '../../.env'; $row->save();
        $this->get('/mi-cuenta/prestamos/1/cuotas/1/comprobante')->assertNotFound();
    }

    public function testPasswordRequiresCurrentPasswordAndDoesNotStorePlaintext()
    {
        $this->get('/mi-cuenta/password')->assertOk();
        $input = ['actual' => 'incorrecta', 'password' => 'nueva-clave123', 'password_confirmation' => 'nueva-clave123'];
        $this->post('/mi-cuenta/password', $input)->assertSessionHasErrors('actual')->assertSessionMissing('_old_input.actual');
        $input['actual'] = 'secreto123';
        $this->post('/mi-cuenta/password', $input)->assertRedirect('/mi-cuenta/password')->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('nueva-clave123', User::find(1)->password));
        $this->assertSame('', User::find(1)->token);
    }

    public function testSimulatorUsesCompanyProductAndDoesNotCreateCredit()
    {
        Prestamos::create(['id' => 1, 'company_id' => 1, 'name' => 'Simple', 'status' => 'A', 'calculo_simple' => true, 'interes' => 12]);
        Prestamos::create(['id' => 2, 'company_id' => 2, 'name' => 'Ajeno', 'status' => 'A', 'calculo_simple' => true, 'interes' => 12]);
        $this->get('/mi-cuenta/simulador')->assertOk()->assertSee('Simple')->assertDontSee('Ajeno');
        $input = ['producto' => 1, 'valor' => 100, 'cuotas' => 2, 'fecha' => '2026-09-01'];
        $this->post('/mi-cuenta/simulador', $input)->assertOk()->assertSee('51.00');
        $input['producto'] = 2;
        $this->post('/mi-cuenta/simulador', $input)->assertNotFound();
        $this->assertSame(3, CreditFolderHeader::count());
    }

    public function testTermsAreScopedAndAcceptanceRequiresExplicitCheckbox()
    {
        TerminosUso::create(['id' => 1, 'company_id' => 1, 'name' => 'Condiciones', 'description' => '<script>alert(1)</script>Texto', 'status' => true]);
        TerminosUso::create(['id' => 2, 'company_id' => 2, 'name' => 'Otra caja', 'status' => true]);
        $this->get('/mi-cuenta/terminos')->assertOk()->assertDontSee('<script>alert(1)</script>', false)->assertDontSee('Otra caja');
        $this->post('/mi-cuenta/terminos', ['termino' => 1])->assertSessionHasErrors('aceptar');
        $this->post('/mi-cuenta/terminos', ['termino' => 2, 'aceptar' => 1])->assertNotFound();
        $this->post('/mi-cuenta/terminos', ['termino' => 1, 'aceptar' => 1])->assertSessionHasNoErrors();
        $this->assertSame(1, TerminosUsoClientes::count());
    }
}
