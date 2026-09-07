@extends('layouts.app')
@section('custom_css_rules')@stop
@section('content')
    <livewire:log-reverso-movimientos.log-reverso-movimientos-component />
@endsection
@section('scripts')
    <script>
        //evento escucha cerrar modal
        window.addEventListener('closeModal', event => {
            $('#modalGeneral').modal('hide');
        });
    </script>
@stop
