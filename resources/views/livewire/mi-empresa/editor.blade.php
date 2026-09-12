<div class="mi-empresa-module">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div><span class="text-primary fw-bold"></span>
            <h2 class="h3 mt-3 mb-1">{{ $company->exists ? ($company->comercial_name ?: $company->company_name) : 'Configura una nueva empresa' }}</h2>
            <p class="text-muted mb-0">Información organizada para administrar tu caja. Los campos con * son obligatorios.</p>
        </div>@if($company->exists)<span class="badge {{ $company->status ? 'bg-success' : 'bg-secondary' }} px-3 py-2">{{ $company->status ? 'Empresa activa' : 'Empresa inactiva' }}</span>@endif
    </div>
    @include('mi-empresa.messages')
    @if($message)<div class="alert alert-success" role="status">{{ $message }}</div>@endif
    <form id="mi-empresa-form" wire:submit.prevent="save" enctype="multipart/form-data">
        <div class="row g-4">
            <aside class="col-xl-3">
                <div class="mi-empresa-form-sidebar">
                    <div class="card border-0 mb-3">
                        <div class="card-body text-center">
                            <div class="mi-empresa-logo-preview mx-auto mb-3"><img id="mi-empresa-logo" src="{{ $photo && !$errors->has('photo') ? $photo->temporaryUrl() : ($company->photo && is_file(public_path('uploads/companies/' . basename($company->photo))) ? asset('uploads/companies/' . basename($company->photo)) : asset('img/no-disponible.png')) }}" alt="Logotipo de la empresa"></div>
                            <h3 class="h5 mb-1" id="mi-empresa-preview-name">{{ ($data['comercial_name'] ?? '') ?: 'Tu empresa' }}</h3>
                            <p class="small text-muted">Logotipo para documentos</p><label for="photo" class="form-label small">Cambiar logotipo</label><input id="photo" name="photo" wire:model="photo" type="file" class="form-control form-control-sm @error('photo') is-invalid @enderror" accept="image/jpeg,image/png,image/webp">
                            <div class="form-text">JPG, PNG o WebP · máximo 2 MB.</div><span wire:loading wire:target="photo" class="small text-primary" role="status">Subiendo logotipo...</span>
                            <div id="mi-empresa-photo-message" class="small mt-2" role="status"></div>@error('photo')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <nav class="card border-0" aria-label="Secciones de configuración">
                        <div class="list-group list-group-flush">
                            @foreach($sections as $id => $section)
                            <a href="#{{ $id }}" class="list-group-item list-group-item-action py-3">
                                <i class="fa {{ $section['icon'] }} me-2 text-primary" aria-hidden="true"></i>
                                {{ $section['title'] }}
                            </a>
                            @endforeach
                        </div>
                    </nav>
                </div>
            </aside>
            <div class="col-xl-9">
                @foreach($sections as $id => $section)<section id="{{ $id }}" class="panel panel-inverse mi-empresa-panel mi-empresa-section mb-4" aria-labelledby="heading-{{ $id }}">
                    <div class="panel-heading">
                        <h3 class="panel-title" id="heading-{{ $id }}"><i class="fa {{ $section['icon'] }} me-2" aria-hidden="true"></i>{{ $section['title'] }}</h3>
                    </div>
                    <div class="panel-body p-4">
                        <p class="text-muted mb-4">{{ $section['description'] }}</p>
                        <div class="row g-4">@foreach($section['fields'] as $name => $field) @include('livewire.mi-empresa.field') @endforeach</div>
                    </div>
                </section>@endforeach
            </div>
        </div>
        <div class="mi-empresa-savebar d-flex flex-wrap align-items-center justify-content-between gap-3 mt-2"><span class="small text-muted" id="mi-empresa-save-state" role="status">{{ $company->exists ? 'Edita los campos y guarda tus cambios.' : 'Completa los datos para crear la empresa.' }}</span>
            <div class="d-flex gap-2"><button class="btn btn-primary px-4" type="submit" wire:loading.attr="disabled" wire:target="save,photo"><i class="fa fa-save me-2" aria-hidden="true"></i>{{ $company->exists ? 'Guardar cambios' : 'Crear empresa' }}</button></div>
        </div>
    </form>
</div>