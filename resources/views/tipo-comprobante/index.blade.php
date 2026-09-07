@extends('layouts.app')
@section('title', 'Tipos de Comprobantes')
@section('custom_css_rules')@stop
@section('content')
<livewire:tipo-comprobante.tipo-comprobante-component />
@endsection
@section('scripts')
<script>
    //evento escucha cerrar modal
    window.addEventListener('closeModal', event => {
        $('#modalGeneral').modal('hide');
    });
</script>
@stop