<?php

use Illuminate\Support\Facades\Route;

Auth::routes();

// Acceso de clientes (Caja Web), además del acceso administrativo /login.
Route::get('login2', 'Auth\Login2Controller@showLoginForm')->name('login2');
Route::post('login2', 'Auth\Login2Controller@login');
Route::post('logout2', 'Auth\Login2Controller@logout')->name('logout2');
Route::get('mi-cuenta', 'Customer\CustomerPortalController@index')
    ->middleware(['auth', \App\Http\Middleware\CheckInactivity::class])->name('portal.index');

// Buscador de clientes de la barra superior.
Route::prefix('mi-cuenta')->name('portal.')->middleware(['auth', \App\Http\Middleware\CheckInactivity::class])->group(function () {
    Route::get('cuentas/{account}/movimientos', 'Customer\PortalActionsController@movements')->name('movements');
    Route::get('acreditaciones', 'Customer\PortalActionsController@requests')->name('requests');
    Route::get('acreditaciones/{id}/comprobante', 'Customer\PortalActionsController@requestAttachment')->name('requests.attachment');
    Route::post('acreditaciones', 'Customer\PortalActionsController@saveRequest')->name('requests.save');
    Route::delete('acreditaciones/{id}', 'Customer\PortalActionsController@deleteRequest')->name('requests.delete');
    Route::get('transferencias', 'Customer\PortalActionsController@transfers')->name('transfers');
    Route::post('transferencias', 'Customer\PortalActionsController@transfer')->middleware('throttle:10,1')->name('transfers.send');
    Route::get('prestamos/{id}', 'Customer\PortalActionsController@credit')->name('credit');
    Route::get('prestamos/{id}/cuotas/{detail}/comprobante', 'Customer\PortalActionsController@creditAttachment')->name('credit.attachment');
    Route::get('prestamos/{id}/archivos/{file}', 'Customer\PortalActionsController@creditFile')->name('credit.file');
    Route::post('prestamos/{id}/cuotas/{detail}/pago', 'Customer\PortalActionsController@pay')->name('credit.pay');
    Route::match(['get', 'post'], 'password', 'Customer\PortalActionsController@password')->middleware('throttle:10,1')->name('password');
    Route::match(['get', 'post'], 'simulador', 'Customer\PortalActionsController@simulator')->name('simulator');
    Route::match(['get', 'post'], 'terminos', 'Customer\PortalActionsController@terms')->name('terms');
});

Route::get('clientes/buscar-global', 'Customer\CustomerSearchController@index')
    ->middleware(['auth', \App\Http\Middleware\CheckInactivity::class])
    ->name('clientes.buscar-global');

// USUARIOS
Route::group(['middleware' => 'App\Http\Middleware\Authenticate'], function () {
    Route::get('/', 'HomeController@index')->name('home');
});

// USUARIOS
Route::group(['middleware' => 'App\Http\Middleware\Authenticate'], function () {
    Route::get('usuarios/verDatos', 'User\UserController@verDatos')->name('usuarios.verDatos');
    Route::get('/usuarios/cambioEstado/{id}', 'User\UserController@cambioEstado')->name('usuarios.cambioEstado');
    Route::get('usuarios/indexData', 'User\UserController@indexData')->name('indexData');
    Route::get('/usuarios/darUsername/{nombre}/{apellido}', 'User\UserController@darUsername')->name('usuarios.darUsername');
    Route::get('usuarios/profile', 'User\UserController@profile')->name('profile');
    Route::resource('usuarios', 'User\UserController')->only(array(
        0 => 'index',
        1 => 'create',
        2 => 'store',
        3 => 'edit',
        4 => 'update',
        5 => 'destroy',
    ));
    Route::post('usuarios/actualizarPassword', 'User\UserController@actualizarPassword')->name('usuarios.actualizarPassword');
});

// MENU
Route::group(['middleware' => 'App\Http\Middleware\Authenticate'], function () {
    Route::resource('menu', 'Menu\MenuController')->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::get('menu/create', 'Menu\MenuController@create')->name('crear_menu');
    Route::get('menu/{id}/edit', 'Menu\MenuController@edit')->name('editar_menu');
    Route::get('menu/{id}/destroy', 'Menu\MenuController@destroy')->name('eliminar_menu');
    Route::post('menu/guardar-orden', 'Menu\MenuController@guardarOrden')->name('guardar_orden');
    Route::post('menu/guardar-nuevo', 'Menu\MenuController@guardarNuevo')->name('guardarNuevo');
    Route::post('menu/update-nemu', 'Menu\MenuController@updateNemu')->name('updateNemu');
    Route::get('menu/datos-menu/{id}', 'Menu\MenuController@datosMenu')->name('datosMenu');
});

// ROL
Route::group(['middleware' => 'App\Http\Middleware\Authenticate'], function () {
    Route::resource('rol', 'Rol\RolController')->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
});

// MENU ROL
Route::group(['middleware' => 'App\Http\Middleware\Authenticate'], function () {
    Route::resource('menu-rol', 'MenuRol\MenuRolController')->only(array(
        0 => 'index',
    ));
    Route::post('menu-rol', 'MenuRol\MenuRolController@guardar')->name('guardar_menu_rol');
});

