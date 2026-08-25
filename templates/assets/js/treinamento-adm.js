// ===== Estado da aplicação =====
const API_URL = 'treinamento-api.php';
const IMG_PADRAO = 'https://images.unsplash.com/photo-1581094794329-c8112a89af12?w=500&h=310&fit=crop';

let treinamentos = [];  // cópia local dos dados vindos do banco
let editingId = null;   // null = criando novo treinamento

// ===== Elementos =====
const grid = document.getElementById('coursesGrid');
const searchInput = document.getElementById('searchInput');
const btnFiltrar = document.getElementById('btnFiltrar');
const filterPanel = document.getElementById('filterPanel');
const filtroNR = document.getElementById('filtroNR');
const filtroStatus = document.getElementById('filtroStatus');
const btnLimparFiltro = document.getElementById('btnLimparFiltro');

const modalOverlay = document.getElementById('modalOverlay');
const modalTitle = document.getElementById('modalTitle');
const imgUpload = document.getElementById('imgUpload');
const inputImagem = document.getElementById('inputImagem');
const preview = document.getElementById('imgUploadPreview');
const removeBtn = document.getElementById('imgUploadRemove');
const inputTitulo = document.getElementById('inputTitulo');
const inputSubtitulo = document.getElementById('inputSubtitulo');
const inputLinkAulas = document.getElementById('inputLinkAulas');
const inputCarga = document.getElementById('inputCarga');
const inputValidade = document.getElementById('inputValidade');
const toggleSemValidade = document.getElementById('toggleSemValidade');
const selectNR = document.getElementById('selectNR');
const btnExcluir = document.getElementById('btnExcluir');

// ===== Carregamento inicial =====
async function carregarTreinamentos() {
  try {
    const resp = await fetch(`${API_URL}?acao=listar`);
    const json = await resp.json();
    if (!json.success) throw new Error(json.message || 'Erro ao carregar.');
    treinamentos = json.data;
    aplicarFiltros();
  } catch (err) {
    console.error(err);
    grid.innerHTML = '<p>Não foi possível carregar os treinamentos. Tente novamente mais tarde.</p>';
  }
}

// ===== KPIs =====
function updateKpis() {
  const total = treinamentos.length;
  const validos = treinamentos.filter(t => t.status === 'valido').length;
  document.getElementById('kpiTotal').textContent = total;
  document.getElementById('kpiValidos').textContent = validos;
  document.getElementById('kpiInvalidos').textContent = total - validos;
}

// ===== Render dos cards =====
function renderCursos(lista) {
  if (lista.length === 0) {
    grid.innerHTML = '<p>Nenhum treinamento encontrado.</p>';
    return;
  }

  grid.innerHTML = lista.map(t => {
    const imagem = t.imagem_treinamento || IMG_PADRAO;
    const cargaTexto = `${t.carga_horaria_treinamento}h`;
    const statusTexto = t.status === 'valido' ? 'Válido' : 'Inválido';

    // O controle de provas fica em provas-adm.js; aqui só consultamos se já existe uma para este treinamento.
    const temProva = (typeof provasPorTreinamento !== 'undefined') && !!provasPorTreinamento[t.id_treinamento];
    const textoBotaoProva = temProva ? 'Editar prova' : 'Criar Prova';

    return `
      <div class="course-card">
        <div class="course-thumb">
          <img src="${imagem}" alt="${escapeHtml(t.nome_treinamento)}" loading="lazy">
          <span class="nr-badge">${escapeHtml(t.nr_treinamento)}</span>
        </div>
        <div class="course-body">
          <div class="course-title-row">
            <h3>${escapeHtml(t.nome_treinamento)}</h3>
            <span class="status-chip ${t.status}">${statusTexto}</span>
          </div>
          <p class="course-desc">${escapeHtml(t.subtitulo_treinamento || '')}</p>
          <div class="course-meta">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <span>${cargaTexto}</span>
          </div>

          <button class="btn-editar" onclick="editarTreinamento(${t.id_treinamento})">Editar</button>

          <div class="action-row">
            <button class="btn-outline-sm" onclick="abrirModalProva(${t.id_treinamento})">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/><line x1="9" y1="14" x2="15" y2="14"/><line x1="9" y1="18" x2="13" y2="18"/><line x1="9" y1="10" x2="15" y2="10"/></svg>
              ${textoBotaoProva}
            </button>

            <button class="btn-outline-sm" onclick="verVideoaulas('${escapeHtml(t.link_aulas_treinamento)}')">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg>
              Videoaulas
            </button>
          </div>
        </div>
      </div>
    `;
  }).join('');
}

