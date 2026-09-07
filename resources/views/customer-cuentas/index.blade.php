@extends('layouts.app-client')
@section('custom_css_rules')@stop
@section('content')
    <livewire:customer-cuentas.customer-cuentas-component />
@endsection
@section('scripts')
    <script>
        //evento escucha cerrar modal
        window.addEventListener('closeModal', event => {
            $('#modalGeneral').modal('hide');
        });
    </script>
@stop
