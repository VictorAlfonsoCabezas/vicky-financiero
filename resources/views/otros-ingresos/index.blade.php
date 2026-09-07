@extends('layouts.app')
@section('custom_css_rules')@stop
@section('content')
<livewire:otros-ingresos.otros-ingresos-component />
@endsection
@section('scripts')
<script>
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
        }
    });
    //evento escucha cerrar modal
    window.addEventListener('closeModal', event => {
        $('#modalGeneral').modal('hide');
    });

    window.addEventListener('closeModal2', event => {
        $('#modalGeneral2').modal('hide');
    });
</script>
@stop