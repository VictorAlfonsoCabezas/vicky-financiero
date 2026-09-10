<?php

namespace Tests\Feature;

use App\Http\Livewire\Cuentas\CuentasComponent;
use App\Http\Livewire\TipoAhorros\TipoAhorrosComponent;
use Tests\TestCase;

class SavingsPaginationTest extends TestCase
{
    public function testChangingSearchReturnsBothModulesToTheFirstPage()
    {
        foreach ([new CuentasComponent(), new TipoAhorrosComponent()] as $component) {
            $component->page = 3;
            $component->updatingSearch();
            $this->assertSame(1, $component->page);
        }
    }

    public function testChangingSavingsStatusResetsPagination()
    {
        $component = new TipoAhorrosComponent();
        $component->page = 4;
        $component->updatingEstado();
        $this->assertSame(1, $component->page);
    }

    public function testLoanCatalogFiltersResetPagination()
    {
        $component = new \App\Http\Livewire\Prestamos\PrestamosComponent();
        $component->page = 3;
        $component->updatingSearch();
        $this->assertSame(1, $component->page);
        $component->page = 3;
        $component->updatingTipoFiltro();
        $this->assertSame(1, $component->page);
    }
}
