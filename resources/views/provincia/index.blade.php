@extends('layouts.app')
@section('title', 'Provincias')
@section('custom_css_rules')@stop
@section('content')
<livewire:provincia.provincia-component />
@endsection
@section('scripts')
<script>
    //evento escucha cerrar modal
    window.addEventListener('closeModal', event => {
        $('#modalGeneral').modal('hide');
    });
</script>
@stop