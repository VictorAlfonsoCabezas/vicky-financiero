@extends('layouts.app')
@section('title', 'Descargos de bóvedas')
@section('custom_css_rules')
    <link rel="stylesheet" href="{{ asset('css/descargo-bovedas.css') }}?v={{ filemtime(public_path('css/descargo-bovedas.css')) }}">
@endsection
@section('content')
    <livewire:descargo-bovedas-header.descargo-bovedas-header-component />
@endsection
@section('scripts')
    <script src="{{ asset('js/descargo-bovedas.js') }}?v={{ filemtime(public_path('js/descargo-bovedas.js')) }}"></script>
@endsection