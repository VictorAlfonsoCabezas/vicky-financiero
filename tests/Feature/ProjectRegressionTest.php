<?php

namespace Tests\Feature;

use App\Http\Livewire\Cuentas\CuentasComponent;
use App\Http\Livewire\SolicitudEncaje\SolicitudEncajeComponent;
use App\Http\Livewire\DocumentosParametrizables\DocumentosFormatoComponent;
use App\Models\DocumentosParametrizables;
use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProjectRegressionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'regression', 'database.connections.regression' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
    }

    private function login()
    {
        $user = new User(); $user->id = 1; $user->company_id = 1; $user->username = 'prueba';
        $this->actingAs($user);
    }

    public function testReceiptPreservesTheRealYearAndUsesTheReceiptCustomer()
    {
        $this->login();
        Schema::create('company', function (Blueprint $t) { $t->increments('id'); $t->string('photo')->nullable(); });
        Schema::create('customer', function (Blueprint $t) { $t->increments('id'); $t->integer('company_id'); });
        Schema::create('entrega_encajes', function (Blueprint $t) {
            $t->increments('id'); $t->integer('customer_id'); $t->date('date_created');
        });
        DB::table('company')->insert(['id' => 1]);
        DB::table('customer')->insert(['id' => 8, 'company_id' => 1]);
        DB::table('entrega_encajes')->insert(['id' => 1, 'customer_id' => 8, 'date_created' => '2026-09-06']);
        \PDF::shouldReceive('loadView')->twice()->withArgs(function ($view, $data) {
            $this->assertSame('reportes.generarPdfEncajeRetenido', $view);
            $this->assertSame('06 de septiembre del 2026', $data['fechaFormateada']);
            $this->assertSame(8, $data['customer']->id);
            return true;
        })->andReturn(\Mockery::mock());
        foreach ([new CuentasComponent(), new SolicitudEncajeComponent()] as $component) {
            $response = $component->imprimirEntregaEncaje(1);
            $this->assertInstanceOf(\Symfony\Component\HttpFoundation\StreamedResponse::class, $response);
        }
    }

    public function testDocumentSavingDoesNotOverwriteAnotherCompanyTemplate()
    {
        $this->login();
        Schema::create('documentos_parametrizables', function (Blueprint $t) {
            $t->increments('id'); $t->integer('company_id'); $t->string('formato'); $t->text('content');
            $t->string('date_created')->nullable(); $t->string('hour_created')->nullable();
            $t->integer('user_created_id')->nullable(); $t->string('user_created_name')->nullable(); $t->timestamps();
        });
        DocumentosParametrizables::create(['company_id' => 2, 'formato' => 'PAGARE', 'content' => 'Privado']);
        $editor = new DocumentosFormatoComponent();
        $editor->mount(0, 1);
        $this->assertSame('PAGARE', $editor->contenido);
        $editor->contenido = 'Formato de mi empresa';
        $editor->guardarFormato();
        $editor->contenido = 'Última versión';
        $editor->guardarFormato();
        $this->assertSame(2, DocumentosParametrizables::count());
        $this->assertSame('Privado', DocumentosParametrizables::where('company_id', 2)->first()->content);
        $this->assertSame('Última versión', DocumentosParametrizables::where('company_id', 1)->first()->content);
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $editor->mount(1, 1);
    }

    public function testInactiveUsersAndUsersWithoutActiveRolesCannotSignIn()
    {
        Schema::create('users', function (Blueprint $t) {
            $t->increments('id'); $t->string('username'); $t->string('password');
            $t->boolean('status'); $t->string('remember_token')->nullable();
        });
        Schema::create('rol', function (Blueprint $t) { $t->increments('id'); $t->boolean('status'); });
        Schema::create('usuario_rol', function (Blueprint $t) { $t->integer('user_id'); $t->integer('rol_id'); });
        DB::table('users')->insert(['id' => 1, 'username' => 'prueba', 'password' => bcrypt('secreto123'), 'status' => false]);
        $this->from('/login')->post('/login', ['username' => 'prueba', 'password' => 'secreto123'])
            ->assertRedirect('/login')->assertSessionHasErrors('username');
        $this->assertGuest();
        DB::table('users')->where('id', 1)->update(['status' => true]);
        DB::table('rol')->insert(['id' => 1, 'status' => false]);
        DB::table('usuario_rol')->insert(['user_id' => 1, 'rol_id' => 1]);
        $this->post('/login', ['username' => 'prueba', 'password' => 'secreto123'])
            ->assertRedirect('/login')->assertSessionHasErrors('error');
        $this->assertGuest();
    }

    public function testDailyNovationReachesTheDailyGeneratorWithoutTerminatingTheRequest()
    {
        $this->login();
        Schema::create('prestamos', function (Blueprint $t) { $t->increments('id'); $t->boolean('diario'); });
        Schema::create('credit_folder_headers', function (Blueprint $t) {
            $t->increments('id'); $t->decimal('valor_novacion'); $t->timestamps();
        });
        DB::table('prestamos')->insert(['id' => 1, 'diario' => true]);
        DB::table('credit_folder_headers')->insert(['id' => 1, 'valor_novacion' => 100]);
        $component = \Mockery::mock(\App\Http\Livewire\Creditos\CreditosComponet::class)->makePartial();
        $component->shouldReceive('validate')->once()->andReturn([]);
        $component->shouldReceive('generarDiario')->once();
        $component->shouldReceive('generarNormal')->never();
        $component->shouldReceive('guardarCredito')->once();
        $component->shouldReceive('generarDatos')->once();
        $component->prestamoNova = 1;
        $component->idActualizarAnteriorNovacion = 1;
        \App\Models\CreditFolderHeader::withoutEvents(function () use ($component) { $component->guardarCreditoNovacion(); });
        $this->assertTrue($component->diarioLetras);
        $this->assertEquals(0, DB::table('credit_folder_headers')->value('valor_novacion'));
    }

    public function testRouteNamesAreUniqueAndRequiredParametersArePresent()
    {
        $names = [];
        foreach (app('router')->getRoutes() as $route) {
            if ($route->getName()) {
                $this->assertNotContains($route->getName(), $names, $route->uri());
                $names[] = $route->getName();
            }
            if (strpos($route->getActionName(), '@') === false) continue;
            list($class, $method) = explode('@', $route->getActionName());
            $required = array_filter((new \ReflectionMethod($class, $method))->getParameters(), function ($p) {
                return !$p->isOptional() && (!$p->getType() || $p->getType()->isBuiltin());
            });
            $this->assertLessThanOrEqual(count($route->parameterNames()), count($required), $route->uri());
        }
    }

    public function testPortfolioDetailUsesItsHeaderAndExcludesOtherCompanies()
    {
        $this->login();
        Schema::create('cartera_header', function (Blueprint $t) { $t->increments('id'); $t->integer('company_id'); $t->string('tipo'); });
        Schema::create('recurrencia_cartera', function (Blueprint $t) { $t->increments('id'); $t->integer('company_id'); $t->integer('desde'); $t->integer('hasta'); });
        Schema::create('credit_folder_details', function (Blueprint $t) {
            $t->increments('id'); $t->integer('company_id'); $t->string('status'); $t->date('date_vencimiento'); $t->string('code_folder_header');
        });
        Schema::create('credit_folder_headers', function (Blueprint $t) {
            $t->increments('id'); $t->integer('company_id'); $t->string('code'); $t->string('customer_name');
        });
        DB::table('cartera_header')->insert(['id' => 1, 'company_id' => 1, 'tipo' => 'P']);
        DB::table('recurrencia_cartera')->insert(['id' => 1, 'company_id' => 1, 'desde' => 1, 'hasta' => 10]);
        foreach ([1, 2] as $company) {
            DB::table('credit_folder_headers')->insert(['company_id' => $company, 'code' => 'A1', 'customer_name' => 'Cliente ' . $company]);
            DB::table('credit_folder_details')->insert(['company_id' => $company, 'status' => 'PENDIENTE', 'date_vencimiento' => date('Y-m-d'), 'code_folder_header' => 'A1']);
        }
        $view = (new \App\Http\Controllers\RecurrenciaCartera\RecurrenciaCarteraController())->show(1, 1);
        $data = $view->getData();
        $this->assertSame('Letras Pendientes de pagos', $data['texto']);
        $this->assertCount(1, $data['detalles']);
        $this->assertSame('Cliente 1', $data['detalles']->first()->nombresClinetes);
    }

    public function testResultsUseCorrectSavingsAccountsAndExcludeForeignOrReversedMovements()
    {
        $this->login();
        Schema::create('credit_folder_details', function (Blueprint $t) {
            $t->increments('id'); $t->integer('company_id'); $t->string('status'); $t->date('date_pay');
            $t->string('code_folder_header'); $t->decimal('interes_periodo'); $t->decimal('interes_mora');
        });
        Schema::create('credit_folder_headers', function (Blueprint $t) {
            $t->increments('id'); $t->integer('company_id'); $t->string('code'); $t->integer('tipo_prestamo')->nullable();
        });
        Schema::create('type_transactions', function (Blueprint $t) { $t->increments('id'); $t->integer('company_id'); $t->string('name_corto'); });
        Schema::create('customer_movimientos', function (Blueprint $t) {
            $t->increments('id'); $t->integer('company_id'); $t->string('afecta'); $t->integer('type_transaction_id');
            $t->boolean('status'); $t->date('date_created'); $t->decimal('valor_movimiento'); $t->boolean('automatico');
            $t->integer('customer_tipo_ahorro_id')->nullable();
        });
        Schema::create('customer_tipo_ahorros', function (Blueprint $t) { $t->increments('id'); $t->integer('company_id'); $t->integer('tipo_ahorros_id'); });
        Schema::create('tipo_ahorros', function (Blueprint $t) { $t->increments('id'); $t->string('name'); });
        foreach ([1 => 10, 2 => 2000] as $company => $amount) {
            DB::table('credit_folder_details')->insert(['company_id' => $company, 'status' => 'PAGADA', 'date_pay' => '2026-09-06', 'code_folder_header' => 'C1', 'interes_periodo' => $amount, 'interes_mora' => 2]);
        }
        DB::table('credit_folder_headers')->insert(['company_id' => 1, 'code' => 'C1']);
        DB::table('type_transactions')->insert([['id' => 1, 'company_id' => 1, 'name_corto' => 'GAS'], ['id' => 2, 'company_id' => 1, 'name_corto' => 'EG']]);
        DB::table('tipo_ahorros')->insert(['id' => 7, 'name' => 'Ahorro']);
        DB::table('customer_tipo_ahorros')->insert(['id' => 100, 'company_id' => 1, 'tipo_ahorros_id' => 7]);
        $base = ['company_id' => 1, 'afecta' => 'E', 'type_transaction_id' => 2, 'status' => true, 'date_created' => '2026-09-06', 'valor_movimiento' => 3, 'automatico' => true, 'customer_tipo_ahorro_id' => 100];
        DB::table('customer_movimientos')->insert($base);
        DB::table('customer_movimientos')->insert(array_merge($base, ['type_transaction_id' => 1, 'automatico' => false, 'valor_movimiento' => 5]));
        DB::table('customer_movimientos')->insert(array_merge($base, ['status' => false, 'valor_movimiento' => 999]));
        DB::table('customer_movimientos')->insert(array_merge($base, ['company_id' => 2, 'valor_movimiento' => 500]));
        $response = $this->getJson('/resultados/generarResultados/2026-09-01/2026-09-30')->assertOk();
        $this->assertEquals(12, $response->json('ingresos'));
        $this->assertEquals(8, $response->json('egresos'));
        DB::table('type_transactions')->delete();
        $this->getJson('/resultados/generarResultados/2026-09-01/2026-09-30')->assertOk()->assertJsonPath('egresos', 0);
    }

    public function testResultsRejectInvalidDateRangesBeforeRunningQueries()
    {
        $this->login();
        $this->getJson('/resultados/generarResultados/2026-09-30/2026-09-01')->assertStatus(422);
        $this->getJson('/resultados/generarResultados/' . rawurlencode("2026-01-01' OR '1'='1") . '/2026-09-30')->assertStatus(422);
    }
}
