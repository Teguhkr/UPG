/**
 * Inisialisasi editor WYSIWYG Quill untuk form admin (Artikel, FAQ).
 *
 * Markup:
 *   <div data-quill-wrapper>
 *     <div data-quill-editor style="min-height: 300px"></div>
 *     <textarea name="content" class="d-none" data-quill-input>...HTML...</textarea>
 *   </div>
 */
import Quill from 'quill/dist/quill';

const TOOLBAR = [
  [{ header: [2, 3, 4, false] }],
  ['bold', 'italic', 'underline', 'strike'],
  [{ color: [] }, { background: [] }],
  [{ list: 'ordered' }, { list: 'bullet' }, { indent: '-1' }, { indent: '+1' }],
  [{ align: [] }],
  ['blockquote', 'link', 'image'],
  ['clean']
];

document.querySelectorAll('[data-quill-wrapper]').forEach(wrapper => {
  const editorEl = wrapper.querySelector('[data-quill-editor]');
  const input = wrapper.querySelector('[data-quill-input]');
  if (!editorEl || !input) return;

  const quill = new Quill(editorEl, {
    theme: 'snow',
    placeholder: wrapper.dataset.placeholder || 'Tulis konten di sini...',
    modules: { toolbar: TOOLBAR }
  });

  if (input.value.trim()) {
    quill.clipboard.dangerouslyPasteHTML(input.value);
  }

  const sync = () => {
    input.value = quill.getText().trim() === '' && !quill.root.querySelector('img') ? '' : quill.root.innerHTML;
  };
  quill.on('text-change', sync);

  const form = wrapper.closest('form');
  if (form) {
    form.addEventListener('submit', e => {
      sync();
      if (input.hasAttribute('data-quill-required') && input.value === '') {
        e.preventDefault();
        quill.focus();
        alert('Isi konten wajib diisi.');
      }
    });
  }
});
