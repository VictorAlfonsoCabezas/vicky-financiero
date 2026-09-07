$(document).ready(function () {
    if (localStorage.getItem("val_domicilio") !== null) {
        totalItems();
    }
});
function addKart(id) {
    $.ajax({
        method: "GET",
        url: "/buscarProducto/" + id
    }).done(function (res) {
        var id = res.id;
        var price = res.precio_a;
        var name = res.name;
        var description = res.description;
        var photo = res.photo;
        var cantidad = 1;
        addResumenProduct(id, price, name, photo, description, cantidad);
        updateResumenTotal();
        llenarCestaTono();
    });
}

function llenarCestaTono() {
    var audio = new Audio('/media/audio/llenarcesta.mp3');
    audio.play();
}

function lessKart(id) {
    var cantNow = $('#p-cantidad-' + id).val();
    if (cantNow > 1) {
        var cantNew = parseInt(cantNow - 1);
        var itemsNew = JSON.parse(localStorage.getItem("items"));
        itemsNew.forEach(function (value, base, array) {
            if (value.id == id) {
                itemsNew[base].cantidad = cantNew;
            }
        });
        localStorage.setItem('items', JSON.stringify(itemsNew));
        updateResumenTotal();
        totalItems();
    } else {
        $('.resumen-' + id).remove();
        itemsNew = JSON.parse(localStorage.getItem("items"));
        itemsNew.forEach(function (elemento, indice, array) {
            if (elemento.id == id) {
                itemsNew.splice(indice, 1);
            }
        });
        localStorage.setItem('items', JSON.stringify(itemsNew));
        updateResumenTotal();
        totalItems();
    }
}

