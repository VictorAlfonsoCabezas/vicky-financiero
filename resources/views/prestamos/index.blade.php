@extends('layouts.app')
@section('custom_css_rules')@stop
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