//PRODUCTOS
Route::group(['middleware' => 'App\Http\Middleware\Authenticate'], function () {
    Route::resource('product', 'Product\ProductController')->only(array(
        0 => 'index',
    ));
});

/// CATEGORY
Route::group(['middleware' => 'App\Http\Middleware\Authenticate'], function () {
    Route::resource('category', 'Category\CategoryController')->only(array(
        0 => 'index',
        1 => 'create',
        2 => 'store',
        3 => 'edit',
        4 => 'update',
        5 => 'destroy',
    ));
});

//CUSTOMER
Route::group(['middleware' => 'App\Http\Middleware\Authenticate'], function () {
    Route::get('customer/cartilla/{code}', 'Customer\CustomerController@cartilla')->name('customer.cartilla');
    Route::get('customer/cartilla/imprimir/{code}', 'Customer\CustomerController@cartillaImprimir')->name('customer.cartillaImprimir');
    Route::resource('customer', 'Customer\CustomerController')->only(array(
        0 => 'index',
        1 => 'create',
        2 => 'store',
        3 => 'show',
        4 => 'edit',
        5 => 'update',
        6 => 'destroy',
    ));
});

//ARBOL
Route::group(['middleware' => 'App\Http\Middleware\Authenticate'], function () {
    Route::put('arbolrole/cambioMenu/{id}', 'Arbol\ArbolController@cambioMenu')->name('arbol.cambioMenu');
    Route::get('/arbolrole/tree/{rol_id}', 'Arbol\ArbolController@tree')->name('general.tree');
    Route::resource('arbolrole', 'Arbol\ArbolController')->only(array(
        0 => 'index',
        1 => 'store',
        2 => 'destroy',
    ));
});

//COUNTRIES
Route::group(['middleware' => 'App\Http\Middleware\Authenticate'], function () {
    Route::resource('country', 'Country\CountryController')->only(array(
        0 => 'index',
    ));
});

//CITIES
Route::group(['middleware' => 'App\Http\Middleware\Authenticate'], function () {
    Route::resource('city', 'City\CityController')->only(array(
        0 => 'index',
        1 => 'store',
        2 => 'edit',
        3 => 'update',
    ));
});

//REGIONS
Route::group(['middleware' => 'App\Http\Middleware\Authenticate'], function () {
    Route::resource('region', 'Region\RegionController')->only(array(
        0 => 'index',
        1 => 'store',
        2 => 'edit',
        3 => 'update',
    ));
});

Route::delete('company/desactivarCompany/{id}', 'Company\CompanyController@desactivarCompany')->middleware('auth')->name('company.desactivarCompany');
Route::get('company/conexionCompanies', 'Company\CompanyController@conexionCompanies')->middleware('auth')->name('company.conexionCompanies');

// MODULOS ONIX
Route::get('/onix', 'OnixHomeController@index')->middleware('auth')->name('onix.dashboard');
//simular publico
Route::get('/simulador-publico', 'SimuladorPublico\SimuladorPublicoController@index')->name('sumuladorPublico');


//LOGIN CLIENTES

//login cierre


/// USUARIOS


// MENU


// ROL


// MENU ROL


// COMPANY
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::patch('company/{company}/estado', 'Company\CompanyController@status')->name('company.status');
    Route::resource('company', 'Company\CompanyController')->only(array(
        0 => 'index',
        1 => 'create',
        2 => 'store',
        3 => 'edit',
        4 => 'update',
        5 => 'destroy',
    ));
    Route::get('/company/changeCompany/{id}', 'Company\CompanyController@changeCompany')->name('company.changeCompany');
});


//PRODUCTOS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('product', 'Product\ProductController')->only(array(
        0 => 'index',
    ));
});

/// CATEGORY
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('category', 'Category\CategoryController')->only(array(
        0 => 'index',
        1 => 'create',
        2 => 'store',
        3 => 'edit',
        4 => 'update',
        5 => 'destroy',
    ));
});

//CUSTOMER
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::get('customer/cartilla/{code}', 'Customer\CustomerController@cartilla')->name('customer.cartilla');
    Route::get('customer/cartilla/imprimir/{code}', 'Customer\CustomerController@cartillaImprimir')->name('customer.cartillaImprimir');
    Route::get('customer/buscarCedulas/{dato}', 'Customer\CustomerController@buscarCedulas')->name('customer.buscarCedulas');
    Route::get('customer/buscarApiDatos/{dato}', 'Customer\CustomerController@buscarApiDatos')->name('customer.buscarApiDatos');
    Route::get('customer/certificadoAhorroProgramado/{id}', 'Customer\CustomerController@certificadoAhorroProgramado')->name('customer.certificadoAhorroProgramado');
    Route::resource('customer', 'Customer\CustomerController')->only(array(
        0 => 'index',
        1 => 'create',
        2 => 'store',
        3 => 'show',
        4 => 'edit',
        5 => 'update',
        6 => 'destroy',
    ));
});

// NOTIFICACIONES
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::get('/notify/getNotify', 'Notify\NotifyController@getNotify')->name('notify.getNotify');
    Route::resource('notify', 'Notify\NotifyController')->only(array());
});

//ARBOL


//Inicio Livewire
// AUDITORIA DE CREDITOS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::get('credit-folder-audits', 'CreditFolderAudits\CreditFolderAuditsController@index')
        ->name('credit-folder-audits.index');
});

