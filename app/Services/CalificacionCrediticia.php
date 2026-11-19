<?php

namespace App\Services;

use App\Models\CreditFolderDetail;
use App\Models\CreditFolderHeader;
use App\Models\CustomerMovimiento;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;

class CalificacionCrediticia
{
    private const PUNTOS = ['puntualidad' => 450, 'liquidacion' => 250, 'vigentes' => 300];
    private const ESTADOS = ['ENTREGADO', 'PAGADO', 'FINALIZADO', 'NOVACION', 'REESTRUCTURADO', 'REESTRUCTURACION'];

    public function empresaId(): int
    {
        abort_unless(Auth::user() && Auth::user()->company_id, 403);

        return (int) Auth::user()->company_id;
    }

    public function calcular(int $socioId, ?CarbonImmutable $hoy = null): array
    {
        $empresa = $this->empresaId();
        $hoy = ($hoy ?? CarbonImmutable::today())->startOfDay();
        $creditos = CreditFolderHeader::where('company_id', $empresa)->where('customer_id', $socioId)
            ->whereIn('status', self::ESTADOS)->orderByDesc('id')->get(['id', 'code', 'status']);
        $cuotas = CreditFolderDetail::where('company_id', $empresa)->whereIn('code_folder_header', $creditos->pluck('code'))
            ->where(function ($query) {
                $query->whereNotIn('status', ['ANULADA', 'CANCELADA', 'ELIMINADA'])->orWhereNull('status');
            })
            ->orderBy('numero_cuota')->orderBy('id')->get()->groupBy('code_folder_header');
        $movimientos = CustomerMovimiento::query()
            ->join('type_transactions as tipo', 'tipo.id', '=', 'customer_movimientos.type_transaction_id')
            ->where('tipo.company_id', $empresa)->whereIn('tipo.name_corto', ['PC', 'PCA', 'PCI'])
            ->where('customer_movimientos.company_id', $empresa)->where('customer_movimientos.customer_id', $socioId)
            ->whereIn('customer_movimientos.credit_folder_details_id', $cuotas->flatten(1)->pluck('id'))
            ->orderBy('customer_movimientos.date_created')->orderBy('customer_movimientos.id')
            ->get(['customer_movimientos.credit_folder_details_id', 'customer_movimientos.status',
                'customer_movimientos.valor_movimiento', 'customer_movimientos.date_created'])
            ->groupBy('credit_folder_details_id');

        $filas = collect();
        $resumen = collect();
        foreach ($creditos as $credito) {
            $reestructurado = in_array($credito->status, ['NOVACION', 'REESTRUCTURADO', 'REESTRUCTURACION'], true);
            $cierre = in_array($credito->status, ['PAGADO', 'FINALIZADO'], true);
            $letras = $this->evaluarCuotas($cuotas->get($credito->code, collect()), $movimientos, $hoy, $cierre, $reestructurado);
            $liquidado = $letras->isNotEmpty() && !$reestructurado && $letras->every(function ($letra) {
                return !$letra['incompleta'] && $letra['importe'] === $letra['cubierto'];
            });
            $trasladoConfirmado = $letras->contains('trasladada', true);
            $estado = $reestructurado
                ? ($trasladoConfirmado ? 'Reestructurado' : 'Reestructuración por verificar')
                : ($liquidado ? 'Liquidado' : ($cierre ? 'Cierre por verificar' : 'Vigente'));
            $resumen->push([
                'id' => $credito->id, 'codigo' => $credito->code, 'estado' => $estado,
                'cuotas' => $letras->count(), 'cubiertas' => $letras->where('completa', true)->count(),
            ]);
            foreach ($letras as $letra) {
                $filas->push($letra + ['credito' => $credito->code, 'vigente' => $estado === 'Vigente']);
            }
        }

        $historial = $filas->filter(function ($letra) {
            return $letra['completa'] && $letra['fecha_pago'] !== null && !$letra['incompleta'];
        });
        $cierres = $resumen->whereIn('estado', ['Liquidado', 'Reestructurado']);
        $exigibles = $filas->where('vigente', true)->where('vencida', true)->where('incompleta', false);
        $importeExigible = $exigibles->sum('importe');
        $saldoVencido = $exigibles->sum('saldo');
        $penalidadSaldo = $exigibles->sum(function ($letra) {
            return $letra['saldo'] * (1 - $this->factorAtraso($letra['dias_atraso']));
        });
        $factores = [
            'puntualidad' => $historial->avg(function ($letra) {
                return $this->factorAtraso($letra['dias_atraso']);
            }),
            'liquidacion' => $cierres->isEmpty() ? null : $cierres->where('estado', 'Liquidado')->count() / $cierres->count(),
            'vigentes' => $importeExigible > 0
                ? 1 - (0.7 * $saldoVencido + 0.3 * $penalidadSaldo) / $importeExigible
                : null,
        ];

        $puntos = 0;
        $pesoDisponible = 0;
        foreach ($factores as $clave => $factor) {
            $maximo = self::PUNTOS[$clave];
            $factores[$clave] = ['puntos' => $factor === null ? null : round($factor * $maximo, 1), 'maximo' => $maximo];
            if ($factor !== null) {
                $puntos += $factor * $maximo;
                $pesoDisponible += $maximo;
            }
        }

        $incompletas = $filas->where('incompleta', true)->count();
        $score = $pesoDisponible > 0 && ($historial->isNotEmpty() || $importeExigible > 0)
            ? (int) round($puntos * 1000 / $pesoDisponible)
            : null;

        return [
            'score' => $score, 'provisional' => $pesoDisponible < 1000 || $incompletas > 0
                || $resumen->whereIn('estado', ['Cierre por verificar', 'Reestructuración por verificar'])->isNotEmpty(),
            'peso_disponible' => $pesoDisponible, 'factores' => $factores, 'creditos' => $resumen,
            'cuotas' => $filas->sortByDesc('vencimiento')->values(), 'saldo_vencido' => $saldoVencido,
            'atrasadas' => $exigibles->where('saldo', '>', 0)->count(),
            'puntuales' => $historial->where('dias_atraso', 0)->count(), 'historial' => $historial->count(),
            'datos_incompletos' => $incompletas, 'corte' => $hoy->toDateString(),
        ];
    }

