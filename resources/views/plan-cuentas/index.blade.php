@extends('layouts.app')
@section('title','Plan de Cuentas')
@section('custom_css_rules')
<style>
    .plan-cuentas-page .pc-stat-icon { width: 44px; height: 44px; flex-shrink: 0; display: grid; place-items: center; border-radius: 12px; font-size: 20px; }
    .plan-cuentas-page .pc-account-list { max-height: 680px; overflow: auto; }
    .plan-cuentas-page .pc-account-list thead th { position: sticky; top: 0; z-index: 1; }
    .plan-cuentas-page .pc-account-button { min-width: 140px; max-width: 340px; white-space: normal; }
    .plan-cuentas-page .pagination { margin-bottom: 0; flex-wrap: wrap; }
    @media (min-width: 1200px) { .plan-cuentas-page .pc-editor { position: sticky; top: 76px; } }
    @media (max-width: 575.98px) { .plan-cuentas-page .page-header { font-size: 24px; } .plan-cuentas-page .pc-account-list { max-height: 480px; } }
</style>
@stop
@section('content')
<livewire:plan-cuentas.plan-cuentas-component />
@endsection
@section('scripts')
<script>
    (() => {
        let origen;
        const modal = () => bootstrap.Modal.getOrCreateInstance(document.getElementById('pcEliminarModal'));
        window.addEventListener('pc-confirmar-eliminar', () => {
            origen = document.activeElement;
            modal().show();
        });
        window.addEventListener('pc-cerrar-eliminar', () => modal().hide());
        document.getElementById('pcEliminarModal').addEventListener('hidden.bs.modal', () => {
            if (origen && origen.isConnected) origen.focus();
        });
    })();
</script>
@stop
