document.addEventListener('DOMContentLoaded', () => {
    if (window.jQuery && jQuery.fn.DataTable) {
        const table = jQuery('#table_prestamos').DataTable({
            autoWidth: false, pageLength: 10, order: [[0, 'asc']],
            columnDefs: [{ targets: 6, orderable: false, searchable: false }],
            buttons: ['copy', 'excel', 'pdf', 'print'].map(extend => ({
                extend, text: {copy: 'Copiar', excel: 'Excel', pdf: 'PDF', print: 'Imprimir'}[extend],
                exportOptions: { columns: [0, 1, 2, 3, 4, 5] }
            })),
            language: { search: 'Buscar:', lengthMenu: 'Mostrar _MENU_', info: '_START_ a _END_ de _TOTAL_ recurrencias',
                infoEmpty: 'Sin recurrencias', infoFiltered: '(de _MAX_ en total)', emptyTable: 'Todavía no hay recurrencias. Crea el primer rango.',
                zeroRecords: 'No se encontraron coincidencias.', paginate: {previous: 'Anterior', next: 'Siguiente'} }
        });
        if (table.buttons) table.buttons().container().appendTo('#recurrence-exports');
    }
    document.querySelectorAll('[data-confirm-delete]').forEach(form => form.addEventListener('submit', event => {
        if (!confirm('¿Eliminar esta recurrencia de cartera?')) event.preventDefault();
    }));
    const form = document.getElementById('form_prestamo_new');
    const errors = document.getElementById('recurrence-errors');
    const button = document.getElementById('recurrence-save');
    document.getElementById('formRecurrenciaNew').addEventListener('show.bs.modal', () => {
        form.reset(); errors.classList.add('d-none'); errors.textContent = '';
    });
    form.addEventListener('submit', async event => {
        event.preventDefault();
        button.disabled = true;
        errors.classList.add('d-none');
        try {
            const response = await fetch(form.action, {method: 'POST', body: new FormData(form), headers: {Accept: 'application/json'}});
            if (!response.ok) {
                const data = await response.json().catch(() => ({}));
                throw new Error(data.errors ? Object.values(data.errors).flat().join(' ') : 'No se pudo guardar. Revisa tu sesión e inténtalo nuevamente.');
            }
            window.location.reload();
        } catch (error) {
            errors.textContent = error.message;
            errors.classList.remove('d-none'); errors.focus();
        } finally { button.disabled = false; }
    });
});
