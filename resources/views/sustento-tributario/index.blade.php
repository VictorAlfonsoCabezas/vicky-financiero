@extends('layouts.app')
@section('title', 'Sustento Tributario')
@section('custom_css_rules')@stop
@section('content')
<livewire:sustento-tributario.sustento-tributario-component />
@endsection
@section('scripts')
<script>
    //evento escucha cerrar modal
    window.addEventListener('closeModal', event => {
        $('#modalGeneral').modal('hide');
    });
</script>
@stop