<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CustomerSearchTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'customer_search_test', 'database.connections.customer_search_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        Schema::create('customer', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('created_at')->nullable();
            $table->integer('company_id');
            $table->string('code');
            $table->string('nombres');
            $table->string('apellidos');
            $table->string('numero_documento');
            foreach (['documento', 'direccion', 'latitud', 'longitud', 'telefono', 'telefono_2', 'telefono_3', 'customer_tarifa_id', 'customer_tarifa_interes', 'correo', 'customer_tarifa_name'] as $field) {
                $table->string($field)->nullable();
            }
        });
    }

    private function loginToCompany()
    {
        $user = new User();
        $user->id = 1;
        $user->company_id = 10;
        $this->actingAs($user);
    }

    private function customer($company = 10, $code = 'SOC-001')
    {
        return DB::table('customer')->insertGetId([
            'company_id' => $company, 'code' => $code, 'nombres' => 'Ana Maria',
            'apellidos' => 'Torres Lopez', 'numero_documento' => '0991234567',
        ]);
    }

    public function testSearchRequiresAuthentication()
    {
        $this->getJson('/clientes/buscar-global?q=Ana')->assertUnauthorized();
    }

    public function testProfileSearchAndSelectionRespectTheCompany()
    {
        $this->loginToCompany();
        $id = $this->customer();
        $foreignId = $this->customer(20);
        $component = new \App\Http\Livewire\Clientes\ClientesComponent();
        foreach (['Ana', 'Torres', '099123', ''] as $term) {
            $component->search = $term;
            $this->assertEquals([$id], $component->customerSearchQuery()->get()->pluck('id')->all());
        }
        $component->page = 3;
        $component->updatingSearch();
        $this->assertSame(1, $component->page);
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $component->seleccionarCliente($foreignId);
    }

    public function testProfileTabsOnlyOpenExistingClientSections()
    {
        $component = new \App\Http\Livewire\Clientes\ClientesComponent();
        $component->selectTab('archivos');
        $this->assertSame('activity', $component->activeTab);
        $component->id_seleccionado = 1;
        $component->selectTab('archivos');
        $this->assertSame('archivos', $component->activeTab);
        $component->selectTab('invalid');
        $this->assertSame('archivos', $component->activeTab);
        $component->selectTab('timeline');
        $this->assertSame('timeline', $component->activeTab);
    }

    public function testCreditClientSearchKeepsAllAlternativesWithinTheCompany()
    {
        $this->loginToCompany();
        $id = $this->customer();
        $foreignId = $this->customer(20);
        $component = new \App\Http\Livewire\Creditos\CreditosComponet();
        foreach (['Ana', 'Torres', '099123', ''] as $term) {
            $component->search = $term;
            $this->assertEquals([$id], $component->customerSearchQuery()->pluck('id')->all());
        }
        $component->page = 4;
        $component->updatingSearch();
        $this->assertSame(1, $component->page);
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $component->seleccionarCliente($foreignId);
    }

    public function testSearchFindsNamesDocumentsAndCodesWithinTheActiveCompany()
    {
        $this->loginToCompany();
        $id = $this->customer();
        $this->customer(20);
        foreach (['Ana Torres', 'Lopez Maria', '099123', 'SOC-001'] as $query) {
            $this->getJson('/clientes/buscar-global?' . http_build_query(['q' => $query]))
                ->assertOk()->assertJsonPath('total', 1)
                ->assertJsonPath('customers.0.name', 'Ana Maria Torres Lopez')
                ->assertJsonPath('customers.0.credit_url', url('creditos/' . $id))
                ->assertJsonPath('customers.0.savings_url', url('cuentas/' . $id))
                ->assertJsonPath('customers.0.customer_url', url('clientes/' . $id));
        }
    }

    public function testSearchPaginatesAndReturnsAnEmptyListForNoMatches()
    {
        $this->loginToCompany();
        for ($i = 0; $i < 23; $i++) $this->customer(10, 'SOC-' . $i);
        $this->getJson('/clientes/buscar-global?q=Ana')->assertOk()
            ->assertJsonCount(20, 'customers')->assertJsonPath('total', 23)->assertJsonPath('last_page', 2);
        $this->getJson('/clientes/buscar-global?q=Ana&page=2')->assertOk()
            ->assertJsonCount(3, 'customers')->assertJsonPath('page', 2);
        $this->getJson('/clientes/buscar-global?q=Inexistente')->assertOk()
            ->assertJsonCount(0, 'customers')->assertJsonPath('total', 0);
    }

    public function testInvalidQueriesAreRejected()
    {
        $this->loginToCompany();
        foreach (['', ' ', 'a', str_repeat('a', 121)] as $query) {
            $this->getJson('/clientes/buscar-global?' . http_build_query(['q' => $query]))
                ->assertStatus(422)->assertJsonValidationErrors('q');
        }
    }

    public function testLegacyCashierSearchAlsoRespectsTheActiveCompany()
    {
        $this->loginToCompany();
        $this->customer();
        $this->customer(20);
        foreach (['099123', 'Ana', 'Torres', 'SOC-001'] as $term) {
            $response = $this->getJson('/customer/buscar/' . $term)->assertOk()->assertJsonCount(1);
            $this->assertSame(10, (int) $response->json('0.company_id'));
        }
    }
}
