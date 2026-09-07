@extends('layouts.app')
@section('title', 'Cuentas')
@section('custom_css_rules')@stop
@section('content')
<livewire:cuentas.cuentas-component :customer_id="$id" />
@endsection
@section('scripts')
@stop