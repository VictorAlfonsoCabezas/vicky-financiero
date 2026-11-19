<?php

namespace Tests\Feature;

use App\Http\Livewire\CalificacionSocios\CalificacionSociosComponent;
use App\Models\{CreditFolderDetail, CreditFolderHeader, Customer, CustomerMovimiento, TypeTransaction};
use App\Services\CalificacionCrediticia;
use App\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class CalificacionSociosTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'score_test',
            'database.connections.score_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => ''],
        ]);
        $campos = [
            Customer::class => ['company_id', 'code', 'nombres', 'apellidos', 'numero_documento'],
            CreditFolderHeader::class => ['company_id', 'customer_id', 'code', 'status'],
            CreditFolderDetail::class => ['company_id', 'code_folder_header', 'numero_cuota', 'status', 'date_vencimiento',
                'date_pay', 'valor_cuota', 'valor_pagado', 'saldo_anterior_cuota', 'faltante_anterior_cuota',
                'faltante_prox_cuota', 'adelanto_prox_cuota', 'interes_mora', 'obervation_pago'],
            CustomerMovimiento::class => ['company_id', 'customer_id', 'credit_folder_details_id',
                'type_transaction_id', 'status', 'valor_movimiento', 'date_created'],
            TypeTransaction::class => ['company_id', 'name_corto'],
        ];
        foreach ($campos as $clase => $columnas) {
            Schema::create((new $clase)->getTable(), function (Blueprint $tabla) use ($columnas) {
                $tabla->increments('id');
                foreach ($columnas as $columna) {
                    $tabla->string($columna)->nullable();
                }
            });
        }
        $usuario = new User();
        $usuario->id = 1;
        $usuario->company_id = 10;
        $this->actingAs($usuario);
    }

    public function testElScoreDistingueAtrasoArrastradoCierresYAbonosAnulados(): void
    {
        foreach (['ACT' => 'ENTREGADO', 'LIQ' => 'PAGADO', 'NOV' => 'NOVACION'] as $codigo => $estado) {
            DB::table('credit_folder_headers')->insert([
                'company_id' => 10, 'customer_id' => 9, 'code' => $codigo, 'status' => $estado,
            ]);
        }
        $cuota = function ($codigo, $numero, $vence, array $datos = []) {
            return DB::table('credit_folder_details')->insertGetId($datos + [
                'company_id' => 10, 'code_folder_header' => $codigo, 'numero_cuota' => $numero,
                'status' => 'PENDIENTE', 'date_vencimiento' => $vence, 'valor_cuota' => 100,
            ]);
        };
        $primera = $cuota('ACT', 1, '2026-09-01', [
            'status' => 'PAGADA', 'date_pay' => '2026-09-01', 'valor_pagado' => 60, 'faltante_prox_cuota' => 40,
        ]);
        $cuota('ACT', 2, '2026-10-01', [
            'status' => 'PAGADA', 'date_pay' => '2026-10-01', 'valor_pagado' => 140, 'faltante_anterior_cuota' => 40,
        ]);
        $tercera = $cuota('ACT', 3, '2026-10-10');
        $cuota('ACT', 4, '2026-12-01');
        $cuota('LIQ', 1, '2026-09-01', [
            'status' => 'PAGADA', 'date_pay' => '2026-08-01', 'valor_pagado' => 80,
            'obervation_pago' => 'PAGA LA TOTALIDAD DEL CREDITO LIQ',
        ]);
        $cuota('NOV', 1, '2026-08-01', [
            'status' => 'PAGADA', 'date_pay' => '2026-09-01', 'obervation_pago' => 'NOVACION',
        ]);
        DB::table('type_transactions')->insert(['id' => 1, 'company_id' => 10, 'name_corto' => 'PC']);
        foreach ([[25, 1, 10], [75, 0, 10], [75, 3, 10], [75, 1, 20]] as [$valor, $estado, $empresa]) {
            DB::table('customer_movimientos')->insert([
                'company_id' => $empresa, 'customer_id' => 9, 'credit_folder_details_id' => $tercera,
                'type_transaction_id' => 1, 'status' => $estado, 'valor_movimiento' => $valor, 'date_created' => '2026-10-10',
            ]);
        }

        $resultado = app(CalificacionCrediticia::class)->calcular(9, CarbonImmutable::parse('2026-11-18'));
        $this->assertSame(3, $resultado['historial']);
        $this->assertSame(2, $resultado['puntuales']);
        $this->assertSame('2026-10-01', $resultado['cuotas']->firstWhere('id', $primera)['fecha_pago']);
        $this->assertSame(7500, $resultado['saldo_vencido']);
        $this->assertEquals(125, $resultado['factores']['liquidacion']['puntos']);
        $this->assertSame(731, $resultado['score']);
        $this->assertFalse($resultado['provisional']);
    }

    public function testLivewireBuscaPorNombreYMuestraLaFaltaDeHistorial(): void
    {
        DB::table('customer')->insert([
            ['id' => 9, 'company_id' => 10, 'nombres' => 'Ana', 'apellidos' => 'Lopez'],
            ['id' => 10, 'company_id' => 20, 'nombres' => 'Ana', 'apellidos' => 'Lopez externa'],
        ]);

        Livewire::test(CalificacionSociosComponent::class)
            ->set('buscar', 'Ana Lopez')
            ->assertSee('Ana Lopez')
            ->assertDontSee('Lopez externa')
            ->call('seleccionarSocio', 9)
            ->assertSee('Sin historial suficiente');
    }
}
