@extends('layouts.app-client')
@section('custom_css_rules')@stop
@section('content')
    <livewire:customer-movimiento-solicitud.customer-movimiento-solicitud-component />
@endsection
@section('scripts')
    <script>
        //evento escucha cerrar modal
        window.addEventListener('closeModal', event => {
            $('#modalGeneral').modal('hide');
        });
    </script>
@stop
