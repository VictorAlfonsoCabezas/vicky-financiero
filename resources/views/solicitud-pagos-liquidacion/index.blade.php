@extends('layouts.app')
@section('title', 'Solicitudes de liquidación')
@section('custom_css_rules')
<style>
    .liquidation-module .panel { border-radius: 6px; overflow: hidden; }
    .liquidation-module .panel-heading { padding: 14px 18px; }
    .liquidation-module .form-label { font-weight: 600; }
    .liquidation-table { font-size: 13px; }
    .liquidation-table th { background: #f0f3f5; color: #495057; padding: 14px; white-space: nowrap; }
    .liquidation-table td { padding: 12px 14px; }
    .liquidation-money { font-variant-numeric: tabular-nums; white-space: nowrap; }
    .liquidation-module .pagination { margin-bottom: 0; }
    .liquidation-module .modal-content { border-radius: 6px; overflow: hidden; }
    .liquidation-module .modal-header, .liquidation-module .modal-body { padding: 20px 24px; }
    .liquidation-module .modal-title { font-size: 16px; font-weight: 600; }
    .liquidation-module .modal-footer { flex-shrink: 0; padding: 16px 24px; background: #f8f9fa; gap: 8px; }
    .liquidation-section { font-size: 14px; font-weight: 600; padding-bottom: 12px; margin-bottom: 16px; border-bottom: 1px solid #dee2e6; }
    .liquidation-section > i { color: #348fe2; }
    .liquidation-summary { padding: 16px; border-radius: 6px; background: #cce3f8; color: #15395a; height: 100%; }
    .liquidation-module dd { overflow-wrap: anywhere; }
    @media(max-width: 767px) { .liquidation-module .page-header { font-size: 24px; } }
</style>
@stop
@section('content')
    @livewire('solicitud-pagos-liquidacion.solicitud-pagos-liquidacion')
@endsection
@section('scripts')
<script>
    document.addEventListener('click', function(event) {
        const button = event.target.closest('[data-liquidation-action]');
        if (!button || button.disabled || Swal.isVisible()) return;
        const component = Livewire.find(button.closest('[wire\\:id]').getAttribute('wire:id'));
        const aprobar = button.dataset.liquidationAction === 'aprobarSolicitud';
        Swal.fire({
            title: aprobar ? '¿Aprobar esta liquidación?' : '¿Rechazar las transferencias?',
            text: aprobar ? 'Se registrará la liquidación del crédito por $ ' + button.dataset.total + '.' : 'Las transferencias se rechazarán. Las otras formas de pago recibidas se conservarán como abono al crédito.',
            icon: 'question', showCancelButton: true, focusCancel: true,
            confirmButtonText: aprobar ? 'Sí, aprobar' : 'Sí, rechazar', cancelButtonText: 'Cancelar',
            buttonsStyling: false, customClass: {confirmButton: 'btn ' + (aprobar ? 'btn-success' : 'btn-danger') + ' me-2', cancelButton: 'btn btn-white'},
            showLoaderOnConfirm: true, allowOutsideClick: () => !Swal.isLoading(), allowEscapeKey: () => !Swal.isLoading(),
            preConfirm: async () => {
                try {
                    if (!aprobar) await component.set('observacionRechazo', document.getElementById('liquidation-reason').value, true);
                    await component.call(button.dataset.liquidationAction, Number(button.dataset.credit), button.dataset.date, button.dataset.time);
                } catch(error) { Swal.showValidationMessage('No se pudo completar la operación. Revisa los datos de la solicitud.'); return false; }
            }
        });
    });
    window.addEventListener('close-modal-liquidacion', function() { $('#modalLiquidacion').modal('hide'); });
    window.addEventListener('alerta-liquidacion', function(event) {
        Swal.fire({title: 'Solicitud de liquidación', text: event.detail.mensaje, icon: event.detail.tipo,
            buttonsStyling: false, customClass: {confirmButton: 'btn btn-primary'}});
    });
</script>
@stop