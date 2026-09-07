@extends('layouts.app-client')
@section('content')
<livewire:house.house-component />
@endsection
@section('scripts')
<script type="text/javascript">
    window.addEventListener('closeModal', event => {
        $('#modalGeneral').modal('hide');
    });

    window.addEventListener('closeModal2', event => {
        $('#modalGeneral2').modal('hide');
    });
</script>
@stop