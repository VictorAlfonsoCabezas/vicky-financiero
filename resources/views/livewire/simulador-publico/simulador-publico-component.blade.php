<!DOCTYPE html>
<html lang="en" class="no-js">

<head>
<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Onix</title>
    <link rel="icon" href="{{ asset('codev/negro.png') }}" type="image/png" />
    <!-- CSS GLOBALES -->
    @include('layouts.css_stylesheet')
    
    @livewireStyles
</head>

<body class="is-boxed has-animations">
    <div class="body-wrap">
        <header class="site-header">
            <div class="container">
                <div class="site-header-inner">
                    <div class="brand header-brand">
                        <h1 class="m-0">
                            <a href="{{ URL::to('/') }}">
                                <img class="header-logo-image" src="{{ URL::to('codev/blanco.png') }}" alt="Logo"
                                    style="width: 125px">
                            </a>
                        </h1>
                    </div>
                </div>
            </div>
        </header>

        <main>
            <section class="hero">
                <div class="container">
                    <div class="hero-inner">
                        <div class="hero-copy">
                            <h1 class="hero-title mt-0">
                                <img class="header-logo-image" src="{{ URL::to('codev/codevBlanco.png') }}"
                                    alt="Logo" style="width: 190px">
                            </h1>

                            <div class="hero-cta">
                                <a class="button button-primary" href="{{ URL::to('login') }}">Iniciar
                                    Sesion</a>
                                <a class="button" style="background-color: #ffbc00 !important;"
                                    href="{{ URL::to('login2') }}">Acceso Clientes</a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="features section">

                <div class="container">
                    <div class="features-inner section-inner has-bottom-divider">
                        <h5>SIMULADOR</h5>
                        <section class="features section">
                            <div class="container">
                                <div class="features-inner section-inner has-bottom-divider">
                                    <div class="col-12">
                                        <div class="features-wrap">
                                            <div>
                                                <label class="small">Valor</label>
                                                <div class="input-group input-group-sm">
                                                    <input id="valor_simulador" type="number" wire:model="valor_simulador" class="form-control" placeholder="Valor" >
                                                </div>
                                            </div>
                                            <div>
                                                <label class="small">Cuotas</label>
                                                <div class="input-group input-group-sm">
                                                    <input id="cuotas_simulador" type="number" wire:model="cuotas_simulador" class="form-control" placeholder="Número de Cuotas">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="small">Fecha Prestamo</label>
                                                <div class="input-group input-group-sm">
                                                    <input id="fecha_prestamo" type="date" wire:model="fecha_prestamo" class="form-control" placeholder="Fecha Prestamo">
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <br>
                                        <div class="row mt-3">
                                            <div class="features-wrap">
                                                <div>
                                                    <label class="small">Tipo </label>
                                                    <select id="tipo_simulador" wire:model="tipo_simulador" class="form-control form-control-sm" wire:change="obtenerDatos" wire:key="tipo_simulador">
                                                        <option value=""> --SELECCIONE--</option>
                                                        <option value="F">FRANCESA (CUOTA FIJA)</option>
                                                        <option value="A">ALEMANA (CAPITAL FIJO)</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="small">Prestamo </label>
                                                    <select id="prestamo_simulador" wire:model="prestamo_simulador" class="form-control form-control-sm" wire:change="obtenerDatosCredito" wire:key="prestamo_simulador">
                                                        <option value=""> --SELECCIONE--</option>
                                                        @foreach ($opcionesPrestamo as $opcion)
                                                        <option value="{{ $opcion->id }}">{{ $opcion->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <br>
                                        <div>
                                            <button type="button" wire:click="simular" class="btn btn-block btn-primary btn-xs" wire:loading.attr="disabled">Generar</button>
                                        </div>
                                        <hr>
                                        <div class="row  mt-3">
                                            <table class="table table-striped table-valign-middle">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Fecha Vencimiento</th>
                                                        <th>Interés del período</th>
                                                        @if ($this->diarioLetras == false)
                                                        <th>Capital Amortizado</th>
                                                        @endif
                                                        @if($this->tipo_simulador != "A")
                                                        <th>Fondo de Desgravamen</th>
                                                        @endif
                                                        <th>Cuota a pagar</th>
                                                        @if ($this->diarioLetras == false)
                                                        <th>Saldo remanente </th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($this->listaLetras as $letras)
                                                    <tr>
                                                        <td class="small">
                                                            {{ $letras['cuotas'] }}
                                                        </td>
                                                        <td class="small">
                                                            {{ $letras['fechas'] }}
                                                        </td>
                                                        <td class="small">
                                                            {{ $letras['interes'] }}
                                                        </td>
                                                        @if ($this->diarioLetras == false)
                                                        <td class="small">
                                                            {{ $letras['amoritizado'] }}
                                                        </td>
                                                        @endif
                                                        @if($this->tipo_simulador != "A")
                                                        <td class="small">
                                                            {{ $letras['desgravamen'] }}
                                                        </td>
                                                        @endif
                                                        <td class="small">
                                                            {{ $letras['cuotaPago'] }}
                                                        </td>
                                                        @if ($this->diarioLetras == false)
                                                        <td class="small">
                                                            {{ $letras['deuda'] }}
                                                        </td>
                                                        @endif
                                                    </tr>
                                                    @endforeach

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                        </section>





                    </div>
                </div>
    </div>
    </section>







    </main>

    </div>
   
</body>
<!-- JS GLOBALES -->
@include('layouts.js_libs')
@livewireScripts
</html>