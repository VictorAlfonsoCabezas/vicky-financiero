@extends('layouts.app')
@section('title', 'Clientes')
@section('custom_css_rules')@stop
@section('content')
<livewire:clientes.clientes-component :customer_id="$id" />
@endsection
@section('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
@stop