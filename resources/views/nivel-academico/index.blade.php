@extends('layouts.app')
@section('title', 'Niveles Académicos')
@section('custom_css_rules')@stop
@section('content')
<livewire:nivel-academico.nivel-academico-component />
@endsection
@section('scripts')
<script>
    //evento escucha cerrar modal
    window.addEventListener('closeModal', event => {
        $('#modalGeneral').modal('hide');
    });
</script>
@stop