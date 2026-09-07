@extends('layouts.app')
@section('title', 'Géneros')
@section('custom_css_rules')@stop
@section('content')
<livewire:genero.genero-component />
@endsection
@section('scripts')
<script>
    //evento escucha cerrar modal
    window.addEventListener('closeModal', event => {
        $('#modalGeneral').modal('hide');
    });
</script>
@stop