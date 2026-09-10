@extends('layouts.app')
@section('title', 'Clientes')
@section('sidebar_state', 'app-sidebar-minified')
@section('custom_css_rules')
<link href="{{ asset('css/savings.css') }}" rel="stylesheet">
<link href="{{ asset('css/customers.css') }}" rel="stylesheet">
@stop
@section('content')
<livewire:clientes.clientes-component :customer_id="$id" />
@endsection
@section('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
@stop