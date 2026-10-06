@extends('layouts.app')
@section('title', 'Estado de resultados')
@section('custom_css_rules')
<style>
    .resultados-module .panel { border-radius: 6px; overflow: hidden; }
    .resultados-module .panel-heading { padding: 14px 18px; }
    .resultados-module .form-label { font-weight: 600; }
    .resultados-summary { border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,.04); }
    .resultados-summary h3 { font-size: clamp(20px, 2vw, 28px); }
    .resultados-icon { display: inline-flex; align-items: center; justify-content: center; width: 46px; height: 46px; border-radius: 10px; font-size: 20px; flex-shrink: 0; }
    .resultados-money { font-variant-numeric: tabular-nums; white-space: nowrap; }
    .resultados-table { font-size: 13px; }
    .resultados-table th { background: #f0f3f5; color: #495057; padding: 14px 18px; white-space: nowrap; }
    .resultados-table td { padding: 12px 18px; }
    .resultados-cuenta { padding-left: calc(var(--cuenta-nivel) * 14px); min-width: 230px; }
    .resultados-table .resultados-nivel-1 { --bs-table-bg: #cce3f8; --bs-table-color: #15395a; --bs-table-hover-bg: #bddbf5; --bs-table-hover-color: #15395a; font-weight: 700; }
    .resultados-table .resultados-nivel-2 { --bs-table-bg: #dadddf; --bs-table-color: #2b2f32; --bs-table-hover-bg: #cfd3d6; --bs-table-hover-color: #2b2f32; font-weight: 600; }
    .resultados-table .resultados-nivel-3 { --bs-table-bg: #fde6c6; --bs-table-color: #623e0a; --bs-table-hover-bg: #fcddb2; --bs-table-hover-color: #623e0a; font-weight: 600; }
    .resultados-table tr[class*="resultados-nivel-"] > td {
        background-color: var(--bs-table-bg);
        color: var(--bs-table-color);
    }
    .resultados-table tr[class*="resultados-nivel-"]:hover > td {
        background-color: var(--bs-table-hover-bg);
        color: var(--bs-table-hover-color);
    }
    .resultados-legend { font-size: 12px; }
    .resultados-module .pagination { margin-bottom: 0; }
    @media (max-width: 767px) {
        .resultados-module .page-header { font-size: 24px; }
        .resultados-table th, .resultados-table td { padding: 10px 12px; }
    }
</style>
@stop
@section('content')
    <livewire:reportes-resultados.reportes-resultados-component />
@endsection
