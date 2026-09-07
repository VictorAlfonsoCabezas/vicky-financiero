<?php

namespace Tests\Feature;

use Tests\TestCase;

class OnixModulesTest extends TestCase
{
    public function testEveryRegisteredControllerActionExists()
    {
        foreach (app('router')->getRoutes() as $route) {
            if (strpos($route->getActionName(), '@') === false) continue;
            list($class, $method) = explode('@', $route->getActionName());
            $this->assertTrue(method_exists($class, $method), $route->uri() . ': ' . $route->getActionName());
        }
    }

    public function testFinancialModulesRequireAuthentication()
    {
        foreach (['onix', 'tipo-ahorros', 'cajas', 'cartera', 'plan-cuentas', 'asientos', 'balance-general', 'customer-movimientos', 'credit-folder-audits'] as $uri) {
            $this->get('/' . $uri)->assertRedirect('/login');
        }
    }

    public function testLivewireEntryPointsExist()
    {
        foreach (['TipoAhorros', 'Cajas', 'Asientos', 'Cartera', 'PlanCuentas', 'BalanceGeneral'] as $module) {
            $class = 'App\\Http\\Livewire\\' . $module . '\\' . $module . 'Component';
            $this->assertTrue(is_subclass_of($class, \Livewire\Component::class), $class);
        }
    }
}
