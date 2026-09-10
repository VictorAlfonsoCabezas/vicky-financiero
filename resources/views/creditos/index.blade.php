@extends('layouts.app')
@section('title', 'Creditos')
@section('custom_css_rules')
<link href="{{ asset('css/savings.css') }}" rel="stylesheet">
<link href="{{ asset('css/loans.css') }}" rel="stylesheet">
@stop
@section('content')
    <livewire:creditos.creditos-componet :customer_id="$id"/>    
@endsection
@section('scripts')
    <script>
       
    </script>
@stop
