@extends('layouts.app')
@section('title', 'Solicitud Encaje')
@section('custom_css_rules')@stop
@section('content')
<livewire:solicitud-encaje.solicitud-encaje-component />
@endsection
@section('scripts')
<script>
    //evento escucha cerrar modal
    window.addEventListener('closeModal', event => {
        $('#modalGeneral').modal('hide');
    });
</script>
@stop