//TIPO AHORROS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('tipo-ahorros', 'TipoAhorros\TipoAhorrosController')->only(array(
        0 => 'index',
    ));
});

// UTILIDADES
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('utilidades', 'Utilidades\UtilidadesController')->only(array(
        0 => 'index',
    ));
});

//PARENTEZCO
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('parentezco', 'Parentezco\ParentezcoController')->only(array(
        0 => 'index',
    ));
});

//GENERO
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('genero', 'Genero\GeneroController')->only(array(
        0 => 'index',
    ));
});

//TIPO DE CUENTA
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('tipo-cuenta', 'TipoCuenta\TipoCuentaController')->only(array(
        0 => 'index',
    ));
});

//TIPO DOCUMENTO
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('tipo-documento', 'TipoDocumento\TipoDocumentoController')->only(array(
        0 => 'index',
    ));
});

//MESES
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('meses', 'Meses\MesesController')->only(array(
        0 => 'index',
    ));
});

//TIPO CUSTOMER
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('tipo-customer', 'TipoCustomer\TipoCustomerController')->only(array(
        0 => 'index',
    ));
});

//INTERES REGLAS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('int-reglas', 'IntReglas\IntReglasController')->only(array(
        0 => 'index',
    ));
});

//INTERES CALCULO
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('int-calculo', 'IntCalculo\IntCalculoController')->only(array(
        0 => 'index',
    ));
});

//CUSTOMER-USER
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('customer-user', 'CustomerUser\CustomerUserController')->only(array(
        0 => 'index',
    ));
});

//CAJAS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('cajas', 'Cajas\CajasController')->only(array(
        0 => 'index',
    ));
    Route::get('/cajas/caja-comprobante/{id}', 'Cajas\CajasController@cajasComprobante');
});

//CAJAS MANAGER
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('cajas-manager', 'Cajas\CajasManagerController')->only(array(
        0 => 'index',
    ));
});

//CUSTOMER-MOVIMIENTOS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('customer-movimientos', 'CustomerMovimientos\CustomerMovimientosController')->only(array(
        0 => 'index',
    ));
});

//CUSTOMER-MOVIMIENTOS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('customer-cuentas', 'CustomerCuentas\CustomerCuentasController')->only(array(
        0 => 'index',
    ));
});

// EMPRESA
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('empresa', 'Empresa\EmpresaController')->only(array(
        0 => 'index',
    ));
});

// ACCIONES
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('acciones', 'Acciones\AccionesController')->only(array(
        0 => 'index',
    ));
});

// ACCIONES DETALLE
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::get('acciones-detalle/{id}', 'AccionesDetalle\AccionesDetalleController@detalle');
    Route::get('/acciones-detalle/verEntrega/{id}', 'AccionesDetalle\AccionesDetalleController@verEntrega');
    Route::get('/acciones-detalle/verCertificado/{id}', 'AccionesDetalle\AccionesDetalleController@verCertificado');
});

// CARTERA
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('cartera', 'Cartera\CarteraController')->only(array(
        0 => 'index',
    ));
});

// CARTERA REGLAS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('cartera-reglas', 'Cartera\CarteraReglasController')->only(array(
        0 => 'index',
    ));
});

// DENOMINACION-BILLETES
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('denominacion-billetes', 'DenominacionBilletes\DenominacionBilletesController')->only(array(
        0 => 'index',
    ));
});

// ACCIONES-VALORES
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('acciones-valores', 'AccionesValores\AccionesValoresController')->only(array(
        0 => 'index',
    ));
});

// COMANDOS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('comandos', 'Comandos\ComandosController')->only(array(
        0 => 'index',
    ));
});

// CUSTOMER-PRESTAMOS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('customer-prestamos', 'CustomerPrestamos\CustomerPrestamosController')->only(array(
        0 => 'index',
    ));
});

// CUSTOMER-TRANSFERENCIAS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('customer-transferencias', 'CustomerTransferencias\CustomerTransferenciasController')->only(array(
        0 => 'index',
    ));
});

// CUSTOMER-SIMULADOR
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('customer-simulador', 'CustomerSimulador\CustomerSimuladorController')->only(array(
        0 => 'index',
    ));
});

//CUENTAS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::get('cuentas/{id}', 'Cuentas\CuentasController@cuentas');
    Route::get('/descargar-cartola/{cliente}/{cuenta}', 'Cartola\CartolaController@descargarPdf');
});

// COUNTRY
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('country', 'Country\CountryController')->only(array(
        0 => 'index',
    ));
});

// PROVINCIA
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('provincia', 'Provincia\ProvinciaController')->only(array(
        0 => 'index',
    ));
});

// PARROQUIA
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('parroquia', 'Parroquia\ParroquiaController')->only(array(
        0 => 'index',
    ));
});

// CIUDAD
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('ciudad', 'Ciudad\CiudadController')->only(array(
        0 => 'index',
    ));
});

// PLAN-CUENTAS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('plan-cuentas', 'PlanCuentas\PlanCuentasController')->only(array(
        0 => 'index',
    ));
});

//CONCILIACION
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('conciliacion', 'Conciliacion\ConciliacionController')->only(array(
        0 => 'index',
    ));
});

