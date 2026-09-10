@php
    $type = $field[1];
    $stored = $company->getAttribute($field[5] ?? $name);
    $value = $data[$name] ?? ($field[3] ?? '');
    $visible = true;
    if (in_array($name, ['category_type', 'domicilio'])) $visible = (string) ($data['company_type'] ?? '') === '2';
    if ($name === 'fecha_inicio_contable') $visible = !empty($data['contabilidad']);
    if ($name === 'time_cron') $visible = !empty($data['active_cron']);
    if ($name === 'valor_notificado') $visible = !empty($data['genera_gastos_cobranza']);
    if ($name === 'numero_dias_interes') $visible = ($data['select_tipo_interes'] ?? 'M') !== 'M';
    if ($type === 'time' && $value) $value = substr($value, 0, 5);
    $required = strpos($field[2], 'required') !== false;
    $integer = strpos($field[2], 'integer') !== false;
@endphp
@if($visible)
<div wire:key="field-{{ $name }}" class="{{ $type === 'textarea' ? 'col-12' : 'col-md-6' }}" data-company-field="{{ $name }}">
@if($type === 'switch')
<div class="company-switch p-3 rounded h-100"><div class="form-check form-switch mb-0"><input type="checkbox" wire:model="data.{{ $name }}" class="form-check-input @error($name) is-invalid @enderror" id="{{ $name }}" name="{{ $name }}" value="1" {{ $value ? 'checked' : '' }}><label for="{{ $name }}" class="form-check-label">{{ $field[0] }}</label></div></div>
@else
<label for="{{ $name }}" class="form-label fw-semibold">{{ $field[0] }}@if($required)<span class="text-danger" aria-hidden="true"> *</span>@endif</label>
@if($type === 'select' || $type === 'category')
<select wire:model="data.{{ $name }}" class="form-select @error($name) is-invalid @enderror" id="{{ $name }}" name="{{ $name }}" {{ $required ? 'required' : '' }}>
@if($type === 'category')<option value="">Sin categoría</option>@foreach($category as $option)<option value="{{ $option->id }}" {{ (string) $value === (string) $option->id ? 'selected' : '' }}>{{ $option->title }}</option>@endforeach
@else @foreach($field[4] as $key => $label)<option value="{{ $key }}" {{ (string) $value === (string) $key ? 'selected' : '' }}>{{ $label }}</option>@endforeach @endif
</select>
@elseif($type === 'textarea')<textarea wire:model.defer="data.{{ $name }}" class="form-control @error($name) is-invalid @enderror" id="{{ $name }}" name="{{ $name }}" rows="3" maxlength="255">{{ $value }}</textarea>
@else
<input wire:model.defer="data.{{ $name }}" class="form-control {{ $type === 'color' ? 'form-control-color' : '' }} @error($name) is-invalid @enderror" type="{{ $type }}" id="{{ $name }}" name="{{ $name }}" value="{{ $value }}" {{ $required ? 'required' : '' }} @if($type === 'number') step="{{ $integer ? '1' : '0.01' }}" min="{{ $name === 'numero_decimales' ? '2' : '0' }}" @endif @if($name === 'ruc') inputmode="numeric" pattern="[0-9]{13}" maxlength="13" @elseif($name === 'phone') maxlength="15" @elseif($type === 'text' || $type === 'email') maxlength="255" @endif @if($errors->has($name)) aria-invalid="true" aria-describedby="error-{{ $name }}" @endif>
@endif
@endif
@error($name)<div id="error-{{ $name }}" class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>

@endif
