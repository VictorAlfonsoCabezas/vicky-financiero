@extends('layouts.app')
@section('title', 'Tipos de transacción')
@section('custom_css_rules')
<style>
    .transactions-module .panel { border-radius: 6px; overflow: hidden; }
    .transactions-module .panel-heading { padding: 14px 18px; }
    .transactions-module .form-label { font-weight: 600; }
    .transactions-table { font-size: 13px; }
    .transactions-table th { padding: 14px; background: #f0f3f5; color: #495057; white-space: nowrap; }
    .transactions-table td { padding: 12px 14px; }
    .transactions-table .transactions-description { min-width: 200px; max-width: 360px; overflow-wrap: anywhere; }
    .transactions-table .btn { white-space: nowrap; }
    .transactions-module .pagination { margin-bottom: 0; }
    .transactions-module .modal-content { border-radius: 6px; overflow: hidden; }
    .transactions-module .modal-header, .transactions-module .modal-body { padding: 20px 24px; }
    .transactions-module .modal-title { font-size: 16px; font-weight: 600; }
    .transactions-module .modal-footer { padding: 16px 24px; background: #f8f9fa; flex-shrink: 0; }
    .transactions-module .modal-content > form { display: flex; flex-direction: column; min-height: 0; overflow: hidden; flex: 1 1 auto; }
    .transactions-module .modal-content > form > .modal-body { overflow-y: auto; min-height: 0; }
    @media (max-width: 767px) { .transactions-module .page-header { font-size: 24px; } }
</style>
@stop
@section('content')
    <livewire:type-transactions.type-transactions-component />
@endsection
@section('scripts')
    <script>
        //evento escucha cerrar modal
        window.addEventListener('closeModal', event => {
            $('#modalGeneral').modal('hide');
        });
    </script>
@stop