// CONFIGURACION-CUENTA
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('configuracion-cuenta', 'ConfiguracionCuenta\ConfiguracionCuentaController')->only(array(
        0 => 'index',
    ));
});

// ASIENTOS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('asientos', 'Asientos\AsientosController')->only(array(
        0 => 'index',
    ));
    Route::get('/asientos/comprobante/{id}', 'Asientos\AsientosController@comprobante');
});

// LIBRO-MAYOR
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('libro-mayor', 'LibroMayor\LibroMayorController')->only(array(
        0 => 'index',
    ));
});

// REPORTES-RESULTADOS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('reportes-resultados', 'ReportesResultados\ReportesResultadosController')->only(array(
        0 => 'index',
    ));
});

// BALANCE-GENERAL
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('balance-general', 'BalanceGeneral\BalanceGeneralController')->only(array(
        0 => 'index',
    ));
});

// PROFORMA-HEADER
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('proforma-header', 'ProformaHeader\ProformaHeaderController')->only(array(
        0 => 'index',
    ));
});

// CLIENTES
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::get('clientes/{id}', 'Clientes\ClientesController@index');
    Route::get('/customer/epson/{cuenta}/{cliente}/{id}', 'Customer\CustomerController@epson')->name('customer.epson');
});


// CREDITOS LIVEWERE
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::get('creditos/{id}', 'Creditos\CreditosController@creditos');
    Route::get("/creditos/pdfLetras/{valor}/{cuota}/{fecha}/{tipo}/{prestamo}/{genera}/{customer}/{codigo}", 'Creditos\CreditosController@pdfLetras')->name('credito.pdfLetras');
});

// ESTADO-CIVIL
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('estado-civil', 'EstadoCivil\EstadoCivilController')->only(array(
        0 => 'index',
    ));
});

// CLASIFICACION FORMAS-PAGO
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('clasificacion-formas-pago', 'ClasificacionFormasPago\ClasificacionFormasPagoController')->only(array(
        0 => 'index',
    ));
});
// FORMAS-PAGO
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('formas-pago', 'FormasPago\FormasPagoController')->only(array(
        0 => 'index',
    ));
});

// GASTOS-GENERADOS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('gastos-generados', 'GastosGenerados\GastosGeneradosController')->only(array(
        0 => 'index',
    ));
});

// AJUSTES
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('ajustes', 'Ajustes\AjustesController')->only(array(
        0 => 'index',
    ));
});

//TIPO-TRANSACIONES
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('type-transactions', 'TypeTransactions\TypeTransactionsController')->only(array(
        0 => 'index',
    ));
});

// BANCOS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('bancos', 'Bancos\BancosController')->only(array(
        0 => 'index',
    ));
});
// BOVEDAS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('bovedas', 'Bovedas\BovedasController')->only(array(
        0 => 'index',
    ));
});

// OPERACIONES DESCARGO BOVEDAS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('operaciones-descargo-bovedas', 'OperacionesDescargoBovedas\OperacionesDescargoBovedasController')->only(array(
        0 => 'index',
    ));
});

// DESCARGO BOVEDAS HEADER
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('descargo-bovedas-header', 'DescargoBovedasHeader\DescargoBovedasHeaderController')->only(array(
        0 => 'index',
    ));
});

// CALCULADORA PLAZO FIJO
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('calculadora-plazo-fijo', 'CalculadoraPlazoFijo\CalculadoraPlazoFijoController')->only(array(
        0 => 'index',
    ));
});

// USER BOVEDAS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('user-bovedas', 'UserBovedas\UserBovedasController')->only(array(
        0 => 'index',
    ));
});

// CUSTOMER TIPO AHORROS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('customer-tipo-ahorros', 'CustomerTipoAhorros\CustomerTipoAhorrosController')->only(array(
        0 => 'index',
    ));
});

// DESCARGO BOVEDA HISTORIAL
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('descargo-bovedas-historial', 'DescargoBovedasHistorial\DescargoBovedasHistorialController')->only(array(
        0 => 'index',
    ));
});

// DEBITO AUTOMATICO
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('debito-automatico', 'DebitoAutomatico\DebitoAutomaticoController')->only(array(
        0 => 'index',
    ));
});

// CUSTOMER MOVIMIENTO SOLICITUD
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('customer-movimiento-solicitud', 'CustomerMovimientoSolicitud\CustomerMovimientoSolicitudController')->only(array(
        0 => 'index',
    ));
});

// SOLICITUD PAGOS CREDITOS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::get(
        'solicitud-pagos-creditos/comprobante/{id}',
        'SolicitudPagosCreditos\SolicitudPagosCreditosController@comprobante'
    )->name('solicitud-pagos-creditos.comprobante');
    Route::resource('solicitud-pagos-creditos', 'SolicitudPagosCreditos\SolicitudPagosCreditosController')->only(array(
        0 => 'index',
    ));
});

// SOLICITUD PAGOS LIQUIDACION
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('solicitud-pagos-liquidacion', 'SolicitudPagosLiquidacion\SolicitudPagosLiquidacionController')->only(array(
        0 => 'index',
    ));
});

