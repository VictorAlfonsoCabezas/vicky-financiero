# Módulo de empresas

La interfaz de `/company`, creación y edición utiliza los paneles, controles, interruptores y modales de Color Admin Bootstrap 5 de `D:/proyectos/plantillabs5/admin/template/template_html/`.

- Directorio paginado con búsqueda por razón social, nombre comercial, RUC y ciudad; filtros de empresas activas/inactivas y contadores.
- Un formulario compartido agrupa los campos anteriores en identidad/contacto, créditos/cobranza, contabilidad/operación y documentos/presentación. Incluye navegación por secciones, vista previa del logo, barra de guardado y advertencia de cambios pendientes.
- `CompanyForm` define los campos y `SaveCompanyRequest` valida y transforma únicamente esos valores. El controlador conserva las propiedades ajenas al formulario. Se corrigieron los indicadores de conexión y facturación electrónica que estaban intercambiados en la creación.
- Las imágenes se validan como JPG/PNG/WebP de hasta 2 MB, con un nombre aleatorio. El archivo anterior se conserva, para no romper referencias existentes; si falla el guardado se elimina únicamente la nueva carga.
- Activación/desactivación mediante acciones Livewire y modal de confirmación, sin eliminación física. No se permite desactivar la empresa actual. Se conserva el acceso administrativo existente y el bloqueo de clientes a este módulo.
- Se retiró el registro duplicado de rutas de empresas. El grupo vigente incluye autenticación y caducidad de sesión.

Verificación: `php vendor/bin/phpunit --filter CompanyModuleTest`, suite completa, compilación Blade, sintaxis JavaScript y renderizado de lectura sobre la base actual. Las pruebas de guardado utilizan SQLite en memoria y una carpeta de imágenes aislada. No se modificaron empresas de la base conectada ni se ejecutaron migraciones.

## Integración Livewire

`CompanyIndex` gestiona búsqueda con debounce, filtros en la URL, paginación y confirmación de estado. `CompanyEditor` gestiona datos, campos dependientes, validación, carga temporal del logo y guardado sin navegación. Después de crear, el componente conserva la identidad del registro para que los siguientes guardados actualicen esa misma empresa.

Ambos componentes comprueban el acceso administrativo y la caducidad en sus solicitudes. El identificador del editor viaja cifrado. `CompanyWriter` comparte la persistencia con los endpoints HTTP anteriores, que se conservan por compatibilidad; `SaveCompanyRequest` sigue siendo la fuente de reglas y transformaciones. JavaScript se limita al aviso de cambios sin guardar y al foco del diálogo, sin peticiones AJAX propias.
