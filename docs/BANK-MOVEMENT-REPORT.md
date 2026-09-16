# Reporte de movimientos para conciliación bancaria

La pantalla existente `/conciliacion` consulta `customer_movimientos` sin modificar movimientos, saldos ni asientos. Usa el layout Onix/Bootstrap 5 del proyecto y los paneles, tablas y formularios de `D:/proyectos/plantillabs5/admin/template/template_html/table_basic.html`.

## Acceso y alcance

El usuario necesita un rol activo con el menú `/conciliacion` asignado en Menú rol. Si hay un rol seleccionado en sesión, se comprueba ese rol. La autorización también se comprueba en las acciones Livewire y exportaciones. Los catálogos y movimientos se limitan siempre a la empresa autenticada; no se recibe una empresa desde el navegador.

Como en el inicio de sesión, se comprueba `rol.status`, no el estado de la tabla intermedia `usuario_rol`: las asignaciones históricas pueden tener ese campo vacío. Sigue siendo obligatorio que la relación usuario–rol exista y tenga el menú asignado.

Se considera identificado el banco cuando `banco_id` corresponde a un banco de la misma empresa con `tipo_cuenta_id > 0` y número de cuenta, siguiendo el catálogo usado para desembolsos. Se incluyen bancos desactivados para consultar su histórico. No se intenta interpretar un ID desconocido como una cuenta contable ni asignar un banco por coincidencia de nombre.

La opción sin banco/cuenta identificada incluye también movimientos internos y de efectivo: permite revisar registros, pero no significa que todos sean operaciones bancarias pendientes. El reporte no importa extractos ni marca movimientos conciliados.

El selector de banco ofrece Todos (opción inicial), Sin banco/cuenta identificada, Solo con banco/cuenta identificada y cada banco. Este único selector evita combinar un banco concreto con la condición sin banco. El filtro de forma de pago se combina con cualquiera de esas opciones, permitiendo consultar Efectivo sin exigir banco. Los totales bancarios conservan su alcance: excluyen movimientos sin banco identificado.

## Cálculo y consulta

- Fechas según `date_created`, orden por fecha, hora e ID.
- Entradas según la acción registrada `S`; salidas según `R`.
- Totales sobre todo el resultado filtrado, no solo las 25 filas de la página.
- Se excluyen de los importes bancarios los anulados, los movimientos sin banco identificado y las acciones distintas de `S`/`R`.
- El neto es entradas menos salidas; no se usa `saldo_general` ni se afirma que sea el saldo bancario.
- Los valores y la dirección se muestran tal como están registrados; el reporte no corrige inconsistencias históricas de captura.
- Consultar aplica los filtros. Excel y PDF aplican los filtros actuales antes de exportar.
- Excel contiene todas las filas filtradas y distingue valor original de entrada/salida computable. Conserva referencias como texto y evita interpretar fórmulas ingresadas en campos de texto.
- PDF incluye filtros y totales. Se limita a 2.000 movimientos para controlar el consumo de memoria; para períodos mayores se ofrece Excel, que procesa la consulta por bloques.

No requiere migraciones. La instalación debe tener aplicadas las migraciones existentes de banco, referencia, forma de pago y comprobante. Antes de usar los totales como evidencia de conciliación, corresponde cotejarlos con los extractos y verificar la calidad de los datos históricos.

## Validación

`php vendor/bin/phpunit --filter BankMovementReportTest`

Las pruebas usan SQLite en memoria y cubren aislamiento por empresa, permisos, fechas, referencias, movimientos sin banco, anulados, totales, paginación, Excel real y generación de PDF. No ejecutan movimientos sobre la base configurada.