// CUSTOMER MOVMIENTO APROBACION
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('customer-movimiento-aprobacion', 'CustomerMovimientoAprobacion\CustomerMovimientoAprobacionController')->only(array(
        0 => 'index',
    ));
});
// CUSTOMER MOVMIENTO APROBACION
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('customer-movimiento-historial', 'CustomerMovimientoHistorial\CustomerMovimientoHistorialController')->only(array(
        0 => 'index',
    ));
});

// PERFIL
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('perfil', 'Perfil\PerfilController')->only(array(
        0 => 'index',
    ));
});

//TIPO CONCEPTOS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('tipo-concepto', 'TipoConcepto\TipoConceptoController')->only(array(
        0 => 'index',
    ));
});

//CONCEPTOS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('conceptos', 'Conceptos\ConceptosController')->only(array(
        0 => 'index',
    ));
});

//MODULOS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('modulos', 'Modulos\ModulosController')->only(array(
        0 => 'index',
    ));
});

//SEDES
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('sedes', 'Sedes\SedesController')->only(array(
        0 => 'index',
    ));
});

//CENTROS DE COSTOS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('centro-costos', 'CentroCostos\CentroCostosController')->only(array(
        0 => 'index',
    ));
});

//SEDE CENTRO DE COSTOS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('sedes-centro-costos', 'SedesCentroCostos\SedesCentroCostosController')->only(array(
        0 => 'index',
    ));
});

//OTROS VALORES QUE INGRESAN
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('otros-ingresos', 'OtrosIngresos\OtrosIngresosController')->only(array(
        0 => 'index',
    ));
});

//TODOS MODULOS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('todos-modulos', 'TodosModulos\TodosModulosController')->only(array(
        0 => 'index',
    ));
});

//OPERACION
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('operacion', 'Operacion\OperacionController')->only(array(
        0 => 'index',
    ));
});

//MOVIMEINTOS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('movimientos-diarios', 'MovimientosDiarios\MovimientosDiariosController')->only(array(
        0 => 'index',
    ));
});

//CARGAS INICIALES
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('carga-inicial', 'CargaInicial\CargaInicialController')->only(array(
        0 => 'index',
    ));
});

//LOG REVERSO MOVIMIENTOS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('log-reversos', 'LogReversoMovimientos\LogReversoMovimientosController')->only(array(
        0 => 'index',
    ));
});

//TERMINOS DE USO
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('terminos-uso', 'TerminosUso\TerminosUsoController')->only(array(
        0 => 'index',
    ));
});

//PROVEEDORES
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('proveedores', 'Proveedores\ProveedoresController')->only(array(
        0 => 'index',
    ));
});

//GASTO
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('gasto', 'Gasto\GastoController')->only(array(
        0 => 'index',
    ));
});

//GASTOS-CATEGORIAS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('gastos-categorias', 'GastosCategorias\GastosCategoriasController')->only(array(
        0 => 'index',
    ));
});

//REPORTE CUENTAS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('reporte-cuentas', 'ReporteCuentas\ReporteCuentasController')->only(array(
        0 => 'index',
    ));
});

//REPORTE CUENTAS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('reportes-niveles', 'ReporteNiveles\ReporteNivelesController')->only(array(
        0 => 'index',
    ));
});

//REPORTE CREDITOS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('reportes-creditos', 'ReporteCreditos\ResporteCreditosController')->only(array(
        0 => 'index',
    ));
});

//CREDITOS AUTOMATICOS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('creditos-automaticos', 'CreditosAutomaticos\CreditosAutomaticosController')->only(array(
        0 => 'index',
    ));
});

//CALCULO VALORES Y APORTES
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('valores-aportes', 'ValoresAportes\ValoresAportesController')->only(array(
        0 => 'index',
    ));
});

//NIVEL ACADEMIVO
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('nivel-academico', 'NivelAcademico\NivelAcademicoController')->only(array(
        0 => 'index',
    ));
});

//USUARIOS NUEVO
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('user-new', 'UserNew\UserNewController')->only(array(
        0 => 'index',
        1 => 'show',
    ));
});

Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('prestamos', 'Prestamos\PrestamosController')->only(['index', 'destroy']);
});

//TIPO DE RETENCIONES*
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('tipo-retencion', 'TipoRetencion\TipoRetencionController')->only(array(
        0 => 'index',
    ));
});

//LISTA DE RETENCIONES*
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('lista-retencion', 'ListaRetencion\ListaRetencionController')->only(array(
        0 => 'index',
    ));
});

//TIPO COMPROBANTES*
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('tipo-comprobante', 'TipoComprobante\TipoComprobanteController')->only(array(
        0 => 'index',
    ));
});

//SUSTENTOS TRIBUTARIOS*
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('sustento-tributario', 'SustentoTributario\SustentoTributarioController')->only(array(
        0 => 'index',
    ));
});

//IMPUESTOS*
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('impuestos', 'Impuestos\ImpuestosController')->only(array(
        0 => 'index',
    ));
});

//interes-fijo-parametrizado
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('interes-fijo-parametrizado', 'InteresFijoParametrizado\InteresFijoParametrizadoController')->only(array(
        0 => 'index',
    ));
});

//reporte-gastos-administrativos
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('reporte-gastos-administrativos', 'ReporteGastosAdministrativos\ReporteGastosAdministrativosController')->only(array(
        0 => 'index',
    ));
});

