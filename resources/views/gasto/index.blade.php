@extends('layouts.app')
@section('title', 'Gastos')
@section('custom_css_rules')
<style>
    .gasto-module .panel { border-radius: 8px; overflow: hidden; }
    .gasto-module .panel-heading { padding: 14px 18px; }
    .gasto-module .panel-body { padding: 20px; }
    .gasto-module .gasto-table-wrap { padding: 0; }
    .gasto-table { font-size: 12px; }
    .gasto-table th { white-space: nowrap; padding: 14px 12px; background: #f0f3f5; color: #495057; }
    .gasto-table td { padding: 12px; }
    .gasto-table .gasto-money { white-space: nowrap; font-variant-numeric: tabular-nums; }
    .gasto-table td:last-child { min-width: 190px; }
    .gasto-table td:last-child .btn { margin: 2px; min-width: 30px; min-height: 30px; display: inline-flex; align-items: center; justify-content: center; }
    .gasto-module .modal-header { background: #f0f3f5; }
    .gasto-module .modal-title { font-size: 18px; }
    .gasto-module .modal-footer { gap: 8px; }
    @media (max-width: 767px) {
        .gasto-module .page-header { font-size: 24px; }
        .gasto-module .panel-body { padding: 14px; }
        .gasto-module .gasto-table-wrap { padding: 0; }
    }
    .gasto-module .modal-content { border: 0; border-radius: 10px; overflow: hidden; }
    .gasto-module .modal-header { padding: 18px 24px; border-bottom: 1px solid #dee2e6; }
    .gasto-module .modal-title > i { color: var(--bs-primary, #348fe2); margin-right: 8px; }
    .gasto-module .modal-body { padding: 24px; }
    .gasto-module .modal-footer { padding: 16px 24px; background: #f8f9fa; }
    .gasto-module .modal-body label { font-weight: 600; margin-bottom: 6px; }
    .gasto-module .modal-body .input-group-text { align-self: stretch; }
    .gasto-module .modal-body .form-control[readonly] { background: #f0f3f5; }
    .gasto-module .modal-body .form-control-lg { font-variant-numeric: tabular-nums; }
    .gasto-module .modal-body .card { border: 1px solid #dee2e6; border-radius: 8px; overflow: hidden; }
    .gasto-module .modal-body .card-header { padding: 14px 16px; background: #f0f3f5; }
    .gasto-modal-intro { padding: 12px 16px; margin-bottom: 20px; border-left: 3px solid var(--bs-primary, #348fe2); background: #f0f3f5; color: #495057; border-radius: 4px; }
    .gasto-modal-table th { background: #f0f3f5; white-space: nowrap; font-size: 12px; padding: 12px; }
    .gasto-modal-table td { padding: 10px 12px; }
    .gasto-modal-table .input-group { min-width: 150px; }
    .gasto-module .modal-body .input-group:has(> .form-control-lg) { flex-wrap: wrap; row-gap: 12px; }
    @media (max-width: 767px) {
        .gasto-module .modal-header, .gasto-module .modal-body, .gasto-module .modal-footer { padding: 16px; }
        .gasto-module .modal-footer > .btn { flex: 1 1 auto; }
    }
</style>
@stop
@section('content')
<livewire:gasto.gasto-component />
@endsection
@section('scripts')
<script>
    //evento escucha cerrar modal
    window.addEventListener('closeModal', event => {
        $('#modalGeneral').modal('hide');
        $('#modalGeneral2').modal('hide');
    });
</script>
@stop