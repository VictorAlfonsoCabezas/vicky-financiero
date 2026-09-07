@extends('layouts.app')
@section('title', 'Gastos')
@section('custom_css_rules')@stop
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