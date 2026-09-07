@extends('layouts.app')
@section('title', 'Impuestos')
@section('custom_css_rules')@stop
@section('content')
<livewire:impuestos.impuestos-component />
@endsection
@section('scripts')
<script>
    //evento escucha cerrar modal
    window.addEventListener('closeModal', event => {
        $('#modalGeneral').modal('hide');
    });
</script>
@stop