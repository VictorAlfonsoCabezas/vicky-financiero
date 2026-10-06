@extends('layouts.app')
@section('title', 'Balance general')
@section('custom_css_rules')
<style>
    .balance-module .panel { border-radius: 6px; overflow: hidden; }
    .balance-module .panel-heading { padding: 14px 18px; }
    .balance-module .form-label { font-weight: 600; }
    .balance-money { font-variant-numeric: tabular-nums; white-space: nowrap; }
    .balance-table { font-size: 13px; }
    .balance-table th { background: #f0f3f5; color: #495057; padding: 14px 18px; white-space: nowrap; }
    .balance-table td { padding: 12px 18px; }
    .balance-cuenta { padding-left: calc(var(--cuenta-nivel) * 14px); min-width: 230px; }
    .balance-table .balance-nivel-1 { --bs-table-bg: #cce3f8; --bs-table-color: #15395a; --bs-table-hover-bg: #bddbf5; --bs-table-hover-color: #15395a; font-weight: 700; }
    .balance-table .balance-nivel-2 { --bs-table-bg: #dadddf; --bs-table-color: #2b2f32; --bs-table-hover-bg: #cfd3d6; --bs-table-hover-color: #2b2f32; font-weight: 600; }
    .balance-table .balance-nivel-3 { --bs-table-bg: #fde6c6; --bs-table-color: #623e0a; --bs-table-hover-bg: #fcddb2; --bs-table-hover-color: #623e0a; font-weight: 600; }
    .balance-table tr[class*="balance-nivel-"] > td {
        background-color: var(--bs-table-bg);
        color: var(--bs-table-color);
    }
    .balance-table tr[class*="balance-nivel-"]:hover > td {
        background-color: var(--bs-table-hover-bg);
        color: var(--bs-table-hover-color);
    }
    .balance-legend { font-size: 12px; }
    .balance-module .pagination { margin-bottom: 0; }
    @media (max-width: 767px) {
        .balance-module .page-header { font-size: 24px; }
        .balance-table th, .balance-table td { padding: 10px 12px; }
    }
</style>
@stop
@section('content')
    <livewire:balance-general.balance-general-component />
@endsection
