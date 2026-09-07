# Acceso administrativo y Caja Web

- `/login`: formulario administrativo, basado en Login v2 de Color Admin Bootstrap 5.
- `/login2`: formulario para clientes, basado en Login v3 de la misma plantilla. Envía las credenciales a su propio endpoint `POST /login2`.
- `/mi-cuenta`: área del cliente con sus cuentas de ahorro y créditos, filtrados por cliente y empresa, con paginación.
- `POST /logout2`: cierra la sesión de cliente y vuelve a `/login2`.

Ambos accesos usan los usuarios y contraseñas existentes, con protección CSRF, límite de intentos y verificación de usuario y rol activos. El acceso de clientes requiere un registro activo en `customer` cuyo `user_id` y `company_id` coincidan con el usuario. No se crearon usuarios ni se modificaron sus relaciones o permisos en la base conectada.

Un cliente que utilice `/login` igualmente será enviado a su área. El middleware comprueba la relación en cada solicitud y también en sesiones recordadas; bloquea las rutas administrativas y sus mensajes Livewire. La sesión compartida permite un usuario activo por navegador, como en Onix. El cierre y la caducidad de la sesión de cliente regresan a `/login2`.

La Caja Web incorporada aquí ofrece consulta de cuentas y créditos. Las operaciones de autoservicio de los componentes antiguos (transferencias, solicitudes de pago, etc.) requieren integración y revisión de autorización antes de habilitarse en este acceso.

Verificación: `php vendor/bin/phpunit --filter DualLoginTest`. Incluye pantallas, inicio administrativo, ingreso del cliente, rechazo de usuarios sin asociación, separación de áreas, bloqueo de endpoints Livewire administrativos, caducidad, cierre y filtrado de datos propios.
