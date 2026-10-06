(() => {
    'use strict';
    let trigger;
    const ids = ['crear', 'carga', 'gasto', 'transferencia', 'detalle-carga'];
    window.addEventListener('bovedas-open', event => {
        if (!ids.includes(event.detail.tipo)) return;
        trigger = document.activeElement;
        const modal = document.getElementById(`vault-modal-${event.detail.tipo}`);
        bootstrap.Modal.getOrCreateInstance(modal).show();
    });
    window.addEventListener('closeModal', () => {
        ids.forEach(id => {
            const modal = document.getElementById(`vault-modal-${id}`);
            if (modal) bootstrap.Modal.getInstance(modal)?.hide();
        });
    });
    document.querySelectorAll('.vault-module .modal').forEach(modal => {
        modal.addEventListener('shown.bs.modal', () => (modal.querySelector('select, input, textarea') || modal.querySelector('button'))?.focus());
        modal.addEventListener('hidden.bs.modal', () => trigger?.focus());
    });
})();
