<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class RecurrenciaCarteraViewTest extends TestCase
{
    public function testIndexRendersRecurrencesAndDeleteMethodInsteadOfJavascriptSource()
    {
        $user = new User();
        $user->id = 1;
        $user->firstname = 'Admin';
        $this->actingAs($user);
        $this->app['events']->forget('composing: layouts.app');
        View::share('menusComposer', []);
        $row = (object) ['id' => 7, 'orden' => 1, 'desde' => 1, 'hasta' => 30,
            'dias' => 30, 'date_created' => '2026-09-12', 'hour_created' => '10:00:00', 'user_created_name' => 'Admin'];
        $html = view('recurrencia-cartera.index', ['datos' => collect([$row])])->render();
        $this->assertStringContainsString('Lista de Recurrencias', $html);
        $this->assertStringContainsString('name="_method" value="delete"', $html);
        $this->assertStringContainsString('recurrenciaCartera/7', $html);
        $this->assertStringNotContainsString('function isFixed(element)', $html);
    }
}
