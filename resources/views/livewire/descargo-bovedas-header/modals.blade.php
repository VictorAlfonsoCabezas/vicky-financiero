@foreach ([
    'carga' => ['Carga inicial', 'storeCarga', 'Registrar carga', 'btn-primary'],
    'gasto' => ['Gasto inicial', 'storeGastoInicial', 'Registrar gasto', 'btn-danger'],
    'transferencia' => ['Transferir entre bóvedas', 'storeTransferencia', 'Confirmar transferencia', 'btn-primary'],
] as $tipo => $modal)
    <div wire:ignore.self class="modal fade" id="vault-modal-{{ $tipo }}" tabindex="-1" aria-labelledby="vault-modal-title-{{ $tipo }}" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title h5" id="vault-modal-title-{{ $tipo }}">{{ $modal[0] }}</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar" wire:loading.attr="disabled" wire:target="{{ $modal[1] }}"></button>
                </div>
                <form wire:submit.prevent="{{ $modal[1] }}">
                    <div class="modal-body p-4">
                        <div class="alert alert-primary mb-4"><span class="small d-block">{{ $tipo === 'transferencia' ? 'Bóveda de origen' : 'Bóveda seleccionada' }}</span><strong>{{ $bovedaNombre }}</strong></div>
                        @if ($errors->any())<div class="alert alert-danger" role="alert"><strong>Revisa los datos antes de continuar.</strong><ul class="mb-0 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

                        @if ($tipo === 'transferencia')
                            <div class="mb-3">
                                <label for="vault-destination" class="form-label">Bóveda de destino</label>
                                <select id="vault-destination" class="form-select @error('trans_boveda_id') is-invalid @enderror" wire:model.defer="trans_boveda_id" required>
                                    <option value="">Selecciona una bóveda</option>
                                    @foreach($bovedasTransferencia as $destino)@if((string) $destino['id'] !== (string) $boveda_id)<option value="{{ $destino['id'] }}">{{ $destino['nombre'] }}</option>@endif @endforeach
                                </select>
                            </div>
                        @endif
                        @if ($tipo !== 'gasto')
                            @php($campoBanco = $tipo === 'carga' ? 'banco_id' : 'trans_banco_id')
                            <div class="mb-3">
                                <label for="vault-bank-{{ $tipo }}" class="form-label">Banco</label>
                                <select id="vault-bank-{{ $tipo }}" class="form-select @error($campoBanco) is-invalid @enderror" wire:model.defer="{{ $campoBanco }}" required>
                                    <option value="">Selecciona un banco</option>
                                    @foreach ($bancos as $banco)<option value="{{ $banco['id'] }}">{{ $banco['nombre'] }} · {{ $banco['numero_cuenta'] }}</option>@endforeach
                                </select>
                                @if(!count($bancos))<div class="form-text text-danger">No hay bancos activos con una cuenta registrada.</div>@endif
                            </div>
                        @endif
                        @if ($tipo === 'carga')
                            <div class="mb-3">
                                <label for="vault-operation" class="form-label">Operación</label>
                                <select id="vault-operation" class="form-select @error('operacion_id') is-invalid @enderror" wire:model.defer="operacion_id" required>
                                    <option value="">Selecciona una operación</option>
                                    @foreach ($cargaInicial as $carga)<option value="{{ $carga['id'] }}">{{ $carga['nombre'] }}</option>@endforeach
                                </select>
                            </div>
                        @endif
                        @php($campoValor = $tipo === 'carga' ? 'valor' : ($tipo === 'gasto' ? 'valor_gasto' : 'trans_valor'))
                        <div class="mb-3">
                            <label for="vault-amount-{{ $tipo }}" class="form-label">Valor</label>
                            <div class="input-group"><span class="input-group-text">$</span><input id="vault-amount-{{ $tipo }}" type="number" step="0.01" inputmode="decimal" class="form-control @error($campoValor) is-invalid @enderror" placeholder="0.00" wire:model.defer="{{ $campoValor }}" required></div>
                        </div>
                        @if ($tipo !== 'carga')
                            @php($campoDescripcion = $tipo === 'gasto' ? 'descripcion_gasto' : 'trans_observacion')
                            <div>
                                <label for="vault-description-{{ $tipo }}" class="form-label">{{ $tipo === 'gasto' ? 'Descripción del gasto' : 'Observación' }}</label>
                                <textarea id="vault-description-{{ $tipo }}" rows="3" class="form-control @error($campoDescripcion) is-invalid @enderror" wire:model.defer="{{ $campoDescripcion }}" placeholder="Describe el motivo de la operación" required></textarea>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-bs-dismiss="modal" wire:loading.attr="disabled" wire:target="{{ $modal[1] }}">Cancelar</button>
                        <button type="submit" class="btn {{ $modal[3] }}" wire:loading.attr="disabled" wire:target="{{ $modal[1] }}">
                            <span wire:loading.remove wire:target="{{ $modal[1] }}">{{ $modal[2] }}</span><span wire:loading wire:target="{{ $modal[1] }}" role="status">Guardando…</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
