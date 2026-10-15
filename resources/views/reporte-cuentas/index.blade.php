@extends('layouts.app')
@section('title', 'Reporte de cuentas')
@section('breadcrumbs1', 'Reportes')
@section('breadcrumbs2', 'Cuentas de ahorro')
@section('custom_css_rules')
<style>
    .reporte-cuentas-tabla { min-width: 950px; }
    .reporte-cuentas-tabla td, .reporte-cuentas-tabla th { padding: .9rem 1rem; }
    .reporte-cuentas-tabla th { font-size: .75rem; text-transform: uppercase; letter-spacing: .025em; }
    .reporte-cuentas .form-label { font-weight: 600; }
    .reporte-cuentas .modal-body { padding: 1.5rem; }
    .reporte-cuentas dd { overflow-wrap: anywhere; }
</style>
@stop
@section('content')
    <livewire:reporte-cuentas.reporte-cuentas-component />
@endsection
@section('scripts')
<script>
    window.addEventListener('close-modal', function () {
        var element = document.getElementById('modalCuenta');
        var instance = element && bootstrap.Modal.getInstance(element);
        if (instance) instance.hide();
    });
</script>
@stop
