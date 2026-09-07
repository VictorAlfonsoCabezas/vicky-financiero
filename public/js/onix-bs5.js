/* Onix browser events adapted to Bootstrap 5 without loading Bootstrap 4/AdminLTE. */
(function () {
    'use strict';
    const $ = window.jQuery;
    if (!$ || !window.bootstrap) return;
    // Onix uses the jQuery modal API, including before DOMContentLoaded.
    $.fn.modal = function (action) {
        return this.each(function () {
            const instance = bootstrap.Modal.getOrCreateInstance(this, typeof action === 'object' ? action : {});
            if (typeof action === 'string' && typeof instance[action] === 'function') instance[action]();
        });
    };
    $.fn.Toasts = function (action, options) {
        if (action !== 'create') return this;
        options = options || {};
        let region = document.getElementById('onix-toasts');
        if (!region) {
            region = document.createElement('div');
            region.id = 'onix-toasts';
            region.className = 'toast-container position-fixed top-0 end-0 p-3';
            region.style.zIndex = '2001';
            region.setAttribute('aria-live', 'polite');
            document.body.appendChild(region);
        }
        const toast = document.createElement('div');
        const color = (options.class || '').match(/bg-(success|danger|warning|info|primary|secondary)/);
        toast.className = 'toast ' + (color ? color[0] : '');
        const header = document.createElement('div');
        header.className = 'toast-header';
        const title = document.createElement('strong');
        title.className = 'me-auto';
        title.textContent = options.title || 'Notificación';
        const close = document.createElement('button');
        close.type = 'button'; close.className = 'btn-close';
        close.setAttribute('data-bs-dismiss', 'toast'); close.setAttribute('aria-label', 'Cerrar');
        header.append(title, close);
        const body = document.createElement('div'); body.className = 'toast-body';
        body.textContent = options.body || '';
        toast.append(header, body); region.appendChild(toast);
        toast.addEventListener('hidden.bs.toast', () => toast.remove());
        bootstrap.Toast.getOrCreateInstance(toast, {autohide: options.autohide !== false, delay: options.delay || 6000}).show();
        return this;
    };
    $.ajaxSetup({headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''}});
})();
