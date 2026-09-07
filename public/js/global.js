$(document).ready(function () {
    $('.sidebar-mini').addClass('sidebar-collapse');
    var menu = $('ul.nav-sidebar').find('a.active').parents('li.has-treeview');
    menu.addClass('menu-open');
    menu.children('a').addClass('active');

    //Livewire evento escucha cerrar modal
    window.addEventListener('closeModal', event => {
        $('#modalGeneral').modal('hide');
        $('#modalGeneral1').modal('hide');
        $('#modalGeneral2').modal('hide');
        $('#modalGeneral3').modal('hide');
        $('#modalGeneral4').modal('hide');
        $('#modalGeneral5').modal('hide');
        $('#modalGeneral6').modal('hide');
        $('#modalGeneral7').modal('hide');
        $('#modalGeneral8').modal('hide');
        $('#modalGeneral9').modal('hide');
        $('#modalGeneral10').modal('hide');
    });

    // Modal Utilidades
    window.addEventListener('close-modal', event => {
        var modalId = event.detail.modalId;
        if (modalId) {
            $('#' + modalId).modal('hide');
        }
    });

    // Modal Ejecutar Pago Automatico Forzado
    window.addEventListener('openConfigWarningModal', event => {
        $('#modalAdvertenciaConfig').modal('show');
    });

    window.addEventListener('closeConfigWarningModal', event => {
        $('#modalAdvertenciaConfig').modal('hide');
    });

    //Livewire evento alertas
    window.addEventListener('alerta', function(event) {
        var data = event.detail;
        $(document).Toasts('create', {
            class: 'bg-' + data.color,
            title: data.titulo,
            body: data.mensaje,
            autohide: true,
            delay: 6000
        });
    });

    //Livewire evento SWALL
    window.addEventListener('alertaGrande', function(event) {
        var data = event.detail;
        Swal.fire({
            type: "warning",
            html: `
                ${data.titulo}<br>${data.mensaje}
            `,
            confirmButtonText: "ACEPTAR"
        });
        // var data = event.detail;
        // $(document).Toasts('create', {
        //     class: 'bg-' + data.color,
        //     title: data.titulo,
        //     body: data.mensaje,
        //     autohide: true,
        //     delay: 6000
        // });
    });
});
var MensajeVerde = function () {
    return {
        validacionGeneral: function (id, reglas, mensajes) {
            const formulario = $('#' + id);
            formulario.validate({
                rules: reglas,
                messages: mensajes,
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "", // validate all fields including form hidden input
                highlight: function (element, errorClass, validClass) { // hightlight error inputs
                    $(element).closest('.form-group').addClass('has-error'); // set error class to the control group
                },
                unhighlight: function (element) { // revert the change done by hightlight
                    $(element).closest('.form-group').removeClass('has-error'); // set error class to the control group
                },
                success: function (label) {
                    label.closest('.form-group').removeClass('has-error'); // set success class to the control group
                },
                errorPlacement: function (error, element) {
                    if ($(element).is('select') && element.hasClass('bs-select')) {//PARA LOS SELECT BOOSTRAP
                        error.insertAfter(element);//element.next().after(error);
                    } else if ($(element).is('select') && element.hasClass('select2-hidden-accessible')) {
                        element.next().after(error);
                    } else if (element.attr("data-error-container")) {
                        error.appendTo(element.attr("data-error-container"));
                    } else {
                        error.insertAfter(element); // default placement for everything else
                    }
                },
                invalidHandler: function (event, validator) { //display error alert on form submit

                },
                submitHandler: function (form) {
                    return true;
                }
            });
        },
        notificaciones: function (mensaje, titulo, tipo) {
            toastr.options = {
                closeButton: true,
                newestOnTop: true,
                positionClass: 'toast-top-right',
                preventDuplicates: true,
                timeOut: '8000'
            };
            if (tipo == 'error') {
                toastr.error(mensaje, titulo);
            } else if (tipo == 'success') {
                toastr.success(mensaje, titulo);
            } else if (tipo == 'info') {
                toastr.info(mensaje, titulo);
            } else if (tipo == 'warning') {
                toastr.warning(mensaje, titulo);
            }
        },
    }
}();
function cambioEmpresa(id) {
    Swal.fire({
        title: "Quieres cambiar de empresa",
        text: "¡Tu podras trasladarte entre tus empresas!",
        type: "info",
        showCancelButton: true,
        confirmButtonText: "Si, trasladarme"
    }).then(function (result) {
        if (result.value) {
            $.ajax({
                url: "/company/changeCompany/" + id,
                type: 'GET',
                success: function (res) {

                    if (res) {
                        Swal.fire('Se cambiara de Empresa...');
                        location.reload(true);
                    }
                }
            });
        }
    });

}

function loading() {
    $('#cargando').css({display: 'block'});
}

function stoploading() {
    $('#cargando').css({display: 'none'});
}

function RoundNumber(rnum, rlength) { // Arguments: number to round, number of decimal places
    var newnumber = Math.round(rnum * Math.pow(10, rlength)) / Math.pow(10, rlength);
    return newnumber; // Output the result to the form field (change for your purposes)
}

function __URL() {
    var URLactual = $(location).attr('href');
    var url = URLactual.split('/');
    var url_final = url[0] + '//' + url[2] + '/';
    return url_final;
}