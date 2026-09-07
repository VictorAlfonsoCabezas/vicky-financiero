@extends('layouts.app')
@section('custom_css_rules')@stop
@section('content')
    <livewire:descargo-bovedas-header.descargo-bovedas-header-component />
@endsection
@section('scripts')
    <script>
        //evento escucha cerrar modal
        window.addEventListener('closeModal', event => {
            $('#modalGeneral').modal('hide');
        });
        window.addEventListener('closeModal', event => {
            $('#modalGeneral1').modal('hide');
        });
        window.addEventListener('closeModal', event => {
            $('#modalGeneral2').modal('hide');
        });
    </script>
@stop
