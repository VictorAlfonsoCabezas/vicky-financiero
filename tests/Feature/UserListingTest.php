<?php

namespace Tests\Feature;

use App\Http\Controllers\User\UserController;
use Illuminate\Http\Request;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserListingTest extends TestCase
{
    public function testListingHandlesMissingRolesAndFiltersBeforePagination()
    {
        config(['database.default' => 'listing', 'database.connections.listing' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        Schema::create('users', function (Blueprint $t) {
            $t->increments('id');
            foreach (['firstname', 'lastname', 'username', 'email'] as $field) { $t->string($field); }
            $t->boolean('status');
        });
        Schema::create('rol', function (Blueprint $t) { $t->increments('id'); $t->string('nombre'); });
        Schema::create('usuario_rol', function (Blueprint $t) { $t->increments('id'); $t->integer('user_id'); $t->integer('rol_id'); });
        foreach ([1 => 'Ana', 2 => 'Ana', 3 => 'Luis'] as $id => $name) {
            DB::table('users')->insert(['id' => $id, 'firstname' => $name, 'lastname' => 'Torres',
                'username' => 'user' . $id, 'email' => 'user' . $id . '@example.com', 'status' => true]);
        }
        DB::table('rol')->insert(['id' => 1, 'nombre' => 'Administrador']);
        DB::table('usuario_rol')->insert([['user_id' => 1, 'rol_id' => 1], ['user_id' => 3, 'rol_id' => 99]]);
        $controller = new UserController();
        $data = $controller->verDatos(Request::create('/', 'GET'))->getData(true);
        $this->assertCount(3, $data['data']);
        $this->assertSame('Sin rol', $data['data'][0]['rol_name']);
        $this->assertSame('Sin rol', $data['data'][1]['rol_name']);
        $this->assertSame('Administrador', $data['data'][2]['rol_name']);
        $data = $controller->verDatos(Request::create('/', 'GET', [
            'length' => 1, 'start' => 1, 'order' => [['column' => 0, 'dir' => 'asc']],
            'columns' => [['data' => 'name', 'search' => ['value' => 'Ana Torres']]],
        ]))->getData(true);
        $this->assertSame(3, $data['recordsTotal']);
        $this->assertSame(2, $data['recordsFiltered']);
        $this->assertCount(1, $data['data']);
        $this->assertSame(2, $data['data'][0]['id']);

        Schema::create('company', function (Blueprint $t) {
            $t->increments('id'); $t->string('company_name'); $t->boolean('status');
        });
        $view = $controller->edit(2)->getData();
        $this->assertNull($view['user']->rol);
        $this->assertSame([], $view['empresas']);
        $this->assertEquals(1, $controller->edit(1)->getData()['user']->rol);

        Schema::table('users', function (Blueprint $t) {
            foreach (['company_id', 'company_varias', 'ruc', 'password', 'remember_token', 'token'] as $field) {
                $t->string($field)->nullable();
            }
            $t->timestamps();
        });
        Schema::table('usuario_rol', function (Blueprint $t) {
            $t->boolean('status')->default(true); $t->timestamps();
        });
        DB::table('company')->insert(['id' => 1, 'company_name' => 'Empresa', 'status' => true]);
        $request = Request::create('/', 'PUT', ['empresa' => [1], 'rol' => 1,
            'firstname' => 'Ana', 'lastname' => 'Torres', 'username' => 'user2',
            'email' => 'user2@example.com', 'ruc' => '123', 'password' => 'test-password']);
        $this->app->instance('request', $request);
        $controller->update($request, 2);
        $this->assertSame(1, DB::table('usuario_rol')->where('user_id', 2)->count());
        $this->assertEquals(1, DB::table('usuario_rol')->where('user_id', 2)->value('rol_id'));
    }
}
