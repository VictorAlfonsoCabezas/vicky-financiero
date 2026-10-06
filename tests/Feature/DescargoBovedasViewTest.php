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

    public function testEmptyViewHasClearStatesAndDistinctModals()
    {
        $html = $this->renderModule();
        $this->assertStringContainsString('No hay bóvedas activas', $html);
        $this->assertStringContainsString('Aún no hay transferencias registradas.', $html);
        $this->assertStringContainsString('wire:click="abrirCrearBoveda"', $html);
        foreach (['crear', 'carga', 'gasto', 'transferencia', 'detalle-carga'] as $type) {
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
            'valoresInicialesEmpresa' => collect([(object) ['id' => 7, 'boveda_origen_id' => 1, 'nombre_banco' => '<script>alert(1)</script>', 'valor' => 1500]]),
        ]);
        $this->assertStringContainsString('$ 1,250.00', $html);
        $this->assertStringContainsString('Este saldo descuenta los créditos vigentes.', $html);
        $this->assertStringContainsString('<option value="2">Sucursal</option>', $html);
        $this->assertStringNotContainsString('<option value="1">Central</option>', $html);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
    }

    private function prepareCreationDatabase()
    {
        config(['database.default' => 'vault_creation_test', 'database.connections.vault_creation_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        Schema::create('bovedas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->dateTime('fecha_creacion');
            $table->string('nombre');
            $table->string('descripcion');
            $table->boolean('principal');
            $table->boolean('boveda');
            $table->boolean('caja');
            $table->boolean('status');
            $table->timestamps();
        });
        $user = new User(); $user->id = 1; $user->company_id = 1;
        $this->actingAs($user);
    }

    public function testLoadDetailIsReadOnlyAndRestrictedToCompanyAndOpeningOperations()
    {
        $this->prepareCreationDatabase();
        Schema::create('bancos', function (Blueprint $t) {
            $t->increments('id'); $t->integer('company_id'); $t->string('nombre'); $t->string('numero_cuenta');
        });
        Schema::create('operaciones_descargo_bovedas', function (Blueprint $t) {
            $t->increments('id'); $t->integer('company_id'); $t->string('nombre'); $t->string('nombre_corto');
        });
        Schema::create('descargo_bovedas_header', function (Blueprint $t) {
            $t->increments('id'); $t->integer('company_id'); $t->integer('bancos_id');
            $t->integer('boveda_origen_id'); $t->integer('operaciones_descargo_bovedas_id');
            $t->dateTime('fecha_creacion'); $t->timestamps(); $t->decimal('valor', 12, 2);
            $t->string('estado'); $t->boolean('status'); $t->string('observacion');
        });
        DB::table('bancos')->insert(['id' => 1, 'company_id' => 1, 'nombre' => 'Banco propio', 'numero_cuenta' => '00123']);
        DB::table('operaciones_descargo_bovedas')->insert([
            ['id' => 1, 'company_id' => 1, 'nombre' => 'Carga empresa', 'nombre_corto' => 'CAREM'],
            ['id' => 2, 'company_id' => 1, 'nombre' => 'Transferencia', 'nombre_corto' => 'TRANENV'],
        ]);
        $row = ['company_id' => 1, 'bancos_id' => 1, 'boveda_origen_id' => 1,
            'operaciones_descargo_bovedas_id' => 1, 'fecha_creacion' => '2026-10-05 10:30:00',
            'created_at' => '2026-10-05 10:31:00', 'valor' => 1500, 'estado' => 'FINALIZADO',
            'status' => 1, 'observacion' => '<script>alert(1)</script>'];
        DB::table('descargo_bovedas_header')->insert(array_merge($row, ['id' => 1]));
        DB::table('descargo_bovedas_header')->insert(array_merge($row, ['id' => 2, 'company_id' => 2]));
        DB::table('descargo_bovedas_header')->insert(array_merge($row, ['id' => 3, 'operaciones_descargo_bovedas_id' => 2]));
        $before = DB::table('descargo_bovedas_header')->get()->toJson();
        $component = new DescargoBovedasHeaderComponent();
        $component->verDetalleCarga(1);
        $this->assertSame('00123', $component->detalleCarga['cuenta']);
        $this->assertSame('Banco propio', $component->detalleCarga['banco']);
        $this->assertSame('2026-10-05 10:31:00', $component->detalleCarga['created_at']);
        $html = $this->renderModule(['detalleCarga' => $component->detalleCarga]);
        $this->assertStringContainsString('Solo consulta', $html);
        $this->assertStringContainsString('$ 1,500.00', $html);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        foreach ([2, 3, 999] as $id) {
            try {
                $component->verDetalleCarga($id);
                $this->fail('Only company opening records can be viewed.');
            } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
                $this->assertSame(404, $e->getStatusCode());
                $this->assertSame([], $component->detalleCarga);
            }
        }
        $this->assertSame($before, DB::table('descargo_bovedas_header')->get()->toJson());
    }

    public function testCreationAssignsCompanyAndRefreshesOnlyActiveCompanyDestinations()
    {
        $this->prepareCreationDatabase();
        $component = new DescargoBovedasHeaderComponent();
        $component->nuevoNombre = '  Sucursal  ';
        $component->nuevaDescripcion = '  Fondos de sucursal  ';
        $component->storeBoveda();
        $created = DB::table('bovedas')->first();
        $this->assertSame('Sucursal', $created->nombre);
        $this->assertSame('Fondos de sucursal', $created->descripcion);
        $this->assertEquals(1, $created->company_id);
        $this->assertEquals(1, $created->status);
        $this->assertEquals(1, $created->boveda);
        $this->assertEquals(0, $created->principal);
        $this->assertEquals(0, $created->caja);
        $this->assertNotEmpty($created->fecha_creacion);
        $this->assertSame('', $component->nuevoNombre);

        $foreign = (array) $created;
        unset($foreign['id']);
        $foreign['company_id'] = 2;
        DB::table('bovedas')->insert($foreign);
        $inactive = $foreign;
        $inactive['company_id'] = 1; $inactive['status'] = 0;
        DB::table('bovedas')->insert($inactive);

        $component->nuevoNombre = 'Central';
        $component->nuevaDescripcion = 'Fondos centrales';
        $component->nuevaPrincipal = true;
        $component->storeBoveda();
        $this->assertEquals(1, DB::table('bovedas')->where('nombre', 'Central')->value('principal'));
        $this->assertCount(2, $component->bovedasTransferencia);
        $this->assertSame(['Sucursal', 'Central'], array_column($component->bovedasTransferencia, 'nombre'));
        // Creation succeeds without a movements table: it must not create financial entries.
        $component->nuevoNombre = 'Anterior';
        $component->nuevaPrincipal = true;
        $component->addError('nuevoNombre', 'Error anterior');
        $component->abrirCrearBoveda();
        $this->assertSame('', $component->nuevoNombre);
        $this->assertFalse($component->nuevaPrincipal);
        $this->assertFalse($component->getErrorBag()->any());
    }

    public function testCreationRejectsBlankNamesAndOverlongDescriptions()
    {
        $this->prepareCreationDatabase();
        $component = new DescargoBovedasHeaderComponent();
        $component->nuevoNombre = '   ';
        $component->nuevaDescripcion = str_repeat('x', 256);
        try {
            $component->storeBoveda();
            $this->fail('Invalid data must not be saved.');
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->assertArrayHasKey('nuevoNombre', $exception->errors());
            $this->assertArrayHasKey('nuevaDescripcion', $exception->errors());
        }
        $this->assertSame(0, DB::table('bovedas')->count());
    }

    public function testCreationRequiresAuthentication()
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        (new DescargoBovedasHeaderComponent())->storeBoveda();
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
