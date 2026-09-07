<div id="document-template-editor">
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <label for="document-template-name" class="form-label">Nombre del formato</label>
            <input id="document-template-name" class="form-control" wire:model.defer="nombredocumento" maxlength="255">
            @error('nombredocumento')<span class="text-danger">{{ $message }}</span>@enderror
        </div>
        <div class="col-md-6">
            <label for="valorInsertar" class="form-label">Variables disponibles</label>
            <select id="valorInsertar" wire:model="valorInsertar" class="form-select" wire:change="valorInsertadoCambiado">
                <option value="">Seleccione una variable</option>
                @foreach($variables as $variable)<option value="{{ $variable->id }}">{{ $variable->variable }}</option>@endforeach
            </select>
            <span class="form-text">{{ $nombreVariable }}</span>
        </div>
    </div>
    <label for="document-template-content" class="form-label">Contenido</label>
    <div wire:ignore>
        <textarea id="document-template-content" class="form-control" rows="18">{{ $contenido }}</textarea>
    </div>
    @error('contenido')<span class="text-danger">{{ $message }}</span>@enderror
    <div class="mt-3">
        <button type="button" class="btn btn-primary" data-save-document wire:loading.attr="disabled">Guardar formato</button>
    </div>
</div>
@once
<link href="https://cdn.jsdelivr.net/npm/froala-editor@4.0.11/css/froala_editor.pkgd.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/froala-editor@4.0.11/js/froala_editor.pkgd.min.js"></script>
<script src="{{ asset('js/document-editor.js') }}"></script>
@endonce
