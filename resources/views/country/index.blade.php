@extends('layouts.app')
@section('title', 'Países')
@section('custom_css_rules')@stop
@section('content')
<livewire:country.country-component />
@endsection
@section('scripts')
<script>
    //evento escucha cerrar modal
    window.addEventListener('closeModal', event => {
        $('#modalGeneral').modal('hide');
    });
</script>
@stop