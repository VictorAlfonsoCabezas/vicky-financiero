<?php

namespace Tests\Feature;

use App\Http\Livewire\UserNew\UserNewComponent;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};
use Tests\TestCase;

class UserNewModuleTest extends TestCase
{
    public function testSearchAndStatusAreCombinedAndPaginationResets()
    {
        config(['database.default' => 'user_new_test', 'database.connections.user_new_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            foreach (['username', 'firstname', 'lastname', 'email', 'ruc'] as $field) $table->string($field);
            $table->boolean('status');
        });
        foreach ([1 => true, 2 => false] as $id => $status) {
            DB::table('users')->insert(['id' => $id, 'username' => 'ana'.$id, 'firstname' => 'Ana',
                'lastname' => 'Torres', 'email' => 'ana'.$id.'@example.com', 'ruc' => '12345', 'status' => $status]);
        }
        $component = new UserNewComponent();
        $component->estado = '0';
        foreach (['ana', 'Torres', 'example.com', '12345'] as $term) {
            $component->search = $term;
            $this->assertEquals([2], $component->usersQuery()->pluck('id')->all());
        }
        $component->page = 3;
        $component->updatingSearch();
        $this->assertSame(1, $component->page);
        $component->page = 4;
        $component->updatingEstado();
        $this->assertSame(1, $component->page);
    }
}
