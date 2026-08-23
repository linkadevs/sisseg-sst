/* ------------------------------------------------------------------
   CONFIG
------------------------------------------------------------------ */
const API_URL = '../Controller/IncidenteApi.php';

/* ------------------------------------------------------------------
   PREVIEW DAS IMAGENS SELECIONADAS
------------------------------------------------------------------ */
document.getElementById('fieldFotos').addEventListener('change', function () {
  const preview = document.getElementById('uploadPreview');
  preview.innerHTML = '';

  Array.from(this.files).forEach(file => {
    if (!file.type.startsWith('image/')) return;
    const reader = new FileReader();
    reader.onload = e => {
      const img = document.createElement('img');
      img.src = e.target.result;
      img.alt = file.name;
      preview.appendChild(img);
    };
    reader.readAsDataURL(file);
  });
});

/* ------------------------------------------------------------------
   VALIDAÇÃO
------------------------------------------------------------------ */
const REQUIRED_FIELDS = [
  { id: 'fieldTipo',       errId: 'errTipo' },
  { id: 'fieldGravidade',  errId: 'errGravidade' },
  { id: 'fieldLocal',      errId: 'errLocal' },
  { id: 'fieldAtividade',  errId: 'errAtividade' },
  { id: 'fieldDescricao',  errId: 'errDescricao' },
  { id: 'fieldAcao',       errId: 'errAcao' },
];

function validateForm() {
  let valid = true;

  REQUIRED_FIELDS.forEach(({ id, errId }) => {
    const el  = document.getElementById(id);
    const err = document.getElementById(errId);
    const empty = !el.value.trim();

    el.classList.toggle('error', empty);
    err.classList.toggle('visible', empty);
    if (empty) valid = false;
  });

  // Data também é obrigatória (não está na lista acima porque é um campo <input type="date">)
  const dataEl  = document.getElementById('fieldData');
  const dataErr = document.getElementById('errData');
  const dataVazia = !dataEl.value;
  dataEl.classList.toggle('error', dataVazia);
  dataErr.classList.toggle('visible', dataVazia);
  if (dataVazia) valid = false;

  return valid;
}

/* Remove erro ao digitar / mudar */
[...REQUIRED_FIELDS, { id: 'fieldData', errId: 'errData' }].forEach(({ id, errId }) => {
  const el  = document.getElementById(id);
  const err = document.getElementById(errId);
  el.addEventListener('input',  () => { el.classList.remove('error'); err.classList.remove('visible'); });
  el.addEventListener('change', () => { el.classList.remove('error'); err.classList.remove('visible'); });
});

/* ------------------------------------------------------------------
   TOAST
------------------------------------------------------------------ */
function showToast(mensagem, sucesso = true) {
  const toast = document.getElementById('toast');
  toast.textContent = (sucesso ? '✓ ' : '✕ ') + mensagem;
  toast.classList.toggle('toast--erro', !sucesso);
  toast.classList.add('show');
  setTimeout(() => toast.classList.remove('show'), 2400);
}

/* ------------------------------------------------------------------
   SUBMIT — envia pro backend PHP (create)
------------------------------------------------------------------ */
const form = document.getElementById('incidentForm');
const btnSubmit = form.querySelector('.btn-submit');

form.addEventListener('submit', async function (e) {
  e.preventDefault();
  if (!validateForm()) return;

  const formData = new FormData(form);
  // status inicial é sempre "Aberto" — decidido no backend (Model\Incidente::createIncidente),
  // não é enviado pelo formulário.

  btnSubmit.disabled = true;
  btnSubmit.textContent = 'Registrando...';

  try {
    const resp = await fetch(`${API_URL}?action=create`, {
      method: 'POST',
      body: formData,
    });
    const resultado = await resp.json();

    if (!resultado.success) {
      showToast(resultado.message || 'Erro ao registrar incidente.', false);
      return;
    }

    showToast(resultado.message || 'Incidente registrado com sucesso!', true);
    setTimeout(() => {
      window.location.href = 'modulo-incidente.html';
    }, 1200);

  } catch (err) {
    console.error('Erro ao enviar incidente:', err);
    showToast('Não foi possível conectar ao servidor.', false);
  } finally {
    btnSubmit.disabled = false;
    btnSubmit.textContent = 'Registrar Incidente';
  }
});
