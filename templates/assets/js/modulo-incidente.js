/* ------------------------------------------------------------------
   CONFIG
------------------------------------------------------------------ */
const API_URL = '../Controller/IncidenteApi.php';

/* ------------------------------------------------------------------
   MAPEAMENTOS
------------------------------------------------------------------ */
const SEVERIDADE_LABEL = { alta: 'Alta', media: 'Média', baixa: 'Baixa' };
const SEVERIDADE_CLASS = { alta: 'badge--alta', media: 'badge--media', baixa: 'badge--baixa' };
const STATUS_CLASS = {
  'Aberto': 'badge--aberto',
  'Investigando': 'badge--investigando',
  'Concluído': 'badge--concluido',
};

/* ------------------------------------------------------------------
   ESTADO
------------------------------------------------------------------ */
let filtroStatusAtual = '';   // '' = todos | 'Aberto' | 'Investigando' | 'Concluído'
let incidenteSelecionadoId = null;

/* ------------------------------------------------------------------
   NAVEGAÇÃO SPA
------------------------------------------------------------------ */
function showPage(id) {
  document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
  document.getElementById(id).classList.add('active');
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

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
   HELPERS
------------------------------------------------------------------ */
function formatarData(dataISO) {
  if (!dataISO) return '';
  const [ano, mes, dia] = dataISO.split(' ')[0].split('-');
  return `${dia}/${mes}/${ano}`;
}

function gravKey(valor) {
  return (valor || '').toLowerCase();
}

/* ------------------------------------------------------------------
   CONTADORES (cards de resumo)
------------------------------------------------------------------ */
async function carregarContadores() {
  try {
    const resp = await fetch(`${API_URL}?action=counts`);
    const resultado = await resp.json();
    if (!resultado.success) return;

    const c = resultado.data;
    document.getElementById('countTodos').textContent = c.todos ?? 0;
    document.getElementById('countAberto').textContent = c['Aberto'] ?? 0;
    document.getElementById('countInvestigando').textContent = c['Investigando'] ?? 0;
    document.getElementById('countConcluido').textContent = c['Concluído'] ?? 0;
  } catch (err) {
    console.error('Erro ao carregar contadores:', err);
  }
}

/* ------------------------------------------------------------------
   RENDERIZAÇÃO DOS CARDS
------------------------------------------------------------------ */
function renderIncidents(lista) {
  const container = document.getElementById('incidentsList');
  const empty = document.getElementById('emptyState');
  container.innerHTML = '';

  if (!lista || lista.length === 0) {
    empty.style.display = 'block';
    return;
  }
  empty.style.display = 'none';

  lista.forEach(inc => {
    const grav = gravKey(inc.gravidade_incidente);
    const status = inc.status_incidente;

    const card = document.createElement('article');
    card.className = 'incident-card';
    card.innerHTML = `
      <div class="incident-card__body">
        <div class="incident-card__top">
          <span class="incident-id">${inc.codigo_incidente}</span>
          <span class="badge ${SEVERIDADE_CLASS[grav] || ''}">${SEVERIDADE_LABEL[grav] || inc.gravidade_incidente}</span>
          <span class="badge ${STATUS_CLASS[status] || ''}">${status}</span>
        </div>
        <p class="incident-card__desc">${inc.descricao_incidente}</p>
        <div class="incident-card__meta">
          <span>${formatarData(inc.data_incidente)}</span>
          <span>Local: ${inc.local_incidente}</span>
          <span>Atividade: ${inc.atividade_incidente}</span>
        </div>
      </div>
      <button class="btn-details" aria-label="Ver detalhes do incidente ${inc.codigo_incidente}">
        Ver Detalhes
      </button>
    `;

    card.querySelector('.btn-details').addEventListener('click', () => abrirDetalhe(inc.id_incidente));
    container.appendChild(card);
  });
}

/* ------------------------------------------------------------------
   BUSCA + FILTRO (server-side)
------------------------------------------------------------------ */
let debounceTimer = null;

async function carregarLista() {
  const query = document.getElementById('searchInput').value.trim();
  const params = new URLSearchParams({ action: 'list' });
  if (filtroStatusAtual) params.set('status', filtroStatusAtual);
  if (query) params.set('busca', query);

  try {
    const resp = await fetch(`${API_URL}?${params.toString()}`);
    const resultado = await resp.json();
    renderIncidents(resultado.success ? resultado.data : []);
  } catch (err) {
    console.error('Erro ao carregar incidentes:', err);
    renderIncidents([]);
  }
}

function aplicarFiltros() {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(carregarLista, 250);
}

document.getElementById('searchInput').addEventListener('input', aplicarFiltros);

document.getElementById('statusFilter').addEventListener('change', function () {
  filtroStatusAtual = this.value;
  sincronizarMetricCardAtivo();
  carregarLista();
});

/* Metric cards clicáveis funcionando como atalho de filtro */
function sincronizarMetricCardAtivo() {
  document.querySelectorAll('.metric-card').forEach(card => {
    card.classList.toggle('metric-card--active', card.dataset.status === filtroStatusAtual);
  });
}

document.querySelectorAll('.metric-card').forEach(card => {
  card.addEventListener('click', () => {
    filtroStatusAtual = card.dataset.status;
    document.getElementById('statusFilter').value = filtroStatusAtual;
    sincronizarMetricCardAtivo();
    carregarLista();
  });
});

/* ------------------------------------------------------------------
   ABRIR DETALHE (busca no backend, já traz a foto em base64)
------------------------------------------------------------------ */
async function abrirDetalhe(id) {
  try {
    const resp = await fetch(`${API_URL}?action=getById&id=${encodeURIComponent(id)}`);
    const resultado = await resp.json();

    if (!resultado.success) {
      showToast(resultado.message || 'Incidente não encontrado.', false);
      return;
    }

    const inc = resultado.data;
    incidenteSelecionadoId = inc.id_incidente;

    document.getElementById('detailId').textContent = inc.codigo_incidente;
    document.getElementById('detailDate').textContent = formatarData(inc.data_incidente);

    const grav = gravKey(inc.gravidade_incidente);
    const badgeSev = document.getElementById('detailBadgeSev');
    badgeSev.textContent = SEVERIDADE_LABEL[grav] || inc.gravidade_incidente;
    badgeSev.className = `badge ${SEVERIDADE_CLASS[grav] || ''}`;

    const badgeStatus = document.getElementById('detailBadgeStatus');
    badgeStatus.textContent = inc.status_incidente;
    badgeStatus.className = `badge ${STATUS_CLASS[inc.status_incidente] || ''}`;

    document.getElementById('detailTipo').textContent = inc.tipo_incidente;
    document.getElementById('detailLocal').textContent = inc.local_incidente;
    document.getElementById('detailAtividade').textContent = inc.atividade_incidente;
    document.getElementById('detailTreinamento').textContent = inc.treinamento_reciclagem_incidente || 'Não informado';
    document.getElementById('detailDescricao').textContent = inc.descricao_incidente;
    document.getElementById('detailTestemunhas').textContent = inc.testemunhas_incidente || 'Nenhuma testemunha informada';
    document.getElementById('detailAcao').textContent = inc.acao_imediata_incidente;

    const fotoEl = document.getElementById('detailPhoto');
    const fotoVazia = document.getElementById('detailPhotoEmpty');
    if (inc.foto_base64) {
      fotoEl.src = inc.foto_base64;
      fotoEl.style.display = 'block';
      fotoVazia.style.display = 'none';
    } else {
      fotoEl.removeAttribute('src');
      fotoEl.style.display = 'none';
      fotoVazia.style.display = 'block';
    }

    showPage('page-detail');
  } catch (err) {
    console.error('Erro ao abrir detalhe:', err);
    showToast('Não foi possível carregar o incidente.', false);
  }
}

/* ------------------------------------------------------------------
   VOLTAR À LISTA
------------------------------------------------------------------ */
document.getElementById('btnBackToList').addEventListener('click', () => {
  showPage('page-list');
  carregarLista();
  carregarContadores();
});

/* ------------------------------------------------------------------
   EXPORTAR PDF
------------------------------------------------------------------ */
document.getElementById('btnExportPdf').addEventListener('click', () => {
  if (!incidenteSelecionadoId) return;
  window.open(`../Controller/IncidentePdf.php?id=${encodeURIComponent(incidenteSelecionadoId)}`, '_blank');
});

/* ------------------------------------------------------------------
   MODAL — ATUALIZAR STATUS
------------------------------------------------------------------ */
const statusModal = document.getElementById('statusModal');
const statusOptionsWrapper = document.getElementById('statusOptions');
const btnConfirmStatus = document.getElementById('statusModalConfirm');

document.getElementById('btnUpdateStatus').addEventListener('click', () => {
  if (!incidenteSelecionadoId) return;

  const statusAtual = document.getElementById('detailBadgeStatus').textContent;
  statusModal.querySelectorAll('input[name="novoStatus"]').forEach(input => {
    input.checked = input.value === statusAtual;
  });
  atualizarSelecaoVisual();
  statusModal.classList.add('show');
});

statusOptionsWrapper.addEventListener('change', atualizarSelecaoVisual);

function atualizarSelecaoVisual() {
  statusModal.querySelectorAll('.modal-status-option').forEach(label => {
    const input = label.querySelector('input');
    label.classList.toggle('selected', input.checked);
  });
}

document.getElementById('statusModalCancel').addEventListener('click', () => {
  statusModal.classList.remove('show');
});

statusModal.addEventListener('click', (e) => {
  if (e.target === statusModal) statusModal.classList.remove('show');
});

btnConfirmStatus.addEventListener('click', async () => {
  const selecionado = statusModal.querySelector('input[name="novoStatus"]:checked');
  if (!selecionado || !incidenteSelecionadoId) return;

  btnConfirmStatus.disabled = true;
  btnConfirmStatus.textContent = 'Salvando...';

  try {
    const body = new URLSearchParams();
    body.set('id_incidente', incidenteSelecionadoId);
    body.set('status', selecionado.value);

    const resp = await fetch(`${API_URL}?action=updateStatus`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: body.toString(),
    });
    const resultado = await resp.json();

    if (!resultado.success) {
      showToast(resultado.message || 'Erro ao atualizar status.', false);
      return;
    }

    showToast('Status atualizado com sucesso!', true);
    statusModal.classList.remove('show');

    // Atualiza o badge na tela de detalhe sem precisar buscar tudo de novo
    const badgeStatus = document.getElementById('detailBadgeStatus');
    badgeStatus.textContent = selecionado.value;
    badgeStatus.className = `badge ${STATUS_CLASS[selecionado.value] || ''}`;

    carregarContadores();

  } catch (err) {
    console.error('Erro ao atualizar status:', err);
    showToast('Não foi possível conectar ao servidor.', false);
  } finally {
    btnConfirmStatus.disabled = false;
    btnConfirmStatus.textContent = 'Salvar';
  }
});

/* ------------------------------------------------------------------
   INIT
------------------------------------------------------------------ */
sincronizarMetricCardAtivo();
carregarContadores();
carregarLista();