(function () {
    'use strict';
    const form = document.getElementById('customer-search-form');
    if (!form) return;
    const input = document.getElementById('customer-search-input');
    const modalElement = document.getElementById('customer-search-modal');
    const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
    const status = document.getElementById('customer-search-status');
    const results = document.getElementById('customer-search-results');
    const rows = document.getElementById('customer-search-rows');
    const pagination = document.getElementById('customer-search-pagination');
    const previous = document.getElementById('customer-search-prev');
    const next = document.getElementById('customer-search-next');
    let query = '', page = 1, pending = null;

    async function search(targetPage) {
        if (pending) pending.abort();
        const controller = new AbortController();
        pending = controller;
        results.hidden = true;
        pagination.hidden = true;
        pagination.classList.add('d-none');
        rows.replaceChildren();
        status.className = 'mb-3 text-muted';
        status.textContent = 'Buscando clientes…';
        modal.show();
        try {
            const url = new URL(form.action, window.location.href);
            url.searchParams.set('q', query);
            url.searchParams.set('page', targetPage);
            const response = await fetch(url, {signal: controller.signal, headers: {'Accept': 'application/json'}, credentials: 'same-origin'});
            if (response.status === 401 || response.redirected) throw new Error('La sesión ha expirado. Inicia sesión nuevamente.');
            if (response.status === 422) throw new Error('Escribe entre 2 y 120 caracteres para buscar.');
            if (!response.ok) throw new Error('No se pudo realizar la búsqueda. Inténtalo nuevamente.');
            const data = await response.json();
            if (pending !== controller) return;
            page = data.page;
            status.textContent = data.total ? `${data.total} cliente(s) encontrados para “${query}”.` : `No se encontraron clientes para “${query}”.`;
            data.customers.forEach(customer => {
                const row = document.createElement('tr');
                [customer.code, customer.name, customer.document].forEach(value => {
                    const cell = document.createElement('td');
                    cell.textContent = value || '—';
                    row.appendChild(cell);
                });
                const actions = document.createElement('td');
                const links = document.createElement('div');
                links.className = 'd-flex flex-wrap gap-2';
                [['Créditos', customer.credit_url], ['Ahorros', customer.savings_url], ['Cliente', customer.customer_url]].forEach(([label, href]) => {
                    const link = document.createElement('a');
                    link.className = 'btn btn-outline-primary btn-sm';
                    link.href = href;
                    link.textContent = label;
                    link.setAttribute('aria-label', `${label} de ${customer.name}`);
                    links.appendChild(link);
                });
                actions.appendChild(links); row.appendChild(actions); rows.appendChild(row);
            });
            results.hidden = data.customers.length === 0;
            pagination.hidden = data.last_page <= 1;
            pagination.classList.toggle('d-none', data.last_page <= 1);
            previous.disabled = page <= 1;
            next.disabled = page >= data.last_page;
            document.getElementById('customer-search-page').textContent = `Página ${page} de ${data.last_page}`;
        } catch (error) {
            if (error.name === 'AbortError' || pending !== controller) return;
            status.className = 'mb-3 text-danger';
            status.textContent = error instanceof TypeError ? 'No se pudo conectar. Revisa tu conexión e inténtalo nuevamente.' : error.message;
        } finally {
            if (pending === controller) pending = null;
        }
    }
    form.addEventListener('submit', event => {
        event.preventDefault();
        query = input.value.trim();
        if (query.length < 2) {
            input.setCustomValidity('Escribe al menos 2 caracteres.'); input.reportValidity(); return;
        }
        search(1);
    });
    input.addEventListener('input', () => input.setCustomValidity(''));
    previous.addEventListener('click', () => search(page - 1));
    next.addEventListener('click', () => search(page + 1));
    modalElement.addEventListener('hidden.bs.modal', () => {
        if (pending) { pending.abort(); pending = null; }
        input.focus();
    });
})();
