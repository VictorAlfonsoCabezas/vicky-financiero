(function () {
    'use strict';
    function initialize() {
        const root = document.getElementById('document-template-editor');
        if (!root || root.dataset.editorReady || !window.Livewire) return;
        root.dataset.editorReady = 'true';
        const component = Livewire.find(root.getAttribute('wire:id'));
        const textarea = root.querySelector('textarea');
        let editor = null;
        if (typeof window.FroalaEditor === 'function') {
            editor = new FroalaEditor(textarea, {
                heightMin: 400,
                toolbarButtons: ['bold', 'italic', 'underline', 'fontSize', 'align', 'formatOL', 'formatUL', 'insertTable', 'undo', 'redo', 'html'],
                events: {
                    contentChanged: function () {
                        component.set('contenido', this.html.get(), true);
                    }
                }
            });
        }
        // Defer the current editor value into the same request that saves it.
        root.addEventListener('click', function (event) {
            if (!event.target.closest('[data-save-document]')) return;
            event.preventDefault();
            component.set('contenido', editor ? editor.html.get() : textarea.value, true);
            component.call('guardarFormato');
        });
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initialize);
    else initialize();
    document.addEventListener('livewire:load', initialize);
})();
