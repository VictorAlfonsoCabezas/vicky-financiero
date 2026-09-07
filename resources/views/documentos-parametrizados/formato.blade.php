@extends('layouts.app')
@section('title', 'Cuentas')
@section('custom_css_rules')@stop
@section('content')
<livewire:documentos-parametrizables.documentos-formato-component :formato="$id" :documento="$doc" >

@endsection
@section('scripts')
@stop