@extends('layouts.app')
@section('title', 'Usuarios')
@section('custom_css_rules')@stop
@section('content')
<livewire:user-new.user-new-component />
@endsection
@section('scripts')
<script>
    //evento escucha cerrar modal
    window.addEventListener('closeModal', event => {
        $('#modalGeneral').modal('hide');
    });
</script>
@stop