//reporte-cartera
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('reporte-cartera', 'ReporteCartera\ReporteCarteraController')->only(array(
        0 => 'index',
    ));
});

//reporte-cartera-niveles
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('reporte-niveles-cartera', 'ReporteCarteraNiveles\ReporteCarteraNivelesController')->only(array(
        0 => 'index',
    ));
});

//CARGA MOVIMIENTOS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('carga-movimientos', 'CargaMovimientos\CargaMovimientosController')->only(array(
        0 => 'index',
    ));
});

//SOLICITUD ENCAJE
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('solicitud-encaje', 'SolicitudEncaje\SolicitudEncajeController')->only(array(
        0 => 'index',
    ));
});
//SOLICITUD ENCAJE
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('cambio-datos-movimientos', 'CambioDatosMovimientos\CambioDatosMovimientosController')->only(array(
        0 => 'index',
    ));
});
//DOCUMENTOS PARAMETRIZADOS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('documentos-parametrizados', 'DocumentosParametrizados\DocumentosParametrizadosController')->only(array(
        0 => 'index',
    ));
    Route::get('formatoCreado/{id}/{doc}', 'DocumentosParametrizados\DocumentosParametrizadosController@formatoCreado')->name('formatoCreado.ver');
});



/*
//MODULO DE PRESTAMOS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::post('prestamos/store', 'Prestamos\PrestamosController@store')->name('prestamos.store');
    Route::resource('prestamos', 'Prestamos\PrestamosController')->only(['index', 'destroy']);
});
*/

//fin livewire


//INGRESOS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::get('/customer/buscar/{dato}', 'Customer\CustomerController@buscarCustomerCaja')->name('customer.buscarCustomerCaja');
    Route::get('/customer/seleccionar/{id}', 'Customer\CustomerController@seleccionarCustomer')->name('customer.seleccionarCustomer');
    Route::resource('ingresos', 'Ingresos\IngresosController')->only(array(
        0 => 'index',
        1 => 'create',
        2 => 'store',
        3 => 'destroy',
    ));
});

//RETIROS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::get('/customer/buscar/{dato}', 'Customer\CustomerController@buscarCustomerCaja')->name('customer.buscarCustomerCaja');

    Route::get('/historial/movimientos/{ruc}', 'Ingresos\IngresosController@verHistorial')->name('ingresos.verHistorial');
    Route::get('/historial/transacciones/movimientos/{id}', 'Customer\CustomerHistorialController@transaccionesMovimientos')->name('transacciones.transaccionesMovimientos');
    Route::resource('retiros', 'Retiros\RetirosController')->only(array(
        0 => 'index',
        1 => 'create',
        2 => 'store',
        3 => 'destroy',
    ));
});

//GRAFICOS HOME
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {

    Route::get('/home/datos', 'OnixHomeController@datosMeses')->name('home.datosMeses');
    Route::get('/home/credit', 'OnixHomeController@datosCreditos')->name('home.datosCreditos');
});

