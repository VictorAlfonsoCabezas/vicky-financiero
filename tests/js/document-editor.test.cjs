const test = require('node:test');
const assert = require('node:assert/strict');
const vm = require('node:vm');
const fs = require('node:fs');
const script = fs.readFileSync('public/js/document-editor.js', 'utf8');

function setup(richEditor) {
    const requests = [], events = {}, textarea = {value: 'Contenido inicial'};
    const component = {set: (...args) => requests.push(['set', ...args]), call: (...args) => requests.push(['call', ...args])};
    const root = {dataset: {}, getAttribute: () => 'component-1', querySelector: () => textarea, addEventListener: (name, fn) => { events[name] = fn; }};
    let creations = 0, currentHtml = 'Texto recién escrito';
    function FroalaEditor() { creations++; this.html = {get: () => currentHtml}; }
    const docEvents = {};
    const context = {document: {readyState: 'complete', getElementById: () => root, addEventListener: (name, fn) => { docEvents[name] = fn; }}, Livewire: {find: () => component}};
    context.window = context;
    if (richEditor) context.FroalaEditor = FroalaEditor;
    vm.runInNewContext(script, context);
    return {requests, textarea, docEvents, creations: () => creations, save: () => events.click({preventDefault() {}, target: {closest: () => true}})};
}

test('saving includes current editor content in the same request, without waiting for a debounce', () => {
    const editor = setup(true);
    editor.save();
    assert.deepEqual(editor.requests, [['set', 'contenido', 'Texto recién escrito', true], ['call', 'guardarFormato']]);
    editor.docEvents['livewire:load']();
    assert.equal(editor.creations(), 1);
});

test('the textarea can save even when the editor CDN is unavailable', () => {
    const editor = setup(false);
    editor.textarea.value = 'Texto de respaldo';
    editor.save();
    assert.deepEqual(editor.requests, [['set', 'contenido', 'Texto de respaldo', true], ['call', 'guardarFormato']]);
});
