@extends('layouts.app')
@section('title', 'Estados Civiles')
@section('custom_css_rules')@stop
@section('content')
<livewire:documentos-parametrizables.documentos-parametrizables-component>
    @endsection
    @section('scripts')
    <script>
        //evento escucha cerrar modal
        window.addEventListener('closeModal', event => {
            $('#modalGeneral').modal('hide');
        });
    </script>
    @stop