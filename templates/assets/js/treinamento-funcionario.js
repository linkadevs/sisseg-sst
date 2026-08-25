/* ==========================================================================
   Treinamentos — visão do funcionário
   Busca os treinamentos reais do banco (via treinamento-funcionario-api.php)
   e só faz: renderizar cards, trocar de aba, atualizar KPIs e navegar
   para as ações (assistir, fazer prova, ver certificado).
   ========================================================================== */

const API_URL = '../View/treinamento-funcionario-api.php';
const IMG_PADRAO = 'https://images.unsplash.com/photo-1581094794329-c8112a89af12?w=600&h=340&fit=crop';

let treinamentos = [];   // dados vindos do banco, já com status/data_conclusao do funcionário logado
let filtroAtual = 'todos';

const grid = document.getElementById('coursesGrid');

/* ---------- Carregamento ---------- */
async function carregarTreinamentos() {
  try {
    const resp = await fetch(`${API_URL}?acao=listar`);
    const json = await resp.json();
    if (!json.success) throw new Error(json.message || 'Erro ao carregar treinamentos.');

    treinamentos = json.data;
    updateKpis();
    updateProgresso();
    renderCursos();
  } catch (err) {
    console.error(err);
    grid.innerHTML = '<p>Não foi possível carregar os treinamentos. Tente novamente mais tarde.</p>';
  }
}

/* ---------- KPIs (Total / Válidos / Inválidos) ---------- */
function updateKpis() {
  const total = treinamentos.length;
  const validos = treinamentos.filter(t => t.status === 'valido').length;
  const invalidos = total - validos;

  document.getElementById('kpiTotal').textContent = total;
  document.getElementById('kpiValidos').textContent = validos;
  document.getElementById('kpiInvalidos').textContent = invalidos;

  // também atualiza os contadores das abas
  document.querySelector('[data-filtro="todos"]').textContent = `Todos (${total})`;
  document.querySelector('[data-filtro="valido"]').textContent = `Válidos (${validos})`;
  document.querySelector('[data-filtro="invalido"]').textContent = `Inválidos (${invalidos})`;
}

/* ---------- Barra de progresso (concluídos com aprovação / total) ---------- */
function updateProgresso() {
  const total = treinamentos.length;
  const concluidos = treinamentos.filter(t => !!t.data_conclusao).length;
  const pct = total === 0 ? 0 : Math.round((concluidos / total) * 100);

  const progressTexto = document.querySelector('.progress-top span:first-child');
  const progressPct = document.querySelector('.progress-top .pct');
  const progressFill = document.querySelector('.progress-fill');

  if (progressTexto) progressTexto.textContent = `Progresso: ${concluidos}/${total}`;
  if (progressPct) progressPct.textContent = `${pct}%`;
  if (progressFill) progressFill.style.width = `${pct}%`;
}

/* ---------- Render dos cards ---------- */
function renderCursos() {
  const lista = treinamentos.filter(t => filtroAtual === 'todos' ? true : t.status === filtroAtual);

  if (lista.length === 0) {
    grid.innerHTML = '<p>Nenhum treinamento encontrado.</p>';
    return;
  }

  grid.innerHTML = lista.map(t => {
    const imagem = t.imagem_treinamento || IMG_PADRAO;
    const cargaTexto = `${t.carga_horaria_treinamento} horas`;
    const validadeTexto = t.data_limite_treinamento ? ` &bull; Validade: ${formatarData(t.data_limite_treinamento)}` : '';
    const statusLabel = t.status === 'valido'
      ? (t.data_limite_treinamento ? 'VÁLIDO' : 'VÁLIDO*')
      : 'INVÁLIDO';

    const acoesExtras = t.informativo ? '' : `
      <div class="action-row">
        <button class="btn-outline-sm" onclick="fazerProva(${t.id_treinamento})">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          Fazer Prova
        </button>
        <button class="btn-outline-sm ${t.status === 'invalido' ? 'disabled' : ''}"
          onclick="${t.status === 'invalido' ? '' : `verCertificado(${t.id_treinamento})`}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M8.21 13.89 7 23l5-3 5 3-1.21-9.12"/></svg>
          Certificado
        </button>
      </div>`;

    const conclusaoRow = t.data_conclusao
      ? `<div class="conclusao-row"><span>Conclusão:</span><span>${formatarData(t.data_conclusao)}</span></div>`
      : '';

    return `
      <div class="course-card">
        <div class="course-thumb">
          <img src="${imagem}" alt="${escapeHtml(t.nome_treinamento)}" loading="lazy">
          <span class="nr-badge">${escapeHtml(t.nr_treinamento)}</span>
        </div>
        <div class="course-body">
          <div class="course-title-row">
            <h3>${escapeHtml(t.nome_treinamento)}</h3>
            <span class="status-badge ${t.status === 'valido' ? 'valido' : 'invalido'}">${statusLabel}</span>
          </div>
          <p class="course-desc">${escapeHtml(t.subtitulo_treinamento || '')}</p>
          <div class="course-meta">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <span>${cargaTexto}${validadeTexto}</span>
          </div>

          <button class="btn-assistir" onclick="assistir('${escapeHtml(t.link_aulas_treinamento)}')">
            <svg viewBox="0 0 24 24" fill="currentColor"><polygon points="6 3 20 12 6 21 6 3"/></svg>
            Assistir
          </button>

          ${acoesExtras}
          ${conclusaoRow}
        </div>
      </div>
    `;
  }).join('');
}

/* ---------- Utilidades ---------- */
function escapeHtml(str) {
  const div = document.createElement('div');
  div.textContent = str ?? '';
  return div.innerHTML;
}

function formatarData(isoDate) {
  // aceita 'YYYY-MM-DD' (vindo do banco) e devolve 'DD/MM/AAAA'
  const [ano, mes, dia] = isoDate.split('-');
  if (!ano || !mes || !dia) return isoDate;
  return `${dia}/${mes}/${ano}`;
}

/* ---------- Abas ---------- */
function setFiltro(f) {
  filtroAtual = f;
  document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.classList.toggle('active', btn.dataset.filtro === f);
  });
  renderCursos();
}

/* ---------- Ações dos cards ---------- */
function assistir(link) {
  if (!link) {
    alert('Nenhum link de videoaula cadastrado para este treinamento.');
    return;
  }
  window.open(link, '_blank');
}

function fazerProva(idTreinamento) {
  window.location.href = `../View/prova.html?id_treinamento=${idTreinamento}`;
}

function verCertificado(idTreinamento) {
  window.location.href = `../View/certificado.html?id_treinamento=${idTreinamento}`;
}

/* ---------- Inicialização ---------- */
carregarTreinamentos();