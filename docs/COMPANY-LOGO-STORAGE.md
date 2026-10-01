# Conservación de logotipos

Los formularios `/company/{id}/edit`, `/mi-empresa` y el componente antiguo de empresas utilizan el mismo guardado. Seleccionar una imagen crea una vista previa: debe pulsarse **Guardar cambios** para conservarla.

Cada nueva carga se escribe con nombre UUID en `storage/app/company-logos` y en `public/uploads/companies`. Se verifica el contenido mediante SHA-256 antes de actualizar la referencia en la base de datos. Una carga fallida no cambia la referencia anterior. No hay eliminación automática de logos, tampoco al reemplazarlos o fallar una transacción; pueden quedar archivos sin referencia que se conservan deliberadamente.

Las pantallas usan `/company/{id}/logo`, que permite servir la copia conservada si no se puede restaurar la pública. Los reportes intentan restaurar el archivo público antes de leerlo. Las imágenes existentes se copian al archivo persistente cuando se consultan; también se reconoce la ubicación antigua `storage/app/public/uploads/companies`.

## Instalación en producción

1. Configurar `COMPANY_LOGOS_PATH` con una carpeta de un volumen persistente, fuera de las carpetas reemplazadas por el despliegue. Todas las instancias deben compartir ese volumen. El valor predeterminado solo es persistente si se conserva `storage`.
2. Permitir lectura y escritura al usuario de PHP. Actualizar la caché de configuración si se utiliza.
3. Ejecutar `php artisan company:preserve-logos` antes de retirar una instalación anterior. El comando no cambia registros ni borra archivos; devuelve error si no puede conservar algún logo.
4. Incluir la carpeta configurada y la base de datos en copias de seguridad externas, y comprobar su restauración.

Dos copias en el mismo servidor no protegen de la pérdida de todo el disco. Los archivos ya ausentes de ambas ubicaciones antiguas y del archivo persistente deben recuperarse de un respaldo o volver a cargarse. Esta implementación no configura ni despliega el volumen del alojamiento.
