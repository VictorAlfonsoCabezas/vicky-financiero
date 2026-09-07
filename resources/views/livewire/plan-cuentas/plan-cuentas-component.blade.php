<div>
    <div class="row mt-2">
        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Plan de Cuentas</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" wire:model="search" name="table_search" class="form-control float-end" placeholder="Buscar plan de cuenta">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <!-- <th style="width: 10px">#</th> -->
                                <th>Nombre</th>
                                <th>Préstamo</th>
                                <th>Gasto</th>
                                <th>Utilidad</th>
                                <th>Código</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cuentas as $cuen)
                            <tr wire:click="verPlan({{ $cuen['id'] }})">
                                <!-- <td>{{ $cuen->id }}</td> -->
                                <td>
                                    @if($cuen->creado_usuario)
                                    <b>{{ $cuen->nombre }}</b>*
                                    @else
                                    {{ $cuen->nombre }}
                                    @endif
                                </td>
                                <td>
                                    @if($cuen->prestamo)
                                    <i class="fa fa-check"></i>
                                    @endif
                                </td>
                                <td>
                                    @if($cuen->gasto)
                                    <i class="fa fa-check"></i>
                                    @endif
                                </td>
                                <td>
                                    @if($cuen->utilidad)
                                    <i class="fa fa-check"></i>
                                    @endif
                                </td>
                                <td><b>{{ $cuen->codigo }}</b></td>
                                <td>
                                    @if($cuen->creado_usuario)
                                    <a class="btn btn-default btn-xs" title="Eliminar plan Creado" wire:click.stop="eliminarPlan({{ $cuen->id }})"><i class="fas fa-trash"></i></a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $cuentas->links() }}
                </div>
                <div class="card-footer">
                    <!-- <button type="submit" class="btn btn-primary">Actualizar</button>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalGeneral">Importar</button> -->
                </div>
            </div>
        </div>

        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="d-flex justify-content-between">
                        @if ($this->id_seleccionado == 0)
                        Crear Nuevo Plan
                        @else
                        <b>{{ $cuenta->codigo }}</b> {{ $cuenta->nombre }}
                        @endif
                    </h3>
                    @if ($this->id_seleccionado !== 0)
                    <a wire:click="reiniciarNuevo();" class="text-primary">Nuevo</a>
                    @endif
                </div>

                <div class="card-body">
                    <div class="row">
                        @if ($errors->any())
                        <div class="col-12">
                            <div class="info-box bg-light">
                                <div class="info-box-content">
                                    <span class="info-box-text text-danger"><b>Revisa la siguiente información</b></span>
                                    @foreach ($errors->all() as $error)
                                    <small class="text-danger">{{ $error }}</small>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="col-6">
                            <label>Nombre</label>
                            <input type="text" class="form-control" placeholder="Nombre Plan" wire:model="nombre">
                        </div>
                        <div class="col-6">
                            <label>Codigo</label>
                            <input type="text" class="form-control" placeholder="Codigo" wire:model="codigo">
                        </div>


                        <div class="row col-12 mt-3">
                            <div class="col-6">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" wire:model="prestamo">
                                        <label class="form-check-label">Préstamo</label>
                                        <p class="small text-muted">Cuenta de tipo Préstamos.</p>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" wire:model="gasto">
                                        <label class="form-check-label">Gasto</label>
                                        <p class="small text-muted">Cuenta de tipo Gasto.</p>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" wire:model="utilidad">
                                        <label class="form-check-label">Utilidad</label>
                                        <p class="small text-muted">Cuenta de tipo Utilidad.</p>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" wire:model="estado_resultados">
                                        <label class="form-check-label">Estado de resultados</label>
                                        <p class="small text-muted">Estado de resultados.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row col-6">
                                <div class="col-6">
                                    <label>Dias desde</label>
                                    <input class="form-control form-control-sm" type="number" wire:model="tiempo_inicio">
                                </div>
                                <div class="col-6">
                                    <label>Dias Hasta</label>
                                    <input class="form-control form-control-sm" type="number" wire:model="tiempo_fin">
                                </div>
                                <div class="col-12">
                                    <label class="small">Bancos</label>
                                    <select class="form-control form-control-sm" wire:model="banco_id">
                                        <option value=""> --SELECCIONE--</option>
                                        @foreach ($bancos as $ban)
                                        <option value="{{ $ban->id }}">{{ $ban->nombre }} #{{(empty($ban->numero_cuenta) ? '' : $ban->numero_cuenta)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="small">Accion (A + /B -)</label>
                                    <select class="form-control form-control-sm" wire:model="accion_cuenta">
                                        <option value=""> --SELECCIONE--</option>
                                        <option value="SUMA"> SUMA (A)</option>
                                        <option value="RESTA"> RESTA (B) </option>
                                        
                                    </select>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
                <div class="card-footer">
                    @if($this->id_seleccionado !== 0)
                    <a class="btn btn-primary text-white" wire:click="actualizarPlan();">Actualizar</a>
                    @else
                    <a class="btn btn-success text-white pull-right" wire:click="nuevoPlan();">Guardar Nuevo</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
<script>

</script>