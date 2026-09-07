@extends('layouts.app')
@section('custom_css_rules')@stop
@section('content')
<livewire:valores-aportes.valores-aportes-component />
@endsection
@section('scripts')
<script>
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
        }
    });
    
</script>
@stop