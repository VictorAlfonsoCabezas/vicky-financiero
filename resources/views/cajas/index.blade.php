@extends('layouts.app')
@section('title', 'Cajas')
@section('breadcrumbs1', 'Cajas')
@section('breadcrumbs2', 'Control de cajas')
@section('custom_css_rules')
<style>
    .cajas-indicador { min-height: 115px; }
    .cajas-tabla { min-width: 1100px; }
    .cajas-tabla td, .cajas-tabla th { padding: .9rem 1rem; }
    .cajas-tabla th { font-size: .75rem; text-transform: uppercase; letter-spacing: .025em; }
    .cajas-tabla .cajas-diferencia { background: rgba(220,53,69,.045); }
    .cajas-tabla .cajas-diferencia td:first-child { border-left: 3px solid var(--bs-danger, #dc3545); }
    .cajas-modulo .modal-dialog-scrollable .modal-content > form { display: flex; flex-direction: column; flex: 1; min-height: 0; }
    .cajas-modulo .modal-body .card { border: 1px solid var(--bs-border-color, #dee2e6); box-shadow: none; }
    .cajas-modulo .modal-body table { font-size: .85rem; }
    .cajas-modulo #modalGeneral2 .card-body { overflow-x: auto; }
    @media (max-width: 575px) { .cajas-indicador .stats-icon { font-size: 26px; right: 12px; } }
</style>
@stop
@section('content')
<livewire:cajas.cajas-component />
@endsection
@section('scripts')
<script>
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Enter' && event.target.closest('#modalGeneral2') && event.target.tagName === 'INPUT') {
            event.preventDefault();
        }
    });
    //evento escucha cerrar modal
    window.addEventListener('closeModal', event => {
        ['modalGeneral', 'modalGeneral3', 'modalGeneral4', 'modalGeneral5'].forEach(cerrarModalCaja);
    });

    window.addEventListener('closeModal2', event => {
        cerrarModalCaja('modalGeneral2');
    });
    function cerrarModalCaja(id) {
        var element = document.getElementById(id);
        if (element && window.bootstrap) {
            var instance = bootstrap.Modal.getInstance(element);
            if (instance) instance.hide();
        }
    }
</script>
@stop
