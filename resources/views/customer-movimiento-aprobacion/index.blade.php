@extends('layouts.app')
@section('custom_css_rules')@stop
@section('content')
    <livewire:customer-movimiento-aprobacion.customer-movimiento-aprobacion-component />
@endsection
@section('scripts')
    <script>
        //evento escucha cerrar modal
        window.addEventListener('closeModal', event => {
            $('#modalGeneral').modal('hide');
        });
    </script>
@stop
