@extends('layouts.app')
@section('title', 'Mi empresa')
@section('custom_css')
<link rel="stylesheet" href="{{ asset('css/mi-empresa.css') }}">@endsection
@section('content')
@livewire('mi-empresa.mi-empresa-editor')
@endsection
@section('scripts')
<script src="{{ asset('js/mi-empresa.js') }}" defer>
    
</script>@endsection