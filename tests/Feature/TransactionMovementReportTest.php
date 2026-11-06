<?php

namespace Tests\Feature;

use App\Services\TransactionMovementReport;
use App\Exports\TransactionMovementExport;
use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class TransactionMovementReportTest extends TestCase
{
    public function testLivewireFiltersUpdateAutomaticallyAndResetPagination()
    {
        for ($i = 0; $i < 51; $i++) $this->movement();
        $this->movement(['status' => 0, 'customer_name' => 'Anulado']);
        $component = \Livewire\Livewire::test(\App\Http\Livewire\Reportes\TransactionMovementComponent::class)
            ->assertDontSee('Consultar');
        $this->assertEquals(51, $component->viewData('movimientos')->total());
        $component->call('gotoPage', 2)->set('filters.buscar', 'Inexistente')->assertSet('page', 1);
        $this->assertEquals(0, $component->viewData('movimientos')->total());
        $component->set('filters.buscar', '')->set('filters.estado', 'anulados');
        $this->assertEquals(1, $component->viewData('movimientos')->total());
        $component->set('filters.desde', '2000-01-01')->set('filters.hasta', '2000-01-02');
        $this->assertEquals(0, $component->viewData('movimientos')->total());
        $component->call('clearFilters')->assertSet('filters.estado', 'activos');
        $this->assertEquals(51, $component->viewData('movimientos')->total());
        $component->set('filters.tipo', '2')->assertHasErrors('filters.tipo');
        $component->set('filters.tipo', '1')->assertHasNoErrors();
        $component->set('filters.hasta', '1999-01-01')->assertHasErrors('filters.hasta');
        $component->call('exportExcel')->assertHasErrors('filters.hasta');
        $component->call('clearFilters')->call('exportExcel')->assertFileDownloaded('movimientos-por-transaccion.xlsx');
        $component->call('exportPdf')->assertFileDownloaded('movimientos-por-transaccion.pdf');
    }

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'transaction_report_test', 'database.connections.transaction_report_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        Schema::create('type_transactions', function (Blueprint $t) {
            $t->increments('id'); $t->integer('company_id'); $t->string('name');
        });
        Schema::create('customer_movimientos', function (Blueprint $t) {
            $t->increments('id'); $t->integer('company_id'); $t->integer('type_transaction_id');
            $t->boolean('status')->default(1); $t->decimal('valor_movimiento', 12, 2);
            foreach (['date_created', 'hour_created', 'code', 'comprobante', 'customer_code', 'customer_name',
                'customer_ruc', 'type_transaction_action', 'type_transaction_name', 'user_created_name', 'observation'] as $field) {
                $t->string($field)->nullable();
            }
        });
        DB::table('type_transactions')->insert([
            ['id' => 1, 'company_id' => 10, 'name' => 'Depósitos'],
            ['id' => 2, 'company_id' => 20, 'name' => 'Tipo ajeno'],
        ]);
        $user = new User(); $user->id = 1; $user->company_id = 10; $this->actingAs($user);
    }

    private function movement(array $data = [])
    {
        DB::table('customer_movimientos')->insert(array_merge([
            'company_id' => 10, 'type_transaction_id' => 1, 'status' => 1,
            'valor_movimiento' => 100.15, 'type_transaction_action' => 'S',
            'date_created' => date('Y-m-d'), 'customer_name' => 'Ana', 'customer_ruc' => '00123',
            'code' => 'MOV-01', 'type_transaction_name' => 'Nombre histórico',
        ], $data));
    }

    public function testFiltersTotalsAndCompleteDownloads()
    {
        for ($i = 0; $i < 51; $i++) $this->movement();
        $this->movement(['status' => 0, 'valor_movimiento' => 900]);
        $this->movement(['type_transaction_action' => 'R', 'valor_movimiento' => 30.05]);
        $this->movement(['company_id' => 20, 'type_transaction_id' => 2]);
        $this->movement(['date_created' => '2000-01-01']);
        $report = app(TransactionMovementReport::class);
        $f = $report->filters([]);
        $this->assertEquals(52, $report->rows($f)->count());
        $this->assertEquals(5107.65, $report->summary($f)->sum('entradas'));
        $this->assertEquals(30.05, $report->summary($f)->sum('salidas'));
        $this->assertEquals(53, $report->rows(array_merge($f, ['estado' => 'todos']))->count());
        $this->assertEquals(0, $report->rows(array_merge($f, ['buscar' => 'Inexistente']))->count());
        $this->assertEquals(52, $report->rows(array_merge($f, ['buscar' => '00123']))->count());
        $export = new TransactionMovementExport($f);
        $this->assertCount(52, $export->view()->getData()['movimientos']);
        $bytes = Excel::raw($export, \Maatwebsite\Excel\Excel::XLSX);
        $this->assertSame('PK', substr($bytes, 0, 2));
        $pdf = \PDF::loadView('reportes.movimientos-transacciones-export', [
            'filters' => $f, 'summary' => $report->summary($f), 'movimientos' => $report->rows($f)->get(),
        ])->setPaper('a4', 'landscape')->output();
        $this->assertSame('%PDF', substr($pdf, 0, 4));
    }

    public function testRejectsTransactionFromAnotherCompany()
    {
        $this->expectException(ValidationException::class);
        app(TransactionMovementReport::class)->filters(['tipo' => 2]);
    }

    public function testRejectsInvertedDates()
    {
        $this->expectException(ValidationException::class);
        app(TransactionMovementReport::class)->filters(['desde' => '2026-11-06', 'hasta' => '2026-11-01']);
    }
}
