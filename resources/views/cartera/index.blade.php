@extends('layouts.app')
@section('custom_css_rules')@stop
@section('content')
    <livewire:cartera.cartera-component />
@endsection
@section('scripts')
    <script>
        //evento escucha cerrar modal
        window.addEventListener('closeModal', event => {
            $('#modalGeneral').modal('hide');
        });

        window.addEventListener('closeModal2', event => {
            $('#modalGeneral2').modal('hide');
        });
    </script>
@stop
