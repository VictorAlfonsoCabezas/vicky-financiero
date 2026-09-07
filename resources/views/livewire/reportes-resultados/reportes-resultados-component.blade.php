<div>
    <div class="row mt-2">
        <div class="col-md-12">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">Filtros</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-3">
                            <label>Fecha Inicio</label>
                            <input type="date" class="form-control form-control-sm" placeholder="Fecha Inicio"
                                wire:model="fechaInicio">
                        </div>
                        <div class="col-3">
                            <label>Fecha Fin</label>
                            <input type="date" class="form-control form-control-sm" placeholder="Fecha Fin"
                                wire:model="fechaFin">
                        </div>
                        <div class="col-3">
                            <label>Saldo</label>
                            <select class="form-control form-control-sm" wire:model="filtroSaldo">
                                <option value="todos">Todos</option>
                                <option value="diferente-de-cero">Diferente de Cero</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-md-12">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">Acciones</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-3">
                            <a wire:click="generarPdf" class="btn btn-danger btn-sm" style="color: white;"><i class="far fa-file-pdf"></i></a>
                      
                            <a class="btn btn-warning btn-sm" wire:click="cierreFom"  title="Cierre">
                                <i class="far fa-file-alt"></i>
                            </a>
                        </div>

                    </div>
                </div>
            </div>

        </div>



        <div class="col-md-12">
            <div class="row">
                <div class="col-md-4">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h4>${{ number_format($totalIngresos, 2, '.', ',') }}</h4>
                            <p>Ingresos</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h4>${{ number_format($totalGastos, 2, '.', ',') }}</h4>
                            <p>Gastos</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h4>${{ number_format($resultadoPeriodo, 2, '.', ',') }}</h4>
                            <p>Resultado del periodo</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Estado de resultados</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Cuenta</th>
                                <th>Saldo Acumulado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($planCuentas as $plan)
                            <tr
                                @if ($plan->nivel == 1) class="bg-primary text-light"
                                @elseif ($plan->nivel == 2)
                                class="bg-secondary text-light"
                                @elseif ($plan->nivel == 3)
                                class="bg-warning text-light" @endif>
                                <td><b>{{ $plan->codigo }}</b></td>
                                <td>
                                    @if ($plan->nivel > 3)
                                    <b>{{ $plan->nombre }}</b>
                                    @else
                                    {{ $plan->nombre }}
                                    @endif
                                </td>
                                <td>${{ $plan->saldo }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td class="text-center" colspan="6">No hay</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $planCuentas->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
