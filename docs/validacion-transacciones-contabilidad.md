# Validacion de transacciones y contabilidad

Fecha: 13 de octubre de 2026.

## Comportamiento revisado

Se revisaron las llamadas de generacion y recalculo de asientos, los eventos de
CustomerMovimiento, Gastos y DescargoBovedasHeader, y los puntos de confirmacion
y reversion en pagos de cuotas, liquidaciones, proveedores, desembolsos,
intereses, utilidades, aprobaciones, transferencias y asientos manuales.

Los descuadres ya no eliminan el asiento ni provocan una reversion del pago.
Los problemas de configuracion contable devuelven una advertencia y conservan
un asiento vinculado al registro de origen. Cuando no es posible determinar
las cuentas, se conserva una cabecera pendiente; no se inventan contrapartidas.
Las lineas disponibles se conservan para completar la revision.

El motivo pendiente se guarda en la descripcion, con el prefijo
`[PENDIENTE CONTABLE]`, respetando el limite existente de 255 caracteres.
No se necesita una migracion. El recalculo satisfactorio restaura la descripcion
normal y reutiliza el asiento vinculado a la operacion.

Asientos, Libro Mayor y Todos los modulos muestran en rojo los descuadres y las
configuraciones pendientes, incluso cuando Debe y Haber son iguales o ambos son
cero. Las advertencias de Livewire llegan en la misma respuesta y permanecen
visibles hasta cerrarlas. Las redirecciones conservan la advertencia en sesion.
Las operaciones revertidas por otro error no generan advertencias de exito.

## Cambios adicionales encontrados

- Los asientos manuales permitian eliminar cabecera y detalles por descuadre.
  Ahora permiten guardar y corregir el asiento; la escritura se hace en una
  transaccion de base de datos.
- Desembolsos e intereses podian detenerse por cuentas o configuraciones
  faltantes. Ahora generan asientos pendientes con la explicacion.
- La aprobacion masiva de utilidades retornaba dentro de una transaccion abierta
  cuando faltaba configuracion. Ahora completa el lote y conserva los pendientes.
  Reutiliza el asiento creado por el evento del movimiento y calcula el total
  general sumando los valores reales del lote.
- El recalculo de un asiento general podia confundir su identificador con el de
  un movimiento. Ahora conserva el asiento general y deja pendiente la revision
  manual cuando no hay datos suficientes para reconstruirlo.
- Cuando la contabilidad no aplica a la fecha de una operacion, el resultado es
  una omision explicita, no un error que revierta pagos de proveedores.

## Evidencia

- Suite completa: `php vendor/bin/phpunit`.
- Resultado final: **184 pruebas aprobadas, 11814 aserciones**.
- Revision de sintaxis: **908 archivos PHP sin errores**; tambien se verificaron
  los archivos modificados despues de esa revision.
- Las cuatro vistas modificadas se compilaron con Blade y se validaron con
  `php -l`. Se verifico el escape de las advertencias iniciales en JavaScript.
- Se verifico la entrega de advertencias a traves del middleware web con una
  peticion HTTP que usa el formato de respuesta de Livewire.
- `git diff --check` sin errores.

Las pruebas nuevas cubren descuadres, falta de conceptos, configuracion,
detalles, codigo de transaccion, cuentas y valores; pagos reales de cuotas y
liquidaciones con contabilidad activada; utilidades sin configuracion; gastos;
intereses; bovedas; asientos manuales; recalculo sin duplicacion; aislamiento
entre empresas; redirecciones y ausencia de advertencias despues de una reversion.

## Limites de la validacion

Las pruebas se ejecutaron en bases SQLite aisladas. No se modificaron datos
reales ni se desplegaron cambios. Falta una comprobacion visual en navegador y
una prueba con las configuraciones y la base MySQL del entorno de uso.

Los errores de base de datos, registros inexistentes, saldo insuficiente,
pagos duplicados, permisos y otras validaciones operativas conservan sus
protecciones y pueden impedir o revertir una operacion. Las fechas habilitadas
y la activacion de contabilidad siguen determinando cuando corresponde un
asiento. Un asiento pendiente requiere revision antes de dar por correcta la
contabilidad; su presencia no garantiza que los reportes esten cuadrados.
