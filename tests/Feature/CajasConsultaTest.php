<?php

namespace Tests\Feature;

use App\DenominacionBilletes;
use App\Http\Livewire\Cajas\CajasComponent;
use App\Models\{Bancos, Bovedas, Cajas, Company};
use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CajasConsultaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'cajas_consulta_test', 'database.connections.cajas_consulta_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '']]);
        foreach ([Bancos::class, Bovedas::class, Cajas::class, Company::class, DenominacionBilletes::class] as $class) {
            $model = new $class;
            Schema::create($model->getTable(), function (Blueprint $table) use ($model) {
                $table->increments('id');
                foreach (array_unique(array_merge($model->getFillable(), $model instanceof Bovedas ? ['boveda'] : [])) as $field) {
                    if ($field !== 'id') $table->string($field)->nullable();
                }
                $table->timestamps();
            });
        }
        Company::create(['id' => 10, 'caja_boveda' => 1]);
        $user = new User;
        $user->id = 1;
        $user->company_id = 10;
        $user->admin = 0;
        $this->actingAs($user);
        foreach ([1 => ['ABIERTA', 0], 2 => ['CERRADA', 12.50], 3 => ['CERRADA', -5.25], 4 => ['CERRADA', 0], 5 => ['ABIERTA', 0]] as $id => $data) {
            DB::table('cajas')->insert(['id' => $id, 'company_id' => $id === 5 ? 20 : 10,
                'code' => 'CAJA-' . $id, 'user_inicial_id' => $id === 1 ? 2 : 1,
                'user_name_inicial' => $id === 2 ? 'Ana' : 'Carlos', 'date_inicial' => date('Y-m-d'),
                'hour_inicial' => '08:00:00', 'valor_inicial' => 100, 'total' => 150,
                'status' => $data[0], 'descuadre' => $data[1]]);
        }
        DB::table('bovedas')->insert(['id' => 1, 'company_id' => 10, 'nombre' => 'Bóveda propia', 'boveda' => 1, 'status' => 1]);
        DB::table('bovedas')->insert(['id' => 2, 'company_id' => 20, 'nombre' => 'Bóveda externa', 'boveda' => 1, 'status' => 1]);
    }

    public function testSummaryIncludesBothSignsAndOnlyOwnCompany()
    {
        $data = (new CajasComponent)->render()->getData();
        $this->assertSame(4, $data['cajas']->total());
        $this->assertEquals(2, $data['resumen']->diferencias);
        $this->assertEquals(1, $data['resumen']->abiertas);
        $this->assertEquals(3, $data['resumen']->cerradas);
        $this->assertEquals(0, $data['cajaAbierta']);
        $this->assertSame(1, $data['bovedas']->count());
    }

    public function testSearchStateAndDifferenceFilters()
    {
        $component = new CajasComponent;
        $component->soloDescuadres = true;
        $this->assertSame(2, $component->render()->getData()['cajas']->total());
        $component->search = 'Ana';
        $this->assertSame(1, $component->render()->getData()['cajas']->total());
        $component->search = '';
        $component->status = 'ABIERTA';
        $this->assertSame(0, $component->render()->getData()['cajas']->total());
    }

    public function testChangingFiltersResetsPaginationAndCanBeCleared()
    {
        $component = new CajasComponent;
        $component->page = 4;
        $component->updated('status');
        $this->assertEquals(1, $component->page);
        $component->search = 'CAJA-2';
        $component->soloDescuadres = true;
        $component->limpiarFiltros();
        $this->assertSame('', $component->search);
        $this->assertFalse($component->soloDescuadres);
        $this->assertSame('ALL', $component->status);
    }

    public function testLivewireRendersBootstrapTemplateAndOwnCajaNotice()
    {
        \Livewire\Livewire::test(CajasComponent::class)->assertSee('panel panel-inverse', false)
            ->assertSee('widget widget-stats', false)->assertSee('No tienes una caja abierta para hoy')
            ->assertSee('Solo cajas con diferencias')->assertSee('Revisar diferencia')
            ->assertDontSee('Bóveda externa')->assertSee('Cierre y arqueo de caja');
    }

    public function testOwnOpenCajaUpdatesTheNotice()
    {
        DB::table('cajas')->where('id', 1)->update(['user_inicial_id' => 1]);
        \Livewire\Livewire::test(CajasComponent::class)->assertSee('Tu caja de hoy está abierta');
    }
}
