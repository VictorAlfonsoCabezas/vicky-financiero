$(document).ready(function () {
    //funcion para consultar pedidos pendientes
//    Siempre averiguar si hay pedidos
//    notify();
//    setInterval('notify()', 5000);
});

function notify() {
    $.ajax({
        async: true,
        url: "/notify/getNotify",
        type: 'GET',
        dataType: 'json',
        success: function (res) {
            if (res) {
                console.log(res);
                var pedido = '';
                pedido += '<span class="dropdown-item dropdown-header">' + res.cantidad + ' Pedido(s)</span>';
                $.each(res.ordenes, function (index, value) {
                    pedido += '   <div class="dropdown-divider"></div>';
                    pedido += '   <a href="/orderheader" class="dropdown-item">';
                    pedido += '      <i class="fas fa-utensils mr-2"></i>' + value.nombres;
                    pedido += '      <span class="float-right text-muted text-sm">' + value.total + ' $</span>';
                    pedido += '   </a>';
                });
                pedido += '   <a href="/orderheader" class="dropdown-item dropdown-footer">Ver Pedidos</a>';
                $('#notificacionesPedidos').html(pedido);
                $('#contadorCampana').html(res.cantidad);
                sonidoAlerta();
            } else {
                var pedido = '';
                pedido += '<span class="dropdown-item dropdown-header">0 Pedidos</span>';
                pedido += '   <div class="dropdown-divider"></div>';
                pedido += '   <a href="#" class="dropdown-item">';
                pedido += '      <i class="far fa-sad-tear"></i> Sin Pedidos';
                pedido += '      <span class="float-right text-muted text-sm">tiempo</span>';
                pedido += '   </a>';
                pedido += '   <a href="/orderheader" class="dropdown-item dropdown-footer">Ir a Pedidos</a>';
                $('#notificacionesPedidos').html(pedido);
                $('#contadorCampana').html(0);
            }

        }
    });
}
function sonidoAlerta() {
    var audio = new Audio('/media/audio/pedido.mp3');
    audio.play();
}
