@extends('layouts.app')
@section('title','Configuración de asientos')
@section('custom_css_rules')
<style>
    .configuracion-cuenta-page .cc-stat-icon { width: 46px; height: 46px; display: grid; place-items: center; border-radius: 12px; font-size: 20px; flex-shrink: 0; }
    .configuracion-cuenta-page .cc-cuentas { max-height: 260px; overflow: auto; }
    .configuracion-cuenta-page .cc-cuentas li:last-child { border-bottom: 0 !important; }
    .configuracion-cuenta-page .cc-search-results { max-height: 230px; overflow: auto; }
    .configuracion-cuenta-page .cc-param-table { min-width: 850px; }
    .configuracion-cuenta-page .pagination { flex-wrap: wrap; }
    .configuracion-cuenta-page .cc-editor-form { display: flex; flex-direction: column; overflow: hidden; }
    @media (min-width: 576px) { .configuracion-cuenta-page .cc-debe { border-right: 1px solid var(--bs-border-color, #dee2e6); } }
    @media (max-width: 575.98px) { .configuracion-cuenta-page .page-header { font-size: 24px; } }
</style>
@stop
@section('content')
<livewire:configuracion-cuenta.configuracion-cuenta-component />
@endsection
@section('scripts')
<script>
    (() => {
        let origen;
        const permitidos = ['modalGeneral', 'modalGeneral2', 'ccEliminarModal'];
        window.addEventListener('cc-abrir-modal', event => {
            if (!permitidos.includes(event.detail.id)) return;
            origen = document.activeElement;
            bootstrap.Modal.getOrCreateInstance(document.getElementById(event.detail.id)).show();
        });
        window.addEventListener('cc-cerrar-modal', event => {
            if (permitidos.includes(event.detail.id)) bootstrap.Modal.getOrCreateInstance(document.getElementById(event.detail.id)).hide();
        });
        permitidos.forEach(id => document.getElementById(id).addEventListener('hidden.bs.modal', () => {
            if (origen && origen.isConnected) origen.focus();
        }));
    })();
</script>
@stop
