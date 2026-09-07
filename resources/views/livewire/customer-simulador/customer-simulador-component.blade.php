<div>
   <div class="row">
      <div class="col-12">
         <div class="row  mt-3">
            <div class="col-4">
               <label class="small">Valor</label>
               <div class="input-group input-group-sm">
                  <input id="valor_simulador" type="number" wire:model="valor_simulador" class="form-control" placeholder="Valor">
               </div>
            </div>
            <div class="col-4">
               <label class="small">Cuotas</label>
               <div class="input-group input-group-sm">
                  <input id="cuotas_simulador" type="number" wire:model="cuotas_simulador" class="form-control" placeholder="Número de Cuotas">
               </div>
            </div>
            <div class="col-4">
               <label class="small">Fecha Prestamo</label>
               <div class="input-group input-group-sm">
                  <input id="fecha_prestamo" type="date" wire:model="fecha_prestamo" class="form-control" placeholder="Fecha Prestamo">
               </div>
            </div>

         </div>
         <div class="row  mt-3">
            <div class="col-4">
               <label class="small">Tipo </label>
               <select id="tipo_simulador" wire:model="tipo_simulador" class="form-control form-control-sm" wire:change="obtenerDatos" wire:key="tipo_simulador">
                  <option value=""> --SELECCIONE--</option>
                  <option value="F">FRANCESA (CUOTA FIJA)</option>
                  <option value="A">ALEMANA (CAPITAL FIJO)</option>
               </select>
            </div>
            <div class="col-4">
               <label class="small">Prestamo </label>
               <select id="prestamo_simulador" wire:model="prestamo_simulador" class="form-control form-control-sm" wire:change="obtenerDatosCredito" wire:key="prestamo_simulador">
                  <option value=""> --SELECCIONE--</option>
                  @foreach ($opcionesPrestamo as $opcion)
                  <option value="{{ $opcion->id }}">{{ $opcion->name }}</option>
                  @endforeach
               </select>
            </div>

         </div>
         <br>
         <br>
         <hr>
         <div class="col-12">
            <button type="button" wire:click="simular" class="btn btn-block btn-primary btn-xs" wire:loading.attr="disabled">Generar</button>
         </div>
         <div class="row  mt-3">
            @if ($this->verPdf == true)
            <div class="col-12">
               <a wire:click="generarPdfSimulador" class="btn btn-info btn-xs" style="color: white;"><i class="fas fa-eye"></i></a>
            </div>
            @endif
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
</div>