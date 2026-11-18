@extends('layouts.app')
@section('title', 'Creditos')
@section('custom_css_rules')
<link href="{{ asset('css/savings.css') }}" rel="stylesheet">
<link href="{{ asset('css/loans.css') }}" rel="stylesheet">
@stop
@section('content')
    <livewire:creditos.creditos-componet :customer_id="$id"/>    
@endsection
@section('scripts')
    <script>
        window.addEventListener('aviso-desembolso-credito', function ({ detail }) {
            const modal = document.getElementById('modalGeneral6');
            const modalAbierto = modal && modal.classList.contains('show');
            const exito = detail.color === 'success';
            const mostrar = function () {
                Swal.fire({
                    titleText: detail.titulo,
                    text: detail.mensaje,
                    icon: exito ? 'success' : 'error',
                    confirmButtonText: 'Aceptar',
                    target: modalAbierto && !exito ? modal : document.body
                });
            };

            if (exito && modalAbierto) {
                modal.addEventListener('hidden.bs.modal', mostrar, { once: true });
            } else {
                mostrar();
            }
        });
    </script>
@stop