function escapeHtml(str) {
  const div = document.createElement('div');
  div.textContent = str ?? '';
  return div.innerHTML;
}

function verVideoaulas(link) {
  if (!link) { alert('Nenhum link de videoaula cadastrado.'); return; }
  window.open(link, '_blank');
}

// ===== Busca + filtro (tudo no lado do cliente, sobre os dados já carregados) =====
function aplicarFiltros() {
  const termo = searchInput.value.trim().toLowerCase();
  const nr = filtroNR.value;
  const status = filtroStatus.value;

  const filtrada = treinamentos.filter(t => {
    const bateBusca = !termo ||
      t.nome_treinamento.toLowerCase().includes(termo) ||
      (t.subtitulo_treinamento || '').toLowerCase().includes(termo) ||
      t.nr_treinamento.toLowerCase().includes(termo);
    const bateNR = !nr || t.nr_treinamento === nr;
    const bateStatus = !status || t.status === status;
    return bateBusca && bateNR && bateStatus;
  });

  renderCursos(filtrada);
  updateKpis();
}

searchInput.addEventListener('input', aplicarFiltros);
filtroNR.addEventListener('change', aplicarFiltros);
filtroStatus.addEventListener('change', aplicarFiltros);

btnFiltrar.addEventListener('click', () => filterPanel.classList.toggle('open'));

btnLimparFiltro.addEventListener('click', () => {
  filtroNR.value = '';
  filtroStatus.value = '';
  aplicarFiltros();
});

// ===== Modal: abrir/fechar =====
function limparFormulario() {
  inputTitulo.value = '';
  inputSubtitulo.value = '';
  inputLinkAulas.value = '';
  inputCarga.value = '';
  inputValidade.value = '';
  toggleSemValidade.checked = true;
  inputValidade.disabled = true;
  selectNR.selectedIndex = 0;
  preview.src = '';
  preview.hidden = true;
  inputImagem.value = '';
  imgUpload.classList.remove('has-image');
}

function abrirModalCriar() {
  editingId = null;
  modalTitle.textContent = 'Criar novo treinamento';
  btnExcluir.style.display = 'none';
  limparFormulario();
  modalOverlay.classList.add('open');
}

function editarTreinamento(id) {
  const t = treinamentos.find(x => x.id_treinamento === id);
  if (!t) return;

  editingId = id;
  modalTitle.textContent = 'Editar treinamento';
  btnExcluir.style.display = 'inline-block';

  inputTitulo.value = t.nome_treinamento;
  inputSubtitulo.value = t.subtitulo_treinamento || '';
  inputLinkAulas.value = t.link_aulas_treinamento || '';
  inputCarga.value = t.carga_horaria_treinamento;

  const temValidade = !!t.data_limite_treinamento;
  toggleSemValidade.checked = !temValidade;
  inputValidade.disabled = !temValidade;
  inputValidade.value = temValidade ? t.data_limite_treinamento : '';

  selectNR.value = t.nr_treinamento;

  if (t.imagem_treinamento) {
    preview.src = t.imagem_treinamento;
    preview.hidden = false;
    imgUpload.classList.add('has-image');
  } else {
    preview.src = '';
    preview.hidden = true;
    imgUpload.classList.remove('has-image');
  }
  inputImagem.value = '';

  modalOverlay.classList.add('open');
}

function fecharModal() {
  modalOverlay.classList.remove('open');
  editingId = null;
}

