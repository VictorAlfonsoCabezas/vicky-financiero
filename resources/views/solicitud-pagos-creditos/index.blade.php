@extends('layouts.app')
@section('title', 'Solicitudes de pagos de créditos')
@section('custom_css_rules')
<style>
    .credit-payments-module .panel { border-radius: 6px; overflow: hidden; }
    .credit-payments-module .panel-heading { padding: 14px 18px; }
    .credit-payments-module .form-label { font-weight: 600; }
    .credit-payments-table { font-size: 13px; }
    .credit-payments-table th { background: #f0f3f5; color: #495057; padding: 14px; white-space: nowrap; }
    .credit-payments-table td { padding: 12px 14px; }
    .credit-payments-money { font-variant-numeric: tabular-nums; white-space: nowrap; }
    .credit-payments-module .pagination { margin-bottom: 0; }
    .credit-payments-module .modal-content { border-radius: 6px; overflow: hidden; }
    .credit-payments-module .modal-header, .credit-payments-module .modal-body { padding: 20px 24px; }
    .credit-payments-module .modal-title { font-size: 16px; font-weight: 600; }
    .credit-payments-module .modal-footer { flex-shrink: 0; padding: 16px 24px; background: #f8f9fa; gap: 8px; }
    .credit-payments-section { font-size: 14px; font-weight: 600; padding-bottom: 12px; margin-bottom: 16px; border-bottom: 1px solid #dee2e6; }
    .credit-payments-section > i { color: #348fe2; }
    .credit-payments-summary { padding: 16px; border-radius: 6px; background: #cce3f8; color: #15395a; height: 100%; }
    .credit-payments-module dd { overflow-wrap: anywhere; }
    @media(max-width: 767px) { .credit-payments-module .page-header { font-size: 24px; } }
</style>
@stop
@section('content')
    @livewire('solicitud-pagos-creditos.solicitud-pagos-creditos')
@endsection
@section('scripts')
<script>
    document.addEventListener('click', function(event) {
        const button = event.target.closest('[data-payment-action]');
        if (!button || button.disabled || Swal.isVisible()) return;
        const component = Livewire.find(button.closest('[wire\\:id]').getAttribute('wire:id'));
        const aprobar = button.dataset.paymentAction === 'aprobarSolicitud';
        Swal.fire({
            title: aprobar ? '¿Aprobar este pago?' : '¿Rechazar los pagos no efectivos?',
            text: aprobar ? 'Se registrará el pago recibido por $ ' + button.dataset.total + '.' : 'Las transferencias y otros pagos no efectivos se rechazarán. El efectivo recibido se conservará y se aplicará a la cuota.',
            icon: 'question', showCancelButton: true, focusCancel: true,
            confirmButtonText: aprobar ? 'Sí, aprobar' : 'Sí, rechazar', cancelButtonText: 'Cancelar',
            buttonsStyling: false,
            customClass: {confirmButton: 'btn ' + (aprobar ? 'btn-success' : 'btn-danger') + ' me-2', cancelButton: 'btn btn-white'},
            showLoaderOnConfirm: true, allowOutsideClick: () => !Swal.isLoading(), allowEscapeKey: () => !Swal.isLoading(),
            preConfirm: async () => {
                try { await component.call(button.dataset.paymentAction, Number(button.dataset.letra)); }
                catch(error) { Swal.showValidationMessage('No se pudo completar la operación. Revisa los datos de la solicitud.'); return false; }
            }
        });
    });
    window.addEventListener('close-modal-pagos', function() { $('#modalPagos').modal('hide'); });
    window.addEventListener('alerta-pago-credito', function(event) {
        Swal.fire({title: event.detail.titulo, text: event.detail.mensaje, icon: event.detail.tipo,
            buttonsStyling: false, customClass: {confirmButton: 'btn btn-primary'}});
    });
</script>
@stop
