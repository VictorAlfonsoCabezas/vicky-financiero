@extends('layouts.app')
@section('title', 'Cuentas')
@section('custom_css_rules')
<link href="{{ asset('css/savings.css') }}" rel="stylesheet">
@stop
@section('content')
<livewire:cuentas.cuentas-component :customer_id="$id" />
@endsection
@section('scripts')
@stop