@extends('layouts.app')
@section('title', 'Tipos de ahorro')
@section('custom_css_rules')
<link href="{{ asset('css/savings.css') }}" rel="stylesheet">
@stop
@section('content')
    <livewire:tipo-ahorros.tipo-ahorros-component />
@endsection
@section('scripts')
    <script>
        //evento escucha cerrar modal
        window.addEventListener('closeModal', event => {
            $('#modalGeneral').modal('hide');
        });
    </script>
@stop
