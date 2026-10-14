@php
    $diferenciaArqueo = (float) str_replace(',', '', (string) $descuadre);
    $efectivoArqueo = (float) str_replace(',', '', (string) $totalManual);
    $saldoArqueo = (float) str_replace(',', '', (string) $saldoSistema);
@endphp
<div class="row g-3 mb-3">
    <div class="col-md-4"><div class="border rounded p-3 h-100 bg-light"><small class="text-muted d-block mb-1">Efectivo contado</small><strong class="fs-5">$ {{ number_format($efectivoArqueo, 2) }}</strong></div></div>
    <div class="col-md-4"><div class="border rounded p-3 h-100 bg-light"><small class="text-muted d-block mb-1">Saldo del sistema</small><strong class="fs-5">$ {{ number_format($saldoArqueo, 2) }}</strong></div></div>
    <div class="col-md-4"><div class="border rounded p-3 h-100 {{ abs($diferenciaArqueo) >= 0.01 ? 'border-danger' : 'border-success' }}"><small class="text-muted d-block mb-1">Diferencia de arqueo</small><strong class="fs-5 {{ abs($diferenciaArqueo) >= 0.01 ? 'text-danger' : 'text-success' }}">$ {{ number_format($diferenciaArqueo, 2) }}</strong></div></div>
</div>
<div class="alert {{ abs($diferenciaArqueo) >= 0.01 ? 'alert-warning' : 'alert-success' }} mb-0"><i class="fa {{ abs($diferenciaArqueo) >= 0.01 ? 'fa-exclamation-circle' : 'fa-check-circle' }} me-1"></i>{{ abs($diferenciaArqueo) >= 0.01 ? 'El arqueo tiene una diferencia. Verifica el efectivo y deja una observación antes de cerrar.' : 'El efectivo contado coincide con el saldo del sistema.' }}</div>
