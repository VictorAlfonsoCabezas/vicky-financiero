<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DualLoginTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'dual_login', 'database.connections.dual_login' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        Schema::create('users', function (Blueprint $t) {
            $t->increments('id'); $t->integer('company_id'); $t->string('username'); $t->string('firstname');
            $t->string('password'); $t->boolean('status'); $t->string('remember_token')->nullable();
        });
        Schema::create('rol', function (Blueprint $t) { $t->increments('id'); $t->string('nombre'); $t->boolean('status'); });
        Schema::create('usuario_rol', function (Blueprint $t) { $t->integer('user_id'); $t->integer('rol_id'); });
        Schema::create('customer', function (Blueprint $t) {
            $t->increments('id'); $t->integer('company_id'); $t->integer('user_id'); $t->boolean('status');
            $t->string('code'); $t->string('nombres'); $t->string('apellidos');
        });
        DB::table('rol')->insert(['id' => 1, 'nombre' => 'Activo', 'status' => true]);
        foreach ([1 => 'administrador', 2 => 'cliente'] as $id => $username) {
            DB::table('users')->insert(['id' => $id, 'company_id' => 1, 'username' => $username, 'firstname' => $username, 'password' => bcrypt('secreto123'), 'status' => true]);
            DB::table('usuario_rol')->insert(['user_id' => $id, 'rol_id' => 1]);
        }
        DB::table('customer')->insert(['id' => 10, 'company_id' => 1, 'user_id' => 2, 'status' => true, 'code' => 'SOC-10', 'nombres' => 'Ana', 'apellidos' => 'Torres']);
    }

    public function testBothScreensUseTheTemplateAndPostToTheirOwnLogin()
    {
        $this->get('/login')->assertOk()->assertSee('Acceso administrativo')
            ->assertSee('login-v2')->assertSee('action="' . route('login') . '"', false)->assertSee(route('login2'), false);
        $this->get('/login2')->assertOk()->assertSee('Acceso de clientes')
            ->assertSee('login-with-news-feed')->assertSee('action="' . route('login2') . '"', false)
            ->assertSee('assets/css/default/app.min.css', false)->assertSee(route('login'), false);
    }

    public function testStaffUseAdministrativeLoginAndCannotUseCustomerLogin()
    {
        $this->post('/login2', ['username' => 'administrador', 'password' => 'secreto123'])
            ->assertRedirect('/login2')->assertSessionHasErrors('error');
        $this->assertGuest();
        $this->post('/login', ['username' => 'administrador', 'password' => 'secreto123'])->assertRedirect('/');
        $this->assertAuthenticatedAs(User::find(1));
        $this->assertSame('staff', session('auth.portal'));
        $this->get('/mi-cuenta')->assertForbidden();
    }

    public function testClientLoginIgnoresAdministrativeIntendedUrls()
    {
        $this->withSession(['url.intended' => url('company')])
            ->post('/login2', ['username' => 'cliente', 'password' => 'secreto123'])->assertRedirect('/mi-cuenta');
        $this->assertSame('customer', session('auth.portal'));
        $this->get('/company')->assertForbidden();
        $this->getJson('/clientes/buscar-global?q=Ana')->assertForbidden();
        $this->postJson('/livewire/message/tipo-ahorros.tipo-ahorros-component', [])->assertForbidden();
        $this->get('/login')->assertRedirect('/mi-cuenta');
        $this->get('/onix')->assertRedirect('/mi-cuenta');
        $this->post('/logout2')->assertRedirect('/login2');
        $this->assertGuest();
    }

    public function testClientEnteringThroughAdministrativeLoginStillGoesToTheClientArea()
    {
        $this->post('/login', ['username' => 'cliente', 'password' => 'secreto123'])->assertRedirect('/mi-cuenta');
        $this->get('/usuarios')->assertForbidden();
    }

    public function testGuestAndExpiredClientSessionsReturnToClientLogin()
    {
        $this->get('/mi-cuenta')->assertRedirect('/login2');
        $this->actingAs(User::find(2))->withSession(['last_activity' => time() - 1801])
            ->get('/mi-cuenta')->assertRedirect('/login2');
        $this->assertGuest();
    }

    public function testClientStatusAndLinkAreRecheckedWithoutTrustingSessionFlags()
    {
        $this->actingAs(User::find(2))->get('/company')->assertForbidden();
        DB::table('customer')->where('id', 10)->update(['status' => false]);
        $this->get('/mi-cuenta')->assertRedirect('/login2');
        $this->assertGuest();
        $this->post('/login2', ['username' => 'cliente', 'password' => 'secreto123'])->assertRedirect('/login2')->assertSessionHasErrors('error');
    }

    public function testPortalOnlyShowsTheAuthenticatedCustomersAccountsAndCredits()
    {
        Schema::create('tipo_ahorros', function (Blueprint $t) { $t->increments('id'); $t->string('name'); });
        Schema::create('customer_tipo_ahorros', function (Blueprint $t) {
            $t->increments('id'); $t->integer('company_id'); $t->integer('customer_id');
            $t->integer('tipo_ahorros_id'); $t->string('codigo'); $t->boolean('status');
        });
        Schema::create('credit_folder_headers', function (Blueprint $t) {
            $t->increments('id'); $t->integer('company_id'); $t->integer('customer_id');
            $t->string('code'); $t->decimal('valor_solicitado'); $t->string('status'); $t->date('date_created');
        });
        DB::table('tipo_ahorros')->insert(['id' => 1, 'name' => 'Ahorro voluntario']);
        foreach ([[1, 10, 'PROPIO'], [1, 20, 'AJENO'], [2, 10, 'OTRAEMPRESA']] as $row) {
            DB::table('customer_tipo_ahorros')->insert(['company_id' => $row[0], 'customer_id' => $row[1], 'tipo_ahorros_id' => 1, 'codigo' => 'CUENTA-' . $row[2], 'status' => true]);
            DB::table('credit_folder_headers')->insert(['company_id' => $row[0], 'customer_id' => $row[1], 'code' => 'CREDITO-' . $row[2], 'valor_solicitado' => 100, 'status' => 'ENTREGADO', 'date_created' => '2026-09-01']);
        }
        $this->actingAs(User::find(2))->get('/mi-cuenta?customer_id=20')->assertOk()
            ->assertSee('CUENTA-PROPIO')->assertSee('CREDITO-PROPIO')->assertSee('Ana')
            ->assertDontSee('AJENO')->assertDontSee('OTRAEMPRESA')->assertDontSee('customer-search-form');
    }
}
