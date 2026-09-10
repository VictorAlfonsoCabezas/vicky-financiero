@extends('layouts.app')
@section('title', $company->exists ? 'Configurar empresa' : 'Nueva empresa')
@section('breadcrumbs1', $company->exists ? 'Configurar empresa' : 'Nueva empresa')
@section('breadcrumbs2', 'Empresas')
@section('custom_css')<link rel="stylesheet" href="{{ asset('css/company.css') }}">@endsection
@section('content')
@livewire('company.company-editor', ['companyId' => $company->id])
@endsection
@section('scripts')<script src="{{ asset('js/company.js') }}" defer></script>@endsection
