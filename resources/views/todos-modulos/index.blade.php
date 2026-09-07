@extends('layouts.app')
@section('title','Todos los Modulos')
@section('custom_css_rules')@stop
@section('content')
<livewire:todos-modulos.todos-modulos-component />
@endsection
@section('scripts')
<script>
    //evento escucha cerrar modal
    window.addEventListener('closeModal', event => {
        $('#modalGeneral').modal('hide');
        $('#modalManualAsiento').modal('hide');
    });
</script>
@stop
