@extends('layouts.app')
@section('title', 'Tipos de prestamo')
@section('custom_css_rules')
<link href="{{ asset('css/savings.css') }}" rel="stylesheet">
<link href="{{ asset('css/loans.css') }}" rel="stylesheet">
@stop
@section('content')
<livewire:prestamos.prestamos-component>
    @endsection
    @section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.addEventListener('closeModal', event => {
                $('#modalGeneral1').modal('hide');
            });
        });
    </script>
    @stop