//FONDO
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::post('fondo/transacciones', 'Fondo\FondoController@storeTransacciones')->name('fondo.storeTransacciones');
    Route::get('fondo/ver/{id}', 'Fondo\FondoController@verMovimientos')->name('fondo.verMovimientos');
    Route::resource('fondo', 'Fondo\FondoController')->only(array(
        0 => 'index',
    ));
});
//SIMULADOR
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::post('simulador/transacciones', 'Simulador\SimuladorController@simular')->name('simulador.simular');
    Route::get('simulador/letras/{valor}/{anual}/{mensual}/{id}', 'Simulador\SimuladorController@pdfLetras')->name('simulador.pdfLetras');
    Route::resource('simulador', 'Simulador\SimuladorController')->only(array(
        0 => 'index',
    ));
});
//CREDITO
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::get('credit/verPdfView/{id}', 'Credit\CreditController@verPdfView')->name('credito.verPdfView');
    Route::post('credit/destroyArchivo/{id}', 'Credit\CreditController@destroyArchivo')->name('credito.destroyArchivo');
    Route::post('credit/guardarArchivos/{id}', 'Credit\CreditController@guardarArchivos')->name('credito.guardarArchivos');
    Route::get('credit/showFiles/{id}', 'Credit\CreditController@showFiles')->name('credito.showFiles');
    Route::post('credit/creditoIncobrableFinal/{id}', 'Credit\CreditController@creditoIncobrableFinal')->name('credito.creditoIncobrableFinal');
    Route::get('credit/incobrableDetalle/{id}', 'Credit\CreditController@incobrableDetalle')->name('credito.incobrableDetalle');
    Route::post('credit/liquitarCreditoFinal/{id}', 'Credit\CreditController@liquitarCreditoFinal')->name('credito.liquitarCreditoFinal');
    Route::get('credit/liquidarDetalle/{id}', 'Credit\CreditController@liquidarDetalle')->name('credito.liquidarDetalle');
    Route::post('credit/pagarLetra/{id}/{customer}', 'Credit\CreditController@pagarLetra')->name('credito.pagarLetra');
    Route::get('credit/verCuota/{id}', 'Credit\CreditController@verCuota')->name('credito.verCuota');
    Route::get('credit/verPagos/{id}', 'Credit\CreditController@verPagos')->name('credito.verPagos');
    Route::get('credit/prestamos/{id}', 'Credit\CreditController@prestamos')->name('credito.prestamos');
    Route::post('credit/creditos', 'Credit\CreditController@generarCredito')->name('credito.generarCredito');
    Route::post('customer/new', 'Customer\CustomerController@generarCustomerNew')->name('customerNew.generarCustomerNew');
    Route::get('credit/print/{id}', 'Credit\CreditController@printDetalle')->name('credito.printDetalle');
    Route::get('credit/printDetalleCredito/{id}', 'Credit\CreditController@printDetalleCredito')->name('credito.printDetalleCredito');
    Route::get('credit/print/letra/{id}', 'Credit\CreditController@printLetra')->name('credito.printLetra');
    Route::get('credit/print/pagare/{id}', 'Credit\CreditController@printPagare')->name('credito.printPagare');
    Route::get('credit/aprobarCredito/{id}/{fecha}', 'Credit\CreditController@aprobarCredito')->name('credito.aprobarCredito');
    Route::get('credit/entregarDinero/{id}/{valor}/{fecha}', 'Credit\CreditController@entregarDinero')->name('credito.entregarDinero');
    Route::get('credit/negarCredito/{id}', 'Credit\CreditController@negarCredito')->name('credito.negarCredito');
    Route::get('credit/pdfCuota/{id}', 'Credit\CreditController@pdfCuota')->name('credito.pdfCuota');
    Route::get('credit/pdfCuotaVer/{detalle}/{customer}', 'Credit\CreditController@pdfCuotaVer')->name('credito.pdfCuotaVer');
    Route::get('credit/pdfNotificaciones/{detalle}/{customer}', 'Credit\CreditController@pdfNotificaciones')->name('credito.pdfNotificaciones');
    Route::get('credit/pdfEncaje/{id}', 'Credit\CreditController@pdfEncaje')->name('credito.pdfEncaje');
    Route::get('credit/pdfVerEncaje/{cabecera}', 'Credit\CreditController@pdfVerEncaje')->name('credito.pdfVerEncaje');
    Route::post('credit/creditos/edit', 'Credit\CreditController@editarCredito')->name('credito.editarCredito');
    Route::get('/credit/verCredito/{id}', 'Credit\CreditController@verCredito')->name('credito.verCredito');
    Route::get('/credit/fondos/{id}', 'Credit\CreditController@verFondos')->name('credito.verFondos');
    Route::get('/credit/download/creditos/{desde}/{hasta}', 'Credit\CreditController@descargarEcxelCreditos')->name('credito.descargarEcxelCreditos');
    Route::get('/credit/download/vencidos/{desde}/{hasta}', 'Credit\CreditController@descargarEcxelCreditosVencidos')->name('credito.descargarEcxelCreditosVencidos');
    Route::get('/credit/download/pendientes/{desde}/{hasta}', 'Credit\CreditController@descargarEcxelCreditosPendientes')->name('credito.descargarEcxelCreditosPendientes');
    Route::get('/credit/numero/{code}', 'Credit\CreditController@editFolder')->name('credito.editFolder');
    Route::post('/credit/creditos/edit/cabecera/{id}/{carpeta}', 'Credit\CreditController@editFolderCabecera')->name('credito.editFolderCabecera');
    Route::post('credit/createTipoPrestamo', 'Credit\CreditController@createTipoPrestamo')->name('credito.createTipoPrestamo');
    Route::post('credit/updateTipoPrestamo', 'Credit\CreditController@updateTipoPrestamo')->name('credito.updateTipoPrestamo');
    Route::get('credit/buscarPrestamo/{prestmo}', 'Credit\CreditController@buscarPrestamo')->name('credito.buscarPrestamo');
    Route::get('credit/consultarPrestamo/{prestmo}', 'Credit\CreditController@consultarPrestamo')->name('credito.consultarPrestamo');
    Route::resource('credit', 'Credit\CreditController')->only(array(
        0 => 'index',
        1 => 'create',
    ));
});
//ALERTAS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::get('/alertas/fechas', 'Alerts\AlertsController@consultarFechas')->name('alertas.consultarFechas');
    Route::get('/alertas/nuevas', 'Alerts\AlertsController@consultarNuevas')->name('alertas.consultarNuevas');
    Route::get('/alertas/nuevas/{id}', 'Alerts\AlertsController@quitarAlerta')->name('alertas.quitarAlerta');
    Route::get('/alertas/prestamos/{id}', 'Alerts\AlertsController@irPrestamos')->name('alertas.irPrestamos');
});
//COBRANZA
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::get('/cobranza/envioWhatsappWeb/{id}', 'Cobranza\CobranzaController@envioWhatsappWeb')->name('cobranza.envioWhatsappWeb');
    Route::get('/cobranza/reporteInteresMora/{inicio}/{fin}', 'Cobranza\CobranzaController@reporteInteresMora')->name('cobranza.reporteInteresMora');
    Route::get('/cobranza/listaLetras', 'Cobranza\CobranzaController@listaLetras')->name('cobranza.listaLetras');
    Route::get('/cobranza/listaLetrasMes', 'Cobranza\CobranzaController@listaLetrasMes')->name('cobranza.listaLetrasMes');
    Route::get('/cobranza/notificarLetra/{id}', 'Cobranza\CobranzaController@notificarLetra')->name('cobranza.notificarLetra');
    Route::get('/cobranza/notificarLetraMensajes/{id}', 'Cobranza\CobranzaController@notificarLetraMensajes')->name('cobranza.notificarLetraMensajes');
    Route::get('/cobranza/listaLetrasMesBuscar', 'Cobranza\CobranzaController@listaLetrasMesBuscar')->name('cobranza.listaLetrasMesBuscar');
    Route::resource('cobranza', 'Cobranza\CobranzaController')->only(array(
        0 => 'index',
    ));
});
//GASTOS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::get('gastos/tcket/{id}', 'Gastos\GatosController@pdfGastos')->name('gastos.pdfGastosAnterior');
    Route::get('gastos/ticket/{id}', 'Gastos\GatosController@pdfGastosNuevo')->name('gastos.pdfGastos');
    Route::resource('gastos', 'Gastos\GatosController')->only(array(
        0 => 'index',
        1 => 'store',
    ));
});
//CUSTOMER INTERES
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::get('interes/historia', 'Customer\CustomerInteresController@historia')->name('interes.historia');
    Route::get('interes/historia/descargar', 'Customer\CustomerInteresController@descargarHistoria')->name('interes.descargarHistoria');
    Route::get('/interes/verTabla/{id}', 'Customer\CustomerInteresController@verTabla')->name('interes.verTabla');
    Route::resource('interes', 'Customer\CustomerInteresController')->only(array(
        0 => 'index',
    ));
});
//CARGAR CREDITOS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::get('cargas/detalles', 'Cargas\CargasInicialesController@descargaDetalles')->name('cargas.descargaDetalles');
    Route::post('cargas/credito/{i_customer}/{date}/{valor}/{garante}/{carpeta}/{valor_encaje}/', 'Cargas\CargasInicialesController@cargarDetalles')->name('cargas.cargarDetalles');
    Route::resource('cargas', 'Cargas\CargasInicialesController')->only(array(
        0 => 'index',
    ));
});
//CARGAR CUSTOMER
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::get('custom/plantilla-movimientos', 'Cargas\CargasInicialesCustomer@descargaPlantillaMovimientos')->name('cargas.descargaPlantillaMovimientos');
    Route::get('custom/plantilla', 'Cargas\CargasInicialesCustomer@descargaPlantilla')->name('cargas.descargaPlantilla');

    Route::post('custom/cargar', 'Cargas\CargasInicialesCustomer@cargarCustomer')->name('customer.cargarCustomer');
    Route::resource('custom', 'Cargas\CargasInicialesCustomer')->only(array(
        0 => 'index',
    ));
});
//REPORTES CREDITOS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('reportesCreditos', 'Reportes\ReportesController')->only(['index']);
});
//REPORTES CREDITOS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {});

