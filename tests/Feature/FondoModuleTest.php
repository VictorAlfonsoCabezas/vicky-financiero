<?php

namespace Tests\Feature;

use App\Http\Controllers\Fondo\FondoController;
use App\Models\FondoHeader;
use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Schema, View};
use Tests\TestCase;

class FondoModuleTest extends TestCase
{
    public function testViewRendersBalancesAndTransactionForms()
    {
        $user = new User(); $user->id = 1; $this->actingAs($user);
        $this->app['events']->forget('composing: layouts.app'); View::share('menusComposer', []);
        $fund = (object) ['id'=>1, 'code'=>'F-001', 'valor_inicial'=>100, 'valor_ingreso'=>20,
            'valor_egreso'=>10, 'valor_total'=>110, 'date_created'=>'2026-09-12', 'hour_created'=>'10:00'];
        $html = view('fondo.index', ['fondos'=>collect([$fund])])->render();
        $this->assertStringContainsString('110.00', $html);
        $this->assertStringContainsString('name="fondo_id"', $html);
        $this->assertStringContainsString('data-fund="1"', $html);
        $this->assertStringContainsString('data-type="EG"', $html);
    }

    public function testForeignFundCannotReceiveAManualTransaction()
    {
        config(['database.default'=>'fondo_test', 'database.connections.fondo_test'=>[
            'driver'=>'sqlite', 'database'=>':memory:', 'prefix'=>'']]);
        $table = (new FondoHeader())->getTable();
        Schema::create($table, function (Blueprint $t) { $t->increments('id'); $t->integer('company_id'); $t->boolean('status'); });
        DB::table($table)->insert(['id'=>2, 'company_id'=>20, 'status'=>true]);
        $user = new User(); $user->id = 1; $user->company_id = 10; $this->actingAs($user);
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        (new FondoController())->storeTransacciones(Request::create('/', 'POST', [
            'fondo_id'=>2, 'type_fondo'=>'IN', 'valor_fondo'=>10, 'observation'=>'Prueba aislada']));
    }
}
