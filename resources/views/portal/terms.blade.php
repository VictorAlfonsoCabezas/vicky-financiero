@extends('portal.layout')
@section('title', 'Términos de uso')
@section('content')
<h1 class="page-header">Términos de uso</h1>
@forelse($terms as $term)<article class="card mb-3 portal-form"><div class="card-body"><h2 class="h4">{{ $term->name }}</h2><div class="mb-3 lh-lg" style="white-space: pre-wrap">{{ strip_tags(html_entity_decode($term->description)) }}</div>
@if(in_array($term->id, $accepted))<span class="badge bg-success">Aceptado</span>@else<form method="POST" action="{{ route('portal.terms') }}" class="mt-3">@csrf<input type="hidden" name="termino" value="{{ $term->id }}"><label class="form-check mb-3"><input class="form-check-input" type="checkbox" name="aceptar" value="1" required> He leído y acepto estos términos.</label><button class="btn btn-primary">Registrar aceptación</button></form>@endif
</div></article>@empty<p>No hay términos de uso publicados por la caja.</p>@endforelse
@endsection
