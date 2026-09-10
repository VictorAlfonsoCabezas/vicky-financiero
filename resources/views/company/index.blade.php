@extends('layouts.app')
@section('title', 'Empresas')
@section('breadcrumbs1', 'Empresas')
@section('breadcrumbs2', 'Administración de empresas')
@section('custom_css')<link rel="stylesheet" href="{{ asset('css/company.css') }}">@endsection
@section('content')
@livewire('company.company-index')
@endsection
@section('scripts')<script src="{{ asset('js/company.js') }}" defer></script>@endsection
