(() => {
    'use strict';
    let dirty = false;
    let returnFocus;
    document.addEventListener('input', event => {
        if (!event.target.closest('#mi-empresa-form')) return;
        dirty = true;
        const state = document.getElementById('mi-empresa-save-state');
        if (state) state.textContent = 'Tienes cambios sin guardar.';
    });
    window.addEventListener('beforeunload', event => {
        if (dirty) { event.preventDefault(); event.returnValue = ''; }
    });
    window.addEventListener('mi-empresa-saved', () => {
        dirty = false;
        const state = document.getElementById('mi-empresa-save-state');
        if (state) state.textContent = 'Todos los cambios están guardados.';
    });
    window.addEventListener('mi-empresa-status-open', () => {
        returnFocus = document.activeElement;
        requestAnimationFrame(() => document.querySelector('.mi-empresa-live-modal .btn-close')?.focus());
    });
    window.addEventListener('mi-empresa-status-close', () => returnFocus?.focus());
    document.addEventListener('keydown', event => {
        const modal = document.querySelector('.mi-empresa-live-modal');
        if (!modal || event.key !== 'Tab') return;
        const controls = [...modal.querySelectorAll('button:not([disabled]),input:not([disabled])')];
        const first = controls[0], last = controls[controls.length - 1];
        if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
        else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
    });
})();
