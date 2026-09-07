@extends('layouts.app')

@section('content')

<div class="card">

    <h4 class="card-header">Cliente # {{$cliente->id}}</h4>
    <div class="card-body">
        <h5 class="card-title"><p>Nombres: {{$cliente->nombres}}</p></h5>
        <p class="card-text">Apellidos: {{$cliente->apellidos}}</p>
        
        <p class="card-text">Número de Documento: {{$cliente->numero_documento}}</p>
        <p class="card-text">Dirección: {{$cliente->direccion}}</p>
        <p class="card-text">Teléfono: {{$cliente->telefono}}</p>
        <p class="card-text">Correo: {{$cliente->correo}}</p>
        
         <a class="btn btn-warning" href="{{URL::to('customer')}}">Regresar</a>
    </div>
</div>


@endsection


