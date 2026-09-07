@extends('layouts.app')
@section('title', 'Ciudades')
@section('custom_css_rules')@stop
@section('content')
<livewire:ciudad.ciudad-component />
@endsection
@section('scripts')
<script>
    //evento escucha cerrar modal
    window.addEventListener('closeModal', event => {
        $('#modalGeneral').modal('hide');
    });
</script>
@stop