# Migración de Onix a Vicky Financiero

Se trasladaron las implementaciones del archivo `D:/proyectos/onix.zip`: modelos, controladores, 109 componentes Livewire en 104 carpetas, servicios, observadores de auditoría de créditos, importaciones, exportaciones, correos, vistas y plantillas Excel. El inventario inicial está en `onix-import-manifest.json`; los archivos de usuarios existentes se conservaron después de la integración.

## Acceso

- `/onix`: panel financiero; también aparece en la barra lateral.
- `/home`: redirige al panel financiero.
- Las pantallas conservan las rutas de Onix, por ejemplo `/tipo-ahorros`, `/cajas`, `/cartera`, `/plan-cuentas`, `/asientos`, `/balance-general`, `/customer-movimientos` y `/credit-folder-audits`.
- Los nuevos registros de menú se pueden preparar con `php artisan onix:menu --dry-run` y guardar con `php artisan onix:menu`. El comando es idempotente y no concede permisos por defecto. Después se asignan desde la administración de roles existente. También admite `--role=ID` para una asignación explícita.

No se ejecutó la sincronización sobre la base conectada ni se cambiaron sus roles. La inspección de migraciones fue de solo lectura; las migraciones financieras de Onix aparecen aplicadas. No se ejecutaron migraciones ni movimientos financieros.

## Módulos trasladados

- Socios, clientes, cuentas, tipos de ahorro, aportes, acciones, transferencias, depósitos, retiros, intereses y utilidades.
- Créditos, préstamos, simuladores, garantías, pagos, liquidaciones, solicitudes, auditoría, cartera y cobranza.
- Cajas, bóvedas, descargos, formas de pago, conciliación y movimientos diarios.
- Plan de cuentas, configuración contable, asientos, libro mayor, balances, resultados y reportes.
- Proveedores, gastos, proformas, impuestos, retenciones, centros de costos y sedes.
- Catálogos, documentos parametrizados, cargas iniciales, usuarios financieros y configuración de empresa.

Los comandos originales `command:cronSistema` y el de llenado de cartolas también se copiaron. No se activaron tareas programadas ni se ejecutaron estos procesos.

## Plantilla

Se usó Color Admin de `D:/proyectos/plantillabs5/admin/template/template_html/`, con sus archivos reales de `../assets`. El layout mantiene encabezado, barra lateral y contenido de Bootstrap 5, e incorpora estilos y scripts de Livewire.

Se adaptaron atributos de modales, pestañas, desplegables, espaciados, alineación, insignias y alertas. `public/js/onix-bs5.js` conecta los eventos de Onix con Bootstrap 5; `public/css/onix-bs5.css` adapta las clases del contenido original. El layout no carga AdminLTE ni un segundo runtime de Bootstrap. Los adaptadores DataTables llamados `bs4` son los que incluye la propia plantilla suministrada.

## Compatibilidad y límites del ZIP

Las rutas `resource` registran únicamente métodos implementados. Se excluyeron declaraciones antiguas sin implementación: `CantidadesInicialesController`, `IngresosController@seleccionarCustomer`, `HomeController@datosValores`, `CargasInicialesCustomer@descargaPlantillaNew` y `ResultadosController@guardarImagen`. El proyecto actual también declaraba `RolController@editRolModal` sin implementación. No se sustituyeron estas funciones por pantallas vacías.

La autenticación administrativa está en `/login` y el acceso de clientes se incorporó en `/login2`, con destino a `/mi-cuenta`; véase `DUAL-LOGIN.md`. No se trasladó la portada comercial. Los proveedores de correo y servicios externos siguen dependiendo de la configuración del proyecto actual. No se copiaron `.env`, credenciales de entorno ni archivos de clientes de `uploads`.

## Verificación

```sh
php vendor/bin/phpunit
php artisan view:cache
php scripts/audit-onix.php
node scripts/check-onix.cjs
node scripts/check-onix-views.cjs
node --check public/js/onix-bs5.js
```

Las pruebas cubren autenticación, resolución de rutas, clases Livewire, CRUD con validación en SQLite aislado, renderizado autenticado con Bootstrap 5 y sincronización idempotente de menú sin concesión de permisos. La revisión estática verifica sintaxis PHP, importaciones, vistas, componentes y recursos globales.

Estas comprobaciones no sustituyen una validación de los flujos financieros completos con datos de prueba ni una revisión visual en navegador. No se probaron envíos reales, importaciones financieras masivas ni contabilizaciones sobre la base conectada.
