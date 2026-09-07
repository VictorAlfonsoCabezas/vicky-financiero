<?php

namespace Tests\Feature;

use App\Http\Livewire\Genero\GeneroComponent;
use App\Models\Genero;
use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class OnixCatalogTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'onix_test', 'database.connections.onix_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        Schema::create('genero', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->string('nombre');
            $table->boolean('status')->default(true);
            $table->boolean('defecto')->default(false);
            $table->timestamps();
        });
        $user = new User();
        $user->id = 1;
        $user->company_id = 1;
        $this->actingAs($user);
    }

    public function testCatalogValidatesCreatesEditsAndDeletesInIsolatedDatabase()
    {
        $component = Livewire::test(GeneroComponent::class);
        $component->call('store')->assertHasErrors(['nombre' => 'required']);
        $component->set('nombre', 'Prueba')->call('store')->assertHasNoErrors();
        $record = Genero::firstOrFail();
        $this->assertSame(1, (int) $record->company_id);
        $component->call('editarGenero', $record->id)->set('nombre', 'Editado')->call('store');
        $this->assertSame('Editado', $record->fresh()->nombre);
        $component->call('borrarGenero', $record->id);
        $this->assertSame(0, Genero::count());
    }

    public function testAuthenticatedModuleRendersInsideBootstrapFiveLayout()
    {
        Schema::create('menu', function (Blueprint $table) {
            $table->increments('id'); $table->integer('menu_id')->default(0);
            $table->integer('orden')->default(0); $table->string('nombre');
            $table->string('url'); $table->string('icono')->nullable();
            $table->timestamps();
        });
        Schema::create('rol', function (Blueprint $table) { $table->increments('id'); });
        Schema::create('menu_rol', function (Blueprint $table) {
            $table->integer('rol_id'); $table->integer('menu_id');
        });
        $response = $this->get('/genero');
        $response->assertOk()->assertSee('data-bs-toggle="modal"', false)
            ->assertSee('assets/css/default/app.min.css', false)
            ->assertSee('js/onix-bs5.js', false)->assertSee('livewire/livewire.js', false);
        $response->assertDontSee('adminlte.min.js', false);
        $response->assertSee('id="customer-search-form"', false)
            ->assertSee('id="customer-search-modal"', false)
            ->assertSee('js/customer-search.js', false);

        $this->artisan('onix:menu')->assertExitCode(0);
        $count = \Illuminate\Support\Facades\DB::table('menu')->count();
        $this->assertGreaterThan(70, $count);
        $this->artisan('onix:menu')->assertExitCode(0);
        $this->assertSame($count, \Illuminate\Support\Facades\DB::table('menu')->count());
        $this->assertSame(0, \Illuminate\Support\Facades\DB::table('menu_rol')->count());
    }

    public function testInactiveSessionExpiresWithoutQueryingCustomerRecords()
    {
        $this->withSession(['last_activity' => time() - 1801])->get('/genero')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function testCatalogCannotModifyAnotherCompanyOrClearItsDefault()
    {
        $foreign = Genero::create(['company_id' => 2, 'nombre' => 'Ajeno', 'defecto' => true]);
        $local = Genero::create(['company_id' => 1, 'nombre' => 'Propio']);
        $component = new GeneroComponent();
        $component->cambioDefecto($local->id);
        $this->assertTrue((bool) $foreign->fresh()->defecto);
        foreach (['editarGenero', 'borrarGenero', 'cambioEstado', 'cambioGenero', 'cambioDefecto'] as $method) {
            try {
                $component->$method($foreign->id);
                $this->fail($method . ' permitió acceder a otra empresa');
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                $this->assertSame('Ajeno', $foreign->fresh()->nombre);
            }
        }
        $component->id_seleccionado = $foreign->id;
        $component->nombre = 'Modificado';
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $component->store();
    }
}
