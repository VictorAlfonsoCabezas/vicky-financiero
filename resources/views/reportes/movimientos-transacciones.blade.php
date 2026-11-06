@extends('layouts.app')
@section('title', 'Movimientos por transacción')
@section('breadcrumbs1', 'Movimientos por tipo de transacción')
@section('breadcrumbs2', 'Reporte de movimientos')
@section('content')
<livewire:reportes.transaction-movement-component />
@endsection