document.getElementById('btnCriarTreinamento').addEventListener('click', abrirModalCriar);
document.getElementById('modalBackBtn').addEventListener('click', fecharModal);

modalOverlay.addEventListener('click', (e) => {
  if (e.target === modalOverlay) fecharModal();
});

toggleSemValidade.addEventListener('change', function () {
  inputValidade.disabled = this.checked;
  if (this.checked) inputValidade.value = '';
});

// ===== Upload de imagem (preview local) =====
inputImagem.addEventListener('change', function (event) {
  const file = event.target.files && event.target.files[0];
  if (!file) return;
  if (!file.type.startsWith('image/')) {
    alert('Selecione um arquivo de imagem válido.');
    return;
  }
  const reader = new FileReader();
  reader.onload = (e) => {
    preview.src = e.target.result;
    preview.hidden = false;
    imgUpload.classList.add('has-image');
  };
  reader.readAsDataURL(file);
});

removeBtn.addEventListener('click', (event) => {
  event.stopPropagation();
  preview.src = '';
  preview.hidden = true;
  inputImagem.value = '';
  imgUpload.classList.remove('has-image');
});

// ===== Salvar (criar ou editar) =====
document.getElementById('btnSalvar').addEventListener('click', async function () {
  const titulo = inputTitulo.value.trim();
  const linkAulas = inputLinkAulas.value.trim();
  const carga = inputCarga.value.trim();
  const nr = selectNR.value;

  if (!titulo) { alert('Digite o título do treinamento.'); inputTitulo.focus(); return; }
  if (!nr) { alert('Selecione a NR relacionada.'); selectNR.focus(); return; }
  if (!carga || Number(carga) <= 0) { alert('Informe a carga horária.'); inputCarga.focus(); return; }
  if (!linkAulas) { alert('Informe o link das videoaulas.'); inputLinkAulas.focus(); return; }

  const formData = new FormData();
  formData.append('acao', editingId !== null ? 'editar' : 'criar');
  if (editingId !== null) formData.append('id_treinamento', editingId);
  formData.append('nome_treinamento', titulo);
  formData.append('subtitulo_treinamento', inputSubtitulo.value.trim());
  formData.append('link_aulas_treinamento', linkAulas);
  formData.append('carga_horaria_treinamento', carga);
  formData.append('nr_treinamento', nr);
  formData.append('sem_validade_treinamento', toggleSemValidade.checked ? '1' : '0');
  if (!toggleSemValidade.checked) {
    formData.append('data_limite_treinamento', inputValidade.value);
  }
  if (inputImagem.files[0]) {
    formData.append('imagem_treinamento', inputImagem.files[0]);
  }

  this.disabled = true;
  try {
    const resp = await fetch(API_URL, { method: 'POST', body: formData });
    const json = await resp.json();
    if (!json.success) { alert(json.message || 'Erro ao salvar.'); return; }

    await carregarTreinamentos();
    fecharModal();
  } catch (err) {
    console.error(err);
    alert('Erro de conexão ao salvar o treinamento.');
  } finally {
    this.disabled = false;
  }
});

// ===== Excluir =====
btnExcluir.addEventListener('click', async function () {
  if (editingId === null) { fecharModal(); return; }

  const t = treinamentos.find(x => x.id_treinamento === editingId);
  if (!confirm(`Excluir o treinamento "${t ? t.nome_treinamento : ''}"? Essa ação não pode ser desfeita.`)) return;

  const formData = new FormData();
  formData.append('acao', 'excluir');
  formData.append('id_treinamento', editingId);

  this.disabled = true;
  try {
    const resp = await fetch(API_URL, { method: 'POST', body: formData });
    const json = await resp.json();
    if (!json.success) { alert(json.message || 'Erro ao excluir.'); return; }

    await carregarTreinamentos();
    fecharModal();
  } catch (err) {
    console.error(err);
    alert('Erro de conexão ao excluir o treinamento.');
  } finally {
    this.disabled = false;
  }
});

// ===== Inicialização =====
carregarTreinamentos();