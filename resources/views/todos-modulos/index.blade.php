@extends('layouts.app')
@section('title','Movimientos y asientos')
@section('breadcrumbs1','Movimientos y asientos')
@section('breadcrumbs2','Todos los módulos')
@section('custom_css_rules')
<style>
    .modulos-indicador { min-height: 108px; color: #fff; cursor: pointer; transition: box-shadow .15s; }
    .modulos-indicador:hover, .modulos-indicador:focus-visible { box-shadow: 0 0 0 3px rgba(52, 143, 226, .25); }
    .modulos-indicador-activo { outline: 3px solid var(--bs-body-color, #212529); outline-offset: 2px; }
    .modulos-tabla { min-width: 1000px; }
    .modulos-tabla th { font-size: .75rem; text-transform: uppercase; letter-spacing: .025em; }
    .modulos-tabla td, .modulos-tabla th { padding: .9rem 1rem; }
    .modulos-tabla .modulos-pendiente td:first-child { border-left: 3px solid var(--bs-danger, #dc3545); }
    .modulos-tabla .modulos-pendiente { background-color: rgba(220, 53, 69, .045); }
    @media (max-width: 575px) { .modulos-indicador .stats-icon { font-size: 26px; right: 12px; top: 15px; } }
</style>
@stop
@section('content')
<livewire:todos-modulos.todos-modulos-component />
@endsection
@section('scripts')
<script>
    //evento escucha cerrar modal
    window.addEventListener('closeModal', event => {
        ['modalGeneral', 'modalManualAsiento'].forEach(function (id) {
            var modal = document.getElementById(id);
            if (modal && window.bootstrap) {
                var instance = bootstrap.Modal.getInstance(modal);
                if (instance) instance.hide();
            }
        });
    });
</script>
@stop
