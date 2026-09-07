# Barrido y correcciones — 6 de septiembre de 2026

## Alcance

Revisión estática de PHP en `app`, JavaScript en `public/js`, vistas y componentes referenciados, recursos del layout, rutas, parámetros de controladores y clases de migraciones. Pruebas funcionales de regresión con SQLite en memoria y pruebas JavaScript del editor. No se ejecutaron movimientos financieros, envíos ni migraciones sobre la base configurada.

## Fallos corregidos

1. La novación de préstamos diarios terminaba en `dd("ssss")`: ahora llega al generador diario. La prueba sustituye la contabilización; no valida los cálculos financieros de la novación.
2. La desactivación de roles guardaba el cambio y luego interrumpía la respuesta: devuelve JSON y trata IDs inexistentes como 404.
3. El inicio de sesión admitía usuarios inactivos y roles desactivados. Ahora exige usuario activo y un rol activo; el rechazo vuelve a `/login`, en lugar de la ruta inexistente `/auth/login`.
4. Los comprobantes de encaje reemplazaban el año por 2024. Conservan la fecha real en español y obtienen el cliente del comprobante, con comprobación de empresa.
5. El catálogo de género permitía leer, editar, eliminar y cambiar estados mediante IDs de otra empresa. Se restringieron esas operaciones y el cambio de valor por defecto; también se validó la longitud real del nombre.
6. Los formatos de documentos se cargaban y sobrescribían por nombre sin comprobar empresa. Se corrigieron carga, guardado, edición por ID, listado y selección del formato de pagaré para PDF. El selector de variables vacío ya no provoca acceso a un objeto nulo.
7. El editor tardaba diez segundos en sincronizar y se destruía/recreaba tras cada respuesta Livewire. El contenido actual se envía junto con el guardado, sin recrear el editor. Hay un textarea funcional si no está disponible Froala y un enlace de edición en el listado.
8. El buscador antiguo de caja consultaba clientes de todas las empresas. Se restringieron sus cuatro variantes de búsqueda.
9. La recurrencia de cartera tenía rutas con nombres duplicados y un `show` sin el segundo parámetro requerido. Se corrigió el registro. Su detalle conserva la cabecera de cartera, limita registros por empresa y tolera créditos ausentes. Siete marcas horarias usaban mes en lugar de minutos; ahora usan `H:i:s`.
10. El reporte de resultados interpolaba fechas en SQL, mezclaba empresas y unía el ID de la cuenta directamente con el ID del tipo de ahorro. Utiliza parámetros, valida fechas, sigue la relación real cuenta → tipo de ahorro y excluye movimientos de ahorro anulados. La ausencia de tipos de transacción deja sumas vacías en vez de causar errores de objeto nulo.
11. Se diferenciaron los nombres de las rutas de comprobantes de gastos. Se retiraron 19 rutas que apuntaban a métodos vacíos; las pantallas y acciones implementadas permanecen registradas.
12. Se eliminaron las interrupciones de depuración ejecutables restantes y código de depuración inalcanzable. El comando de cartolas finaliza con código de éxito.

## Pendientes detectados

- **Instalación desde cero:** `2021_09_06_141824_create_country_table.php` y `2023_09_29_183552_create_country_table.php` declaran `CreateCountryTable` y crean `country` con columnas diferentes (`name/iso/code` frente a `nombre/codigo_pais/codigo_llamada`). Es un bloqueo de migración, no un error de sintaxis. No se renombraron archivos ni se alteró el historial aplicado: hay que conciliar ambos esquemas y el historial antes de ejecutar migraciones en una instalación nueva.
- **Edición antigua de cabecera de crédito:** `CreditController@editFolderCabecera` contenía únicamente un `dd` y no tiene implementación en el ZIP. Devuelve ahora un error HTTP 501 explícito. No se inventó una operación que modifique créditos.
- Este barrido no certifica todos los procesos financieros ni todos los controles de permisos de los 109 componentes. La cobertura entre empresas se añadió a los flujos indicados, no a todo el sistema. Falta revisión visual en navegador y validación integral de contabilizaciones, importaciones masivas y proveedores externos con datos de prueba.

## Verificación reproducible

```sh
php vendor/bin/phpunit
node --test tests/js/document-editor.test.cjs
php artisan view:cache
php scripts/audit-onix.php
php scripts/audit-project.php
node scripts/check-onix.cjs
node scripts/check-onix-views.cjs
```

Resultado: 24 pruebas PHP y 2 pruebas JavaScript aprobadas. No se detectaron rutas ocultadas por otras, nombres duplicados, parámetros faltantes, acciones vacías registradas ni `dd`/`dump` ejecutables. La revisión estática no detectó errores de sintaxis, importaciones locales faltantes ni recursos globales faltantes. El conflicto de migraciones queda visible en `audit-project.php`.