    private function evaluarCuotas($cuotas, $movimientos, CarbonImmutable $hoy, bool $cierre, bool $reestructurado)
    {
        $filas = collect();
        $adelantosPrevios = [];
        foreach ($cuotas as $cuota) {
            $vencimiento = $this->fecha($cuota->date_vencimiento);
            $fechaPago = $this->fecha($cuota->date_pay);
            $importe = $this->centavos($cuota->valor_cuota);
            $trasladada = $reestructurado && strtoupper(trim((string) $cuota->obervation_pago)) === 'NOVACION';
            $liquidacion = $cierre && $cuota->status === 'PAGADA' && $fechaPago
                && strpos(strtoupper((string) $cuota->obervation_pago), 'PAGA LA TOTALIDAD DEL CREDITO') === 0;
            $registros = $movimientos->get($cuota->id, collect());
            $confirmados = $registros->filter(function ($movimiento) use ($hoy) {
                $fecha = $this->fecha($movimiento->date_created);
                return in_array((string) $movimiento->status, ['', '1'], true) && $fecha && $fecha <= $hoy;
            });
            $aplicado = 0;
            $adelanto = $trasladada ? 0 : $this->centavos($cuota->saldo_anterior_cuota);
            $fechaAdelanto = null;
            $adelantoPorRastrear = $adelanto;
            foreach ($adelantosPrevios as $indice => $previo) {
                if ($adelantoPorRastrear === 0) {
                    break;
                }
                $consumido = min($previo['disponible'], $adelantoPorRastrear);
                if ($consumido > 0) {
                    if (!$fechaAdelanto || $previo['fecha'] > $fechaAdelanto) {
                        $fechaAdelanto = $previo['fecha'];
                    }
                    $adelantosPrevios[$indice]['disponible'] -= $consumido;
                    $adelantoPorRastrear -= $consumido;
                }
            }

            $arrastre = 0;
            if ($liquidacion && $fechaPago <= $hoy) {
                $aplicado = $importe;
            } elseif (!$trasladada && ($fechaPago === null || $fechaPago <= $hoy)) {
                $neto = 0;
                if ($cuota->status === 'PAGADA' && ($registros->isEmpty() || $confirmados->isNotEmpty())) {
                    $neto = $this->centavos($cuota->valor_pagado);
                } elseif ($confirmados->isNotEmpty()) {
                    $neto = $this->centavos($confirmados->sum('valor_movimiento'));
                }
                if ($registros->isNotEmpty()) {
                    $neto = min($neto, $this->centavos($confirmados->sum('valor_movimiento')));
                }
                $neto = max(0, $neto - $this->centavos($cuota->interes_mora));
                $arrastre = min($neto, $this->centavos($cuota->faltante_anterior_cuota));
                $aplicado = $neto - $arrastre;
            }
            $cubierto = min($importe, $aplicado + $adelanto);
            $requerido = max(0, $importe - $adelanto);
            $fechaCompleta = $cubierto === $importe && $importe > 0
                ? $this->fechaCompleta($confirmados, $requerido, $fechaPago, $aplicado)
                : null;
            if ($cubierto === $importe && $adelanto > 0 && $fechaAdelanto
                && ($fechaCompleta === null || $fechaAdelanto > $fechaCompleta)) {
                $fechaCompleta = $fechaAdelanto;
            }
            $fechaAplicacion = $fechaPago ?? $this->fecha(optional($confirmados->last())->date_created);
            $excedente = min(max(0, $aplicado - $requerido), $this->centavos($cuota->adelanto_prox_cuota));
            if ($excedente > 0 && $fechaAplicacion) {
                $adelantosPrevios[] = ['disponible' => $excedente, 'fecha' => $fechaAplicacion];
            }

            foreach ($filas as $indice => $anterior) {
                if ($arrastre <= 0) {
                    break;
                }
                if (!$anterior['arrastrada'] || $anterior['saldo'] <= 0) {
                    continue;
                }
                $recuperado = min($arrastre, $anterior['saldo']);
                $anterior['cubierto'] += $recuperado;
                $anterior['saldo'] -= $recuperado;
                $arrastre -= $recuperado;
                if ($anterior['saldo'] === 0) {
                    $anterior['completa'] = true;
                    $anterior['fecha_pago'] = optional($fechaAplicacion)->toDateString();
                    $anterior['incompleta'] = !$fechaAplicacion || !$anterior['vencimiento'];
                    $anterior['dias_atraso'] = $this->diasAtraso($this->fecha($anterior['vencimiento']), $fechaAplicacion ?? $hoy);
                }
                $filas[$indice] = $anterior;
            }

            $completa = $importe > 0 && $cubierto === $importe;
            $filas->push([
                'id' => $cuota->id, 'numero' => $cuota->numero_cuota, 'vencimiento' => optional($vencimiento)->toDateString(),
                'fecha_pago' => optional($fechaCompleta)->toDateString(), 'importe' => $importe, 'cubierto' => $cubierto,
                'saldo' => $importe - $cubierto, 'completa' => $completa, 'trasladada' => $trasladada,
                'arrastrada' => (float) $cuota->faltante_prox_cuota > 0, 'vencida' => $vencimiento && $vencimiento < $hoy,
                'incompleta' => !$vencimiento || $importe === 0 || ($completa && !$fechaCompleta)
                    || (!$trasladada && $cuota->status === 'PAGADA' && !$completa && $cubierto === 0 && $registros->isEmpty()),
                'dias_atraso' => $this->diasAtraso($vencimiento, $fechaCompleta ?? $hoy),
            ]);
        }

        return $filas;
    }