//agregar producto
function addResumenProduct(id, price, name, photo, description, cantidad) {
    var incremento = false;
    var val = {};
    val['id'] = id;
    val['price'] = price;
    val['name'] = name;
    val['photo'] = photo;
    val['description'] = description;
    val['cantidad'] = cantidad;
    var items = [];
    if (localStorage.getItem("items") !== null) {
        items = JSON.parse(localStorage.getItem("items"));
    }
    items.forEach(function (value, base, array) {
        if (value.id == id) {
            var cantnew = parseInt(value.cantidad) + parseInt(cantidad);
            items[base].cantidad = cantnew;
            incremento = true;
        }
    });
    if (incremento == false) {
        items.push(val);
    }
    localStorage.setItem('items', JSON.stringify(items));
    totalItems();
}
//actuliaz valores
function updateResumenTotal() {
    var items_pedido = JSON.parse(localStorage.getItem("items"));
    var subtotal = 0;
    var val_domicilio = JSON.parse(localStorage.getItem("val_domicilio"));
    var valor = 0;
    var cantidad = 0;
    var costo = 0;
    $.each(items_pedido, function (key, value) {
        if (value.id !== undefined) {
            valor = parseFloat(value.price);
            cantidad = parseInt(value.cantidad);
            costo = parseFloat(valor * cantidad);
            subtotal = parseFloat(subtotal + costo);
            $('#p-costo-' + value.id).html('$ ' + Number((costo * 100) / 100).toFixed(2));
            $('#p-cantidad-' + value.id).val(cantidad);
        }
    });
    var total = parseFloat(subtotal + val_domicilio);
    total = Number((total * 100) / 100).toFixed(2);
    var domicilio = Number((val_domicilio * 100) / 100).toFixed(2);
    subtotal = Number((subtotal * 100) / 100).toFixed(2);
    $('#totalResumen').html('$ ' + total);
    $('#total_resumen').html('$ ' + total);
    $('#domicilio_resumen').html('$ ' + Number((domicilio * 100) / 100).toFixed(2));
    localStorage.setItem("subtotal", subtotal);
    localStorage.setItem("total", total);
}
//contador items carrito
function totalItems() {
    if (localStorage.getItem("items") === null) {
        total_items = 0;
    } else {
        total_items = parseInt(JSON.parse(localStorage.getItem("items")).length);
    }
    if (total_items == 0) {
        var vacio = '';
        vacio += '<center><img src="/img/carrovacio.png" width="260px" height="200px" class="img-rounded"><br><h1>VACIO</h1></center>';
        vacio += '<hr style="border: 1px dashed black;"/>';
        $('.resumen-item').html(vacio);
        $('#totalResumen').html('$ 0.00');
        $('#domicilio_resumen').html('$ 0.00');
        $('#total_resumen').html('$ 0.00');
    }
    $('#contadorItem').html(total_items);
}
//Resumen de compra
function mostrarModal() {
    //preguntar si no existe array
    if (localStorage.getItem("items") === null) {
        total_items = 0;
    } else {
        total_items = parseInt(JSON.parse(localStorage.getItem("items")).length);
    }
    if (total_items !== 0) {
        var items_pedido = JSON.parse(localStorage.getItem("items"));
        var subtotal = 0;
        var subtotaliva = 0;
        var domicilio = JSON.parse(localStorage.getItem("val_domicilio"));
        var valor = 0;
        var cantidad = 0;
        var costo = 0;
        var resumen = '';
        $.each(items_pedido, function (key, value) {
            if (value.id !== undefined) {
                valor = parseFloat(value.price);
                cantidad = parseInt(value.cantidad);
                costo = parseFloat(valor * cantidad);
                costo = Number((costo * 100) / 100).toFixed(2);
                subtotal = parseFloat(subtotal + costo);
                resumen += '<section class="col-lg-3 col-xs-3 resumen-' + value.id + '" style="padding: 0px;">';
                if (value.photo == null) {
                    resumen += '      <img src="/img/sinimagenprod.jpg" width="65px" height="65px" class="img-rounded"><br>';
                } else {
                    resumen += '      <img src="/uploads/products/' + value.photo + '" width="65px" height="65px" class="img-rounded"><br>';
                }
                resumen += '</section>';
                resumen += '<section class="col-lg-9 col-xs-9 resumen-' + value.id + '" style="padding: 2 2px 0 0; top: -12px;">';
                resumen += '   <h5 class="text-danger">' + value.name + ': <b class="pull-right text-primary" id="p-costo-' + value.id + '">$ ' + costo + '</b></h5>';
                resumen += '   <p style="font-size: 12px; text-align: left;"><font face="Comic Sans MS, arial, verdana">' + value.description + '</font></p>';
                resumen += '   <a class="btn btn-circle btn-dark btn-sm" onclick="lessKart(' + value.id + ');"><i class="fa fa-minus"></i></a>';
                resumen += '   <input type="text" style="width: 45px; text-align: center;height: 35px;" value="' + cantidad + '" id="p-cantidad-' + value.id + '">';
                resumen += '   <a class="btn btn-circle btn-dark btn-sm" onclick="addKart(' + value.id + ');"><i class="fa fa-plus"></i></a>';
                resumen += '</section>';
                resumen += '<hr class="resumen-' + value.id + '" style="border: 1px dashed black;"/>';
            }
        });
        var total = parseFloat(subtotal + domicilio);
        total = Number((total * 100) / 100).toFixed(2);
        var subtotal = Number((((JSON.parse(localStorage.getItem("subtotal")))) * 100) / 100).toFixed(2);
        var domicilio = Number((domicilio * 100) / 100).toFixed(2);
        $('#totalResumen').html('$ ' + total);
        $('#domicilio_resumen').html('$ ' + domicilio);
        $('#total_resumen').html('$ ' + total);
        $('.resumen-item').html(resumen);
        $('#formResumen').modal('show');
    } else {
        var vacio = '';
        vacio += '<center><img src="/img/carrovacio.png" width="260px" height="200px" class="img-rounded"><br><h1>VACIO</h1></center>';
        vacio += '<hr style="border: 1px dashed black;"/>';
        $('.resumen-item').html(vacio);
        $('#totalResumen').html('$ 0.00');
        $('#domicilio_resumen').html('$ 0.00');
        $('#total_resumen').html('$ 0.00');
        $('#formResumen').modal('show');
    }
}
//resumen de compra
function mostrarResumen() {
    var total_items = parseInt(JSON.parse(localStorage.getItem("items")).length);
    if (total_items !== null && total_items !== 0) {
        var items_pedido = JSON.parse(localStorage.getItem("items"));
        var subtotal = 0;
        var domicilio = JSON.parse(localStorage.getItem("val_domicilio"));
        var valor = 0;
        var cantidad = 0;
        var costo = 0;
        var resumen = '';
        $.each(items_pedido, function (key, value) {
            if (value.id !== undefined) {
                valor = parseFloat(value.price);
                cantidad = parseInt(value.cantidad);
                costo = parseFloat(valor * cantidad);
                costo = Number((costo * 100) / 100).toFixed(2);
                subtotal = parseFloat(subtotal + costo);
                resumen += '<section class="col-lg-3 col-xs-3 resumen-' + value.id + '" style="padding: 0px;">';
                if (value.photo == null) {
                    resumen += '      <img src="/img/sinimagenprod.jpg" width="65px" height="65px" class="img-rounded"><br>';
                } else {
                    resumen += '      <img src="/uploads/products/' + value.photo + '" width="65px" height="65px" class="img-rounded"><br>';
                }
                resumen += '</section>';
                resumen += '<section class="col-lg-9 col-xs-9 resumen-' + value.id + '" style="padding: 2 2px 0 0; top: -12px;">';
                resumen += '   <h5 class="text-danger">' + value.name + ': <b class="pull-right text-primary" id="p-costo-' + value.id + '">$ ' + costo + '</b></h5>';
                resumen += '   <p style="font-size: 12px; text-align: left;"><font face="Comic Sans MS, arial, verdana">' + value.description + '</font></p>';
                resumen += '   <a class="btn btn-circle btn-dark btn-sm" onclick="lessKart(' + value.id + ');"><i class="fa fa-minus"></i></a>';
                resumen += '   <input type="text" style="width: 45px; text-align: center;height: 35px;" value="' + cantidad + '" id="p-cantidad-' + value.id + '">';
                resumen += '   <a class="btn btn-circle btn-dark btn-sm" onclick="addKart(' + value.id + ');"><i class="fa fa-plus"></i></a>';
                resumen += '</section>';
                resumen += '<hr class="resumen-' + value.id + '" style="border: 1px dashed black;"/>';
            }
        });
        var total = parseFloat(subtotal + domicilio);
        total = Number((total * 100) / 100).toFixed(2);
        var subtotal = Number((((JSON.parse(localStorage.getItem("subtotal")))) * 100) / 100).toFixed(2);
        var domicilio = Number((domicilio * 100) / 100).toFixed(2);
        $('#totalResumen').html('$ ' + total);
        $('#domicilio_resumen').html('$ ' + domicilio);
        $('#total_resumen').html('$ ' + total);
        $('.resumen-item').html(resumen);
    } else {
        var vacio = '';
        vacio += '<center><img src="/img/carrovacio.png" width="260px" height="200px" class="img-rounded"><br><h1>VACIO</h1></center>';
        vacio += '<hr style="border: 1px dashed black;"/>';
        $('.resumen-item').html(vacio);
        $('#totalResumen').html('$ 0.00');
        $('#domicilio_resumen').html('$ 0.00');
        $('#total_resumen').html('$ 0.00');
    }
}
function compraFinal() {
    var company = JSON.parse(localStorage.getItem("company"));
    console.log(company);
    window.location.href = "/buy/" + company;
}