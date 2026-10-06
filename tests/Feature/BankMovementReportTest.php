<?php

namespace Tests\Feature;

use App\Exports\BankMovementExport;
use App\Http\Livewire\Conciliacion\ConciliacionComponent;
use App\Services\BankMovementReport;
use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

class BankMovementReportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'bank_report_test', 'database.connections.bank_report_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        foreach (['bancos', 'formas_pago', 'type_transactions'] as $name) {
            Schema::create($name, function (Blueprint $t) {
                $t->increments('id'); $t->integer('company_id');
                $t->string('nombre')->nullable(); $t->string('name')->nullable();
                $t->string('numero_cuenta')->nullable(); $t->integer('tipo_cuenta_id')->default(1);
            });
        }
        Schema::create('rol', function (Blueprint $t) { $t->increments('id'); $t->boolean('status'); });
        Schema::create('usuario_rol', function (Blueprint $t) { $t->integer('user_id'); $t->integer('rol_id'); $t->string('status'); });
        Schema::create('menu', function (Blueprint $t) { $t->increments('id'); $t->string('url'); });
        Schema::create('menu_rol', function (Blueprint $t) { $t->integer('menu_id'); $t->integer('rol_id'); });
        DB::table('rol')->insert(['id' => 1, 'status' => 1]);
        DB::table('usuario_rol')->insert(['user_id' => 1, 'rol_id' => 1, 'status' => '1']);
        DB::table('menu')->insert(['id' => 1, 'url' => '/conciliacion']);
        DB::table('menu_rol')->insert(['menu_id' => 1, 'rol_id' => 1]);
        Schema::create('customer_movimientos', function (Blueprint $t) {
            $t->increments('id'); $t->integer('company_id'); $t->integer('banco_id')->nullable();
            $t->integer('forma_pago_id')->nullable(); $t->integer('type_transaction_id')->nullable();
            $t->boolean('status')->default(1); $t->decimal('valor_movimiento', 12, 2);
            foreach (['date_created', 'hour_created', 'code', 'comprobante', 'numero_deposito', 'customer_name',
                'customer_ruc', 'customer_code', 'type_transaction_action', 'type_transaction_name', 'forma_pago_name',
                'observation', 'date_cancel', 'razon_cancel'] as $field) $t->string($field)->nullable();
        });
        DB::table('bancos')->insert([
            ['id' => 1, 'company_id' => 10, 'nombre' => 'Banco propio', 'numero_cuenta' => '00123'],
            ['id' => 2, 'company_id' => 20, 'nombre' => 'Banco ajeno', 'numero_cuenta' => '00999'],
        ]);
        Schema::create('operaciones_descargo_bovedas', function (Blueprint $t) {
            $t->increments('id'); $t->integer('company_id'); $t->string('nombre_corto');
        });
        DB::table('operaciones_descargo_bovedas')->insert([
            ['id' => 1, 'company_id' => 10, 'nombre_corto' => 'CAREM'],
            ['id' => 2, 'company_id' => 10, 'nombre_corto' => 'CARCLI'],
            ['id' => 3, 'company_id' => 10, 'nombre_corto' => 'TRANRECI'],
            ['id' => 4, 'company_id' => 20, 'nombre_corto' => 'CAREM'],
        ]);
        Schema::create('descargo_bovedas_header', function (Blueprint $t) {
            $t->increments('id'); $t->integer('company_id'); $t->integer('bancos_id')->nullable();
            $t->integer('operaciones_descargo_bovedas_id'); $t->integer('customer_movimiento_id')->nullable();
            $t->boolean('status')->default(1); $t->decimal('valor', 12, 2);
            $t->dateTime('fecha_creacion'); $t->string('estado'); $t->string('observacion')->nullable();
        });
        DB::table('formas_pago')->insert(['id' => 1, 'company_id' => 10, 'nombre' => 'Transferencia']);
        DB::table('type_transactions')->insert(['id' => 1, 'company_id' => 10, 'name' => 'Depósito']);
        $user = new User(); $user->id = 1; $user->company_id = 10;
        $this->actingAs($user);
    }

    private function movement(array $data = [])
    {
        return DB::table('customer_movimientos')->insertGetId(array_merge([
            'company_id' => 10, 'banco_id' => 1, 'valor_movimiento' => '100.10', 'type_transaction_action' => 'S',
            'date_created' => date('Y-m-15'), 'hour_created' => '12:00:00', 'code' => 'MOV-01',
            'customer_name' => 'Ana', 'forma_pago_id' => 1, 'type_transaction_id' => 1,
        ], $data));
    }

    private function opening(array $data = [])
    {
        return DB::table('descargo_bovedas_header')->insertGetId(array_merge([
            'company_id' => 10, 'bancos_id' => 1, 'operaciones_descargo_bovedas_id' => 1,
            'valor' => '250.50', 'fecha_creacion' => date('Y-m-15') . ' 23:59:59',
            'estado' => 'FINALIZADO', 'observacion' => 'CARGA INICIAL',
        ], $data));
    }

    public function testVaultOpeningsAppearByBankInScreenTotalsAndExports()
    {
        $this->movement();
        $this->opening();
        $this->opening(['operaciones_descargo_bovedas_id' => 2, 'valor' => '49.50']);
        $filters = array_merge(BankMovementReport::defaults(), ['banco' => 1]);
        $report = app(BankMovementReport::class);
        $this->assertEquals(400.10, $report->totals($filters)->entradas);
        $this->assertEquals(3, $report->query($filters)->count());
        $component = Livewire::test(ConciliacionComponent::class)
            ->set('filters.banco', '1')->assertSee('Carga inicial de bóveda')->assertSee('400.10');
        $this->assertCount(3, $component->viewData('movimientos'));
        $component->set('filters.tipo', 'carga_inicial');
        $this->assertEquals(300, $component->viewData('totales')->entradas);
        $this->assertCount(2, $component->viewData('movimientos'));

        $export = new BankMovementExport($filters);
        $rows = $export->query()->get();
        $this->assertCount(3, $rows);
        $mapped = $export->map($rows->firstWhere('origen', 'boveda'));
        $this->assertSame('Banco propio', $mapped[2]);
        $this->assertSame(250.50, $mapped[13]);
        $this->assertStringContainsString('Carga inicial de bóveda', view('conciliacion.pdf', [
            'movimientos' => $rows, 'totales' => $report->totals($filters),
            'filters' => $filters, 'labels' => $report->filterLabels($filters),
        ])->render());
        $this->assertEquals(1, DB::table('customer_movimientos')->count());
        $day = array_merge($filters, ['desde' => date('Y-m-15'), 'hasta' => date('Y-m-15')]);
        $this->assertEquals(400.10, $report->totals($day)->entradas);
        $this->assertEquals(100.10, $report->totals(array_merge($filters, ['forma' => 1]))->entradas);
        $this->assertEquals(100.10, $report->totals(array_merge($filters, ['tipo' => 1]))->entradas);
        $this->assertEquals(300, $report->totals(array_merge($filters, ['buscar' => 'CARGA-BOVEDA']))->entradas);
    }

    public function testVaultOpeningsRespectDatesStateCompanyAndAvoidLinkedDuplicates()
    {
        $linked = $this->movement();
        $this->opening(['customer_movimiento_id' => $linked]);
        $this->opening(['estado' => 'PENDIENTE']);
        $this->opening(['fecha_creacion' => '2000-01-01 12:00:00']);
        $this->opening(['company_id' => 20, 'bancos_id' => 2, 'operaciones_descargo_bovedas_id' => 4]);
        $this->opening(['operaciones_descargo_bovedas_id' => 3]);
        $this->opening(['operaciones_descargo_bovedas_id' => 4]);
        $this->opening(['status' => 0]);
        $this->opening(['bancos_id' => 2]);
        $this->opening(['bancos_id' => null]);
        $report = app(BankMovementReport::class);
        $f = array_merge(BankMovementReport::defaults(), ['banco' => 1]);
        $this->assertEquals(1, $report->query($f)->count());
        $this->assertEquals(100.10, $report->totals($f)->entradas);
        $f['estado'] = 'todos';
        $this->assertEquals(2, $report->query($f)->count());
        $this->assertEquals(100.10, $report->totals($f)->entradas);
        $f['estado'] = 'anulados';
        $this->assertEquals(1, $report->query($f)->count());
        $this->assertEquals(0, $report->totals($f)->entradas);
        $f = array_merge(BankMovementReport::defaults(), ['tipo' => 'carga_inicial']);
        $this->assertEquals(2, $report->totals($f)->revisar);
        $this->assertEquals(0, $report->totals($f)->entradas);
    }

    public function testTotalsExcludeCancelledUnidentifiedAndForeignMovements()
    {
        $this->movement();
        $this->movement(['valor_movimiento' => '30.05', 'type_transaction_action' => 'R']);
        $this->movement(['status' => 0, 'valor_movimiento' => 900]);
        $this->movement(['banco_id' => null, 'valor_movimiento' => 800]);
        $this->movement(['banco_id' => 2, 'valor_movimiento' => 700]);
        $this->movement(['company_id' => 20, 'banco_id' => 2, 'valor_movimiento' => 600]);
        $this->movement(['type_transaction_action' => '?', 'valor_movimiento' => 500]);
        $report = app(BankMovementReport::class);
        $filters = array_merge($report::defaults(), ['estado' => 'todos', 'alcance' => 'todos']);
        $totals = $report->totals($filters);
        $this->assertEquals(6, $totals->cantidad);
        $this->assertEquals(100.10, $totals->entradas);
        $this->assertEquals(30.05, $totals->salidas);
        $this->assertEquals(3, $totals->revisar);
        $this->assertNull($report->rows($filters)->where('m.banco_id', 2)->first()->banco_nombre);
        $this->assertCount(3, $report->rows(array_merge($report::defaults(), ['banco' => 'bancarios']))->get());
    }

    public function testBankSelectorAllowsCashWithoutAConflictingScope()
    {
        DB::table('formas_pago')->insert(['id' => 2, 'company_id' => 10, 'nombre' => 'Efectivo']);
        $cash = $this->movement(['banco_id' => null, 'forma_pago_id' => 2]);
        $bank = $this->movement();
        $component = Livewire::test(ConciliacionComponent::class)->assertSee('Todos (con y sin banco)');
        $this->assertEquals(2, $component->viewData('movimientos')->total());
        $component->set('filters.banco', '1')->call('applyFilters');
        $this->assertEquals([$bank], $component->viewData('movimientos')->pluck('id')->all());
        $component->set('filters.banco', 'sin_banco')->set('filters.forma', '2')->call('applyFilters');
        $this->assertEquals([$cash], $component->viewData('movimientos')->pluck('id')->all());
        $this->assertEquals([$cash], (new BankMovementExport($component->get('applied')))->query()->pluck('m.id')->all());
        $component->set('filters.banco', '')->call('applyFilters');
        $this->assertEquals([$cash], $component->viewData('movimientos')->pluck('id')->all());
        $component->call('clearFilters');
        $this->assertEquals(2, $component->viewData('movimientos')->total());
    }

    public function testSearchDatesAndMissingBankScopeAreApplied()
    {
        $id = $this->movement(['banco_id' => null, 'numero_deposito' => 'REF-77']);
        $this->movement(['company_id' => 20, 'numero_deposito' => 'REF-77']);
        $this->movement(['date_created' => '2000-01-01', 'numero_deposito' => 'REF-77']);
        $report = app(BankMovementReport::class);
        $f = array_merge($report::defaults(), ['alcance' => 'sin_banco', 'buscar' => 'REF-77']);
        $this->assertEquals([$id], $report->rows($f)->pluck('m.id')->all());
    }

    public function testForeignFilterIsRejected()
    {
        $this->expectException(ValidationException::class);
        app(BankMovementReport::class)->query(array_merge(BankMovementReport::defaults(), ['banco' => 2]));
    }

    public function testPermissionIsCheckedOnEveryReportRequest()
    {
        DB::table('menu_rol')->delete();
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        app(BankMovementReport::class)->rows(BankMovementReport::defaults());
    }

    public function testLegacyAssignmentWithEmptyStatusCanAccessItsAssignedMenu()
    {
        DB::table('usuario_rol')->update(['status' => '']);
        session(['rol_id' => 1]);
        $this->movement();
        Livewire::test(ConciliacionComponent::class)->assertSee('Conciliación bancaria')->assertSee('100.10');
        $this->assertEquals(10, app(BankMovementReport::class)->companyId());
    }

    public function testInactiveRoleCannotAccessEvenWithLegacyAssignment()
    {
        DB::table('usuario_rol')->update(['status' => '']);
        DB::table('rol')->update(['status' => 0]);
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        app(BankMovementReport::class)->companyId();
    }

    public function testSelectedRoleMustBelongToTheUser()
    {
        session(['rol_id' => 2]);
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        app(BankMovementReport::class)->companyId();
    }

    public function testScreenPaginatesAndKeepsTotalsForAllRows()
    {
        for ($i = 0; $i < 26; $i++) $this->movement(['valor_movimiento' => 10]);
        $component = Livewire::test(ConciliacionComponent::class)->assertSee('Conciliación bancaria')
            ->assertSee('260.00')->assertSee('26 registros');
        $this->assertCount(25, $component->viewData('movimientos'));
        $component->call('gotoPage', 2);
        $this->assertCount(1, $component->viewData('movimientos'));
        $component->set('filters.buscar', 'inexistente')->assertSee('No se encontraron movimientos');
        $component->assertSet('page', 1);
    }

    public function testInvalidDatesKeepLastValidResults()
    {
        Livewire::test(ConciliacionComponent::class)->set('filters.desde', '2026-09-30')
            ->set('filters.hasta', '2026-09-01')->call('applyFilters')->assertHasErrors('filters.hasta');
    }

    public function testExcelContainsAllRowsAndPreservesTextReferences()
    {
        for ($i = 0; $i < 26; $i++) $this->movement(['numero_deposito' => '=1+1', 'customer_ruc' => '001234']);
        $this->opening();
        $bytes = \Maatwebsite\Excel\Facades\Excel::raw(new BankMovementExport(BankMovementReport::defaults()), \Maatwebsite\Excel\Excel::XLSX);
        $file = tempnam(sys_get_temp_dir(), 'bank-test-');
        try {
            file_put_contents($file, $bytes);
            $sheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file)->getActiveSheet();
            $this->assertEquals(28, $sheet->getHighestRow());
            $this->assertSame('Carga inicial de bóveda', $sheet->getCell('J28')->getValue());
            $this->assertEquals(250.50, $sheet->getCell('N28')->getValue());
            $this->assertSame('=1+1', $sheet->getCell('G2')->getValue());
            $this->assertSame('s', $sheet->getCell('G2')->getDataType());
            $this->assertSame('001234', $sheet->getCell('I2')->getValue());
        } finally { unlink($file); }
    }

    public function testPdfDownloadIsGeneratedWithoutChangingMovements()
    {
        $this->movement();
        $this->opening();
        $component = new ConciliacionComponent(); $component->mount();
        $response = $component->exportPdf();
        ob_start(); $response->sendContent(); $bytes = ob_get_clean();
        $this->assertStringStartsWith('%PDF', $bytes);
        $this->assertEquals(1, DB::table('customer_movimientos')->count());
    }
}
