@extends('layouts.app')
@section('title', 'Editar Usuarios')
@section('custom_css_rules')@stop
@section('content')
<livewire:user-new.user-new-edit-component :id="$id" />
@endsection
@section('scripts')
<script>
    //evento escucha cerrar modal
    window.addEventListener('closeModal', event => {
        $('#modalGeneral').modal('hide');
    });
</script>
@stop