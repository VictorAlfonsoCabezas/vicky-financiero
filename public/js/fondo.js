document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form_fondo_transacction');
    const errors = document.getElementById('fund-errors');
    const save = document.getElementById('fund-save');
    const modal = id => bootstrap.Modal.getOrCreateInstance(document.getElementById(id));
    if (window.jQuery && jQuery.fn.DataTable) {
        const table = jQuery('#table_fondos').DataTable({autoWidth:false, pageLength:10,
            columnDefs:[{targets:6, orderable:false, searchable:false}],
            buttons:['copy','excel','pdf','print'].map(extend => ({extend, text:{copy:'Copiar',excel:'Excel',pdf:'PDF',print:'Imprimir'}[extend], exportOptions:{columns:[0,1,2,3,4,5]}})),
            language:{search:'Buscar:', lengthMenu:'Mostrar _MENU_', info:'_START_ a _END_ de _TOTAL_ fondos', infoEmpty:'Sin fondos', infoFiltered:'(de _MAX_ fondos)', emptyTable:'No hay fondos activos.', zeroRecords:'Sin coincidencias.', paginate:{previous:'Anterior',next:'Siguiente'}}});
        if (table.buttons) table.buttons().container().appendTo('#fund-exports');
    }
    let request;
    document.addEventListener('click', async event => {
        const trigger = event.target.closest('[data-fund], [data-details]');
        if (!trigger) return;
        if (trigger.dataset.fund) {
            form.reset(); errors.classList.add('d-none');
            form.elements.fondo_id.value = trigger.dataset.fund;
            form.elements.type_fondo.value = trigger.dataset.type;
            document.getElementById('fund-title').textContent = trigger.dataset.type === 'IN' ? 'Registrar ingreso' : 'Registrar egreso';
            document.getElementById('fund-context').textContent = 'Fondo: ' + trigger.dataset.code;
            modal('frmModalFondo').show(); return;
        }
        if (request) request.abort();
        request = new AbortController();
        const status = document.getElementById('fund-details-status');
        const body = document.querySelector('#fondo_detalle_table tbody');
        body.replaceChildren(); status.textContent = 'Cargando movimientos…';
        document.getElementById('fund-details-title').textContent = 'Movimientos · ' + trigger.dataset.code;
        modal('frmModalFondoDetalles').show();
        try {
            const response = await fetch(trigger.dataset.details, {headers:{Accept:'application/json'}, signal:request.signal});
            if (!response.ok) throw new Error('No se pudieron cargar los movimientos.');
            const rows = await response.json();
            rows.forEach(item => {
                const row = document.createElement('tr');
                [item.type_transaction_name, Number(item.valor).toLocaleString('es-EC',{minimumFractionDigits:2}), item.user_created_name, item.observation_created, [item.date_created,item.hour_created].join(' ')].forEach(value => {
                    const cell = document.createElement('td'); cell.textContent = value ?? ''; row.appendChild(cell);
                }); body.appendChild(row);
            });
            status.textContent = rows.length ? rows.length + ' movimientos' : 'Este fondo todavía no tiene movimientos.';
        } catch(error) { if (error.name !== 'AbortError') status.textContent = error.message; }
    });
    form.addEventListener('submit', async event => {
        event.preventDefault();
        if (!confirm('¿Confirmas registrar este ' + (form.elements.type_fondo.value === 'IN' ? 'ingreso' : 'egreso') + ' por $ ' + form.elements.valor_fondo.value + '?')) return;
        save.disabled = true; errors.classList.add('d-none');
        try {
            const response = await fetch(form.action, {method:'POST',body:new FormData(form),headers:{Accept:'application/json'}});
            if (!response.ok) {
                const data = await response.json().catch(() => ({}));
                throw new Error(data.errors ? Object.values(data.errors).flat().join(' ') : 'No se pudo registrar el movimiento. Revisa tu sesión.');
            }
            location.reload();
        } catch(error) { errors.textContent = error.message; errors.classList.remove('d-none'); errors.focus(); }
        finally { save.disabled = false; }
    });
});
