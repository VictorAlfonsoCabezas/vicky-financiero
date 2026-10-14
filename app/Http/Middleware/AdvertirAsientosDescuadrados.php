<?php

namespace App\Http\Middleware;

use App\Models\AsientosHeader;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AdvertirAsientosDescuadrados
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $advertencias = $request->attributes->get('asientos_descuadrados', []);
        foreach ($advertencias as $id => $mensaje) {
            // No notificar asientos revertidos por un error posterior o ya corregidos.
            $asiento = AsientosHeader::where('company_id', Auth::check() ? Auth::user()->company_id : null)
                ->where('id', $id)->where('status', true)->first();
            if (!$asiento || (!$asiento->pendiente_contable && AsientosHeader::estaCuadrado($id))) {
                unset($advertencias[$id]);
            }
        }

        if (!$advertencias) {
            return $response;
        }

        if ($request->hasHeader('X-Livewire') && $response instanceof JsonResponse) {
            $payload = $response->getData(true);
            if (isset($payload['effects']) && empty($payload['effects']['redirect'])) {
                foreach ($advertencias as $mensaje) {
                    $payload['effects']['dispatches'][] = [
                        'event' => 'asiento-descuadrado',
                        'data' => ['mensaje' => $mensaje],
                    ];
                }
                $response->setData($payload);
                return $response;
            }
        }

        if ($response->isRedirection() || $response instanceof JsonResponse) {
            $request->session()->flash('asientos_descuadrados', array_values($advertencias));
        }
        return $response;
    }
}
