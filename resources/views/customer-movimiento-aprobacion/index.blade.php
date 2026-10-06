@extends('layouts.app')
@section('title', 'Aprobación de movimientos')
@section('custom_css_rules')
<style>
    .approval-module .panel { border-radius: 6px; overflow: hidden; }
    .approval-module .panel-heading { padding: 14px 18px; }
    .approval-module .form-label { font-weight: 600; }
    .approval-table { font-size: 13px; }
    .approval-table th { padding: 14px; background: #f0f3f5; color: #495057; white-space: nowrap; }
    .approval-table td { padding: 12px 14px; }
    .approval-observation { min-width: 180px; max-width: 300px; overflow-wrap: anywhere; }
    .approval-actions { min-width: 190px; }
    .approval-table td:first-child { font-variant-numeric: tabular-nums; }
    .approval-module .pagination { margin-bottom: 0; }
    .approval-module .modal-content { border-radius: 6px; overflow: hidden; }
    .approval-module .modal-header, .approval-module .modal-body { padding: 20px 24px; }
    .approval-module .modal-title { font-size: 16px; font-weight: 600; }
    .approval-module .modal-footer { padding: 16px 24px; background: #f8f9fa; flex-shrink: 0; }
    .approval-module .modal-content > form { display: flex; flex-direction: column; flex: 1 1 auto; min-height: 0; overflow: hidden; }
    .approval-module .modal-content > form > .modal-body { overflow-y: auto; min-height: 0; }
    .approval-module dd { margin-bottom: 0; overflow-wrap: anywhere; }
    .approval-amount { padding: 16px; border-radius: 6px; background: #cce3f8; color: #15395a; font-variant-numeric: tabular-nums; }
    @media (max-width: 767px) { .approval-module .page-header { font-size: 24px; } }
</style>
@stop
@section('content')
    <livewire:customer-movimiento-aprobacion.customer-movimiento-aprobacion-component />
@endsection
@section('scripts')
    <script>
        document.addEventListener('click', function (event) {
            const button = event.target.closest('[data-confirmar-aprobacion]');
            if (!button || button.disabled || !button.closest('.approval-module')) return;

            const component = Livewire.find(button.closest('[wire\\:id]')?.getAttribute('wire:id'));
            if (!component || Swal.isVisible()) return;

            Swal.fire({
                title: '¿Aprobar esta solicitud?',
                text: 'Cliente: ' + button.dataset.cliente + '. Importe: $ ' + button.dataset.valor + '. Se registrará el movimiento al confirmar.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, aprobar',
                cancelButtonText: 'Cancelar',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn btn-success me-2',
                    cancelButton: 'btn btn-white'
                },
                focusCancel: true,
                showLoaderOnConfirm: true,
                allowOutsideClick: () => !Swal.isLoading(),
                allowEscapeKey: () => !Swal.isLoading(),
                preConfirm: async () => {
                    try {
                        await component.call('aceptar', Number(button.dataset.confirmarAprobacion));
                    } catch (error) {
                        Swal.showValidationMessage('No se pudo completar la aprobación. Revisa la solicitud antes de intentarlo nuevamente.');
                        return false;
                    }
                }
            });
        });
        //evento escucha cerrar modal
        window.addEventListener('closeModal', event => {
            $('#modalGeneral').modal('hide');
        });
    </script>
@stop