    private function fechaCompleta($movimientos, int $requerido, ?CarbonImmutable $fechaPago, int $aplicado): ?CarbonImmutable
    {
        if ($requerido === 0 || $movimientos->isEmpty()) {
            return $fechaPago;
        }
        $ajuste = max(0, $this->centavos($movimientos->sum('valor_movimiento')) - $aplicado);
        $acumulado = 0;
        foreach ($movimientos as $movimiento) {
            $acumulado += $this->centavos($movimiento->valor_movimiento);
            if ($acumulado - $ajuste >= $requerido) {
                return $this->fecha($movimiento->date_created);
            }
        }

        return $fechaPago;
    }

    private function fecha($valor): ?CarbonImmutable
    {
        $fecha = substr((string) $valor, 0, 10);
        if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/D', $fecha, $partes) || !checkdate($partes[2], $partes[3], $partes[1])) {
            return null;
        }

        return CarbonImmutable::parse($fecha)->startOfDay();
    }

    private function diasAtraso(?CarbonImmutable $vencimiento, CarbonImmutable $fecha): int
    {
        return $vencimiento && $fecha > $vencimiento ? (int) $vencimiento->diffInDays($fecha) : 0;
    }

    private function factorAtraso(int $dias): float
    {
        if ($dias === 0) return 1;
        if ($dias <= 7) return 0.75;
        if ($dias <= 30) return 0.5;
        if ($dias <= 60) return 0.25;

        return 0;
    }

    private function centavos($valor): int
    {
        return max(0, (int) round((float) $valor * 100));
    }
}
