@extends('layouts.app')
@section('title','Tipo de Retención')
@section('custom_css_rules')@stop
@section('content')
<livewire:tipo-retencion.tipo-retencion-component />
@endsection
@section('scripts')
<script>
    //evento escucha cerrar modal
    window.addEventListener('closeModal', event => {
        $('#modalGeneral').modal('hide');
    });
</script>
@stop