@extends('layouts.app')
@section('custom_css_rules')@stop
@section('content')
    <livewire:clasificacion-formas-pago.clasificacion-formas-pago-componet/>
@endsection
@section('scripts')
    <script>
        //evento escucha cerrar modal
        window.addEventListener('closeModal', event => {
            $('#modalGeneral').modal('hide');
        });
    </script>
@stop
