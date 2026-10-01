<?php

namespace Tests\Feature;

use App\Http\Livewire\DescargoBovedasHeader\DescargoBovedasHeaderComponent;
use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class DescargoBovedasViewTest extends TestCase
{
    private function renderModule(array $overrides = [])
    {
        return view('livewire.descargo-bovedas-header.descargo-bovedas-header-component', array_merge([
            'bovedas' => collect(), 'valoresInicialesEmpresa' => collect(), 'gastosIniciales' => collect(),
            'bancosValores' => collect(), 'transferencias' => collect(), 'saldosBancos' => [],
            'creditosVigentes' => 0, 'bancos' => [], 'cargaInicial' => [], 'bovedasTransferencia' => [],
            'boveda_id' => '', 'bovedaNombre' => '', 'errors' => new ViewErrorBag(),
        ], $overrides))->render();
    }

    public function testEmptyViewHasClearStatesAndThreeDistinctForms()
    {
        $html = $this->renderModule();
        $this->assertStringContainsString('No hay bóvedas activas', $html);
        $this->assertStringContainsString('Aún no hay transferencias registradas.', $html);
        foreach (['carga', 'gasto', 'transferencia'] as $type) {
            $this->assertSame(1, substr_count($html, 'id="vault-modal-' . $type . '"'));
        }
        $this->assertStringNotContainsString('btn-tool', $html);
    }

    public function testViewFormatsBalancesAndExcludesSourceFromDestinations()
    {
        $html = $this->renderModule([
            'bovedas' => collect([(object) ['id' => 1, 'nombre' => 'Central', 'descripcion' => 'Fondos', 'principal' => 1, 'saldoBoveda' => 1500]]),
            'creditosVigentes' => 250,
            'boveda_id' => 1, 'bovedaNombre' => 'Central',
            'bovedasTransferencia' => [['id' => 1, 'nombre' => 'Central'], ['id' => 2, 'nombre' => 'Sucursal']],
            'valoresInicialesEmpresa' => collect([(object) ['boveda_origen_id' => 1, 'nombre_banco' => '<script>alert(1)</script>', 'valor' => 1500]]),
        ]);
        $this->assertStringContainsString('$ 1,250.00', $html);
        $this->assertStringContainsString('Este saldo descuenta los créditos vigentes.', $html);
        $this->assertStringContainsString('<option value="2">Sucursal</option>', $html);
        $this->assertStringNotContainsString('<option value="1">Central</option>', $html);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
    }

    public function testOpeningAnotherOperationClearsPreviousInputAndErrors()
    {
        config(['database.default' => 'vault_test', 'database.connections.vault_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        Schema::create('bovedas', function (Blueprint $table) {
            $table->increments('id'); $table->integer('company_id'); $table->string('nombre');
            $table->boolean('status'); $table->boolean('principal');
        });
        DB::table('bovedas')->insert([
            ['id' => 1, 'company_id' => 1, 'nombre' => 'Central', 'status' => 1, 'principal' => 1],
            ['id' => 2, 'company_id' => 2, 'nombre' => 'Ajena', 'status' => 1, 'principal' => 1],
        ]);
        $user = new User(); $user->id = 1; $user->company_id = 1;
        $this->actingAs($user);
        $component = new DescargoBovedasHeaderComponent();
        $component->trans_observacion = 'Anterior'; $component->trans_valor = 999;
        $component->addError('trans_valor', 'Error anterior');
        $component->abrirOperacion(1, 'transferencia');
        $this->assertSame('Central', $component->bovedaNombre);
        $this->assertSame('', $component->trans_observacion);
        $this->assertSame('', $component->trans_valor);
        $this->assertFalse($component->getErrorBag()->any());
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $component->abrirOperacion(2, 'transferencia');
    }
}