//MODULO DE RECURRENCIA PRESTAMOS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::post('recurrencia-prestamos/save', 'RecurrenciaPrestamos\RecurrenciaPrestamosController@save')->name('recu.save');
    Route::get('recurrencia-prestamos/editar/{id}', 'RecurrenciaPrestamos\RecurrenciaPrestamosController@editar')->name('recu.editar');
    Route::resource('recurrencia-prestamos', 'RecurrenciaPrestamos\RecurrenciaPrestamosController')->only(array(
        0 => 'index',
    ));
});
//MODULO DE RECURRENCIA DE CARTERA
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::post('recurrenciaCartera/store', 'RecurrenciaCartera\RecurrenciaCarteraController@store')->name('recurrenciaCartera.storeAnterior');
    Route::get('recurrenciaCarteraVew', 'RecurrenciaCartera\RecurrenciaCarteraController@recurrenciaCarteraVew')->name('recurrenciaCartera.verDatos');
    Route::get('recurrenciaCartera/calcular', 'RecurrenciaCartera\RecurrenciaCarteraController@calcular')->name('recurrenciaCartera.calcular');
    Route::get('recurrenciaCartera/show/{heder}/{recu}', 'RecurrenciaCartera\RecurrenciaCarteraController@show')->name('recurrenciaCartera.show');
    Route::resource('recurrenciaCartera', 'RecurrenciaCartera\RecurrenciaCarteraController')->only(array(
        0 => 'index',
        2 => 'store',
        6 => 'destroy',
    ));
});
//RESULTADOS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::get('/resultados/generarResultados/{inicio}/{fin}', 'Resultados\ResultadosController@generarResultados')->name('resultados.generarResultados');
    Route::get('/resultados/generarPdfResultados/{inicio}/{fin}', 'Resultados\ResultadosController@generarPdfResultados')->name('resultados.generarPdfResultados');

    Route::resource('resultados', 'Resultados\ResultadosController')->only(['index']);
});
// REPORTE INGRESOS
Route::group(['middleware' => ['App\Http\Middleware\Authenticate', \App\Http\Middleware\CheckInactivity::class]], function () {
    Route::resource('reporte-ingresos', 'ReporteIngresos\ReporteIngresosController')->only(array(
        0 => 'index',
    ));
});

Route::post('product/saveCateLayapa', 'Product\ProductController@saveCateLayapa')->middleware('auth')->name('product.saveCateLayapa');

Route::get('/home', function () {
    return redirect()->route('onix.dashboard');
})->middleware('auth');
