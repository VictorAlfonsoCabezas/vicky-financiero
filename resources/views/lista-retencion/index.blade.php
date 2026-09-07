@extends('layouts.app')
@section('title', 'Lista Retenciones')
@section('custom_css_rules')@stop
@section('content')
<livewire:lista-retencion.lista-retencion-component />
@endsection
@section('scripts')
<script>
    //evento escucha cerrar modal
    window.addEventListener('closeModal', event => {
        $('#modalGeneral').modal('hide');
    });
</script>
@stop