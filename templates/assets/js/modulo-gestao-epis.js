// ===== VARIÁVEIS GLOBAIS =====
let editandoId = null;
let atividadeAtualSlug = null;
let streamCamera = null;
let fotoCheckinBase64 = null;
let arquivoCheckinFallback = null;

const EPI_API = '../View/epi-api.php';
const INSPECAO_API = '../View/inspecao-api.php';

// ===== CARREGAMENTO INICIAL =====

document.addEventListener('DOMContentLoaded', carregarEstoque);

async function carregarEstoque() {
  try {
    const resposta = await fetch(`${EPI_API}?acao=listar`);
    const dados = await resposta.json();
    if (!dados.sucesso) throw new Error(dados.mensagem);

    const stockList = document.getElementById('stock-list');
    stockList.innerHTML = '';
    dados.epis.forEach(epi => stockList.appendChild(criarLinhaEstoque(epi)));
  } catch (erro) {
    console.error('[Carregar Estoque] Falha ao buscar EPIs:', erro);
  }
}

function calcularStatusFront(qtd, minimo) {
  if (isNaN(qtd) || isNaN(minimo) || minimo <= 0) return 'ok';
  if (qtd >= minimo) return 'ok';
  if (qtd >= minimo * 0.5) return 'alert';
  return 'critical';
}

function atualizarStatusPreview() {
  const qtd = parseInt(document.getElementById('epiQuantidade').value);
  const minimo = parseInt(document.getElementById('epiMinimo').value);
  const status = calcularStatusFront(qtd, minimo);
  const badge = document.getElementById('epiStatusBadge');
  badge.className = `badge ${status}`;
  badge.textContent = textoDoStatus(status);
}

function criarLinhaEstoque(epi) {
  const div = document.createElement('div');
  div.className = `stock-row status-${epi.status_epi}`;
  div.setAttribute('data-id', epi.id_epi);
  div.setAttribute('data-descricao', epi.descricao_epi || '');
  div.setAttribute('data-funcao', epi.funcao_epi || '');
  div.setAttribute('data-ca', epi.ca_epi || '');
  div.innerHTML = `
    <span class="stock-icon ${corDoStatus(epi.status_epi)}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 18v-1a8 8 0 0 1 16 0v1"></path><rect x="3" y="18" width="18" height="3" rx="1"></rect><line x1="12" y1="4" x2="12" y2="7"></line></svg>
    </span>
    <div class="stock-info">
      <p class="stock-name">${epi.nome_epi}</p>
      <p class="stock-qty">Disponível: ${epi.qtd_epi} | Mínimo: ${epi.qtd_minima_epi}</p>
    </div>
    <span class="badge ${epi.status_epi}">${textoDoStatus(epi.status_epi)}</span>
    <div class="stock-actions">
      <button class="btn-icon" onclick="editarEPI(${epi.id_epi})" title="Editar EPI">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
      </button>
      <button class="btn-icon" onclick="aumentarQtd(${epi.id_epi})" title="Aumentar quantidade">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
      </button>
      <button class="btn-icon" onclick="diminuirQtd(${epi.id_epi})" title="Diminuir quantidade">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line></svg>
      </button>
      <button class="btn-icon btn-danger" onclick="excluirEPI(${epi.id_epi})" title="Excluir EPI">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
      </button>
    </div>
  `;
  return div;
}

function corDoStatus(status) {
  return status === 'ok' ? 'green' : status === 'alert' ? 'orange' : 'red';
}

function textoDoStatus(status) {
  return status === 'ok' ? 'OK' : status === 'alert' ? 'Alerta' : 'Crítico';
}

// ===== FUNÇÕES DO MODAL EPI =====

function abrirModalNovoEPI() {
  editandoId = null;
  document.getElementById('modalEpiTitle').textContent = 'Novo EPI';
  document.getElementById('epiId').value = '';
  document.getElementById('epiNome').value = '';
  document.getElementById('epiDescricao').value = '';
  document.getElementById('epiFuncao').value = '';
  document.getElementById('epiCa').value = '';
  document.getElementById('epiQuantidade').value = '';
  document.getElementById('epiMinimo').value = '';
  document.getElementById('epiStatus').value = 'ok';
  document.getElementById('btnExcluirModal').style.display = 'none';
  document.getElementById('modalEpi').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function abrirModalNovoEPI() {
  editandoId = null;
  document.getElementById('modalEpiTitle').textContent = 'Novo EPI';
  document.getElementById('epiId').value = '';
  document.getElementById('epiNome').value = '';
  document.getElementById('epiDescricao').value = '';
  document.getElementById('epiFuncao').value = '';
  document.getElementById('epiCa').value = '';
  document.getElementById('epiQuantidade').value = '';
  document.getElementById('epiMinimo').value = '';
  atualizarStatusPreview();
  document.getElementById('btnExcluirModal').style.display = 'none';
  document.getElementById('modalEpi').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function editarEPI(id) {
  editandoId = id;
  const row = document.querySelector(`.stock-row[data-id="${id}"]`);
  if (!row) return;

  const nome = row.querySelector('.stock-name').textContent;
  const qtdText = row.querySelector('.stock-qty').textContent;
  const disponivel = parseInt(qtdText.match(/Disponível:\s*(\d+)/)[1]);
  const minimo = parseInt(qtdText.match(/Mínimo:\s*(\d+)/)[1]);

  document.getElementById('modalEpiTitle').textContent = 'Editar EPI';
  document.getElementById('epiId').value = id;
  document.getElementById('epiNome').value = nome;
  document.getElementById('epiDescricao').value = row.getAttribute('data-descricao') || '';
  document.getElementById('epiFuncao').value = row.getAttribute('data-funcao') || '';
  document.getElementById('epiCa').value = row.getAttribute('data-ca') || '';
  document.getElementById('epiQuantidade').value = disponivel;
  document.getElementById('epiMinimo').value = minimo;
  atualizarStatusPreview();
  document.getElementById('btnExcluirModal').style.display = 'block';
  document.getElementById('modalEpi').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function fecharModalEpi() {
  document.getElementById('modalEpi').classList.remove('open');
  document.body.style.overflow = 'auto';
  editandoId = null;
}

async function salvarEPI(event) {
  event.preventDefault();

  const nome = document.getElementById('epiNome').value.trim();
  const descricao = document.getElementById('epiDescricao').value.trim();
  const funcao = document.getElementById('epiFuncao').value.trim();
  const ca = document.getElementById('epiCa').value.trim();
  const quantidade = parseInt(document.getElementById('epiQuantidade').value);
  const minimo = parseInt(document.getElementById('epiMinimo').value);
  const id = document.getElementById('epiId').value;

  if (!nome || isNaN(quantidade) || isNaN(minimo)) {
    alert('Preencha todos os campos corretamente.');
    return;
  }

  const payload = {
    nome_epi: nome,
    descricao_epi: descricao,
    funcao_epi: funcao,
    ca_epi: ca,
    qtd_epi: quantidade,
    qtd_minima_epi: minimo,
  };

  try {
    const resposta = await fetch(`${EPI_API}?acao=${id ? 'atualizar' : 'criar'}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(id ? { id_epi: id, ...payload } : payload),
    });
    const dados = await resposta.json();
    if (!dados.sucesso) {
      alert(dados.mensagem || 'Não foi possível salvar o EPI.');
      return;
    }

    const stockList = document.getElementById('stock-list');
    const linhaExistente = document.querySelector(`.stock-row[data-id="${dados.epi.id_epi}"]`);
    const novaLinha = criarLinhaEstoque(dados.epi);
    if (linhaExistente) {
      linhaExistente.replaceWith(novaLinha);
    } else {
      stockList.appendChild(novaLinha);
    }

    fecharModalEpi();
  } catch (erro) {
    console.error('[Salvar EPI] Erro:', erro);
    alert('Erro de conexão ao salvar o EPI.');
  }
}

async function excluirEPIModal() {
  const id = document.getElementById('epiId').value;
  if (id && confirm('Tem certeza que deseja excluir este EPI?')) {
    await excluirEPI(parseInt(id));
    fecharModalEpi();
  }
}

// ===== FUNÇÕES DE QUANTIDADE =====

async function aumentarQtd(id) {
  await ajustarQuantidade(id, 1);
}

async function diminuirQtd(id) {
  await ajustarQuantidade(id, -1);
}

async function ajustarQuantidade(id, delta) {
  try {
    const resposta = await fetch(`${EPI_API}?acao=ajustar-quantidade`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id_epi: id, delta }),
    });
    const dados = await resposta.json();
    if (!dados.sucesso) {
      alert(dados.mensagem || 'Não foi possível atualizar a quantidade.');
      return;
    }

    const row = document.querySelector(`.stock-row[data-id="${id}"]`);
    if (!row) return;

    const minimoAtual = parseInt(row.querySelector('.stock-qty').textContent.match(/Mínimo:\s*(\d+)/)[1]);
    row.querySelector('.stock-qty').textContent = `Disponível: ${dados.qtd_epi} | Mínimo: ${minimoAtual}`;
    row.className = `stock-row status-${dados.status_epi}`;
    row.querySelector('.badge').className = `badge ${dados.status_epi}`;
    row.querySelector('.badge').textContent = textoDoStatus(dados.status_epi);
    row.querySelector('.stock-icon').className = `stock-icon ${corDoStatus(dados.status_epi)}`;
  } catch (erro) {
    console.error('[Ajustar Quantidade] Erro:', erro);
    alert('Erro de conexão ao atualizar a quantidade.');
  }
}

// ===== FUNÇÃO DE EXCLUSÃO =====

async function excluirEPI(id) {
  if (!confirm('Tem certeza que deseja excluir este EPI permanentemente?')) return;

  try {
    const resposta = await fetch(`${EPI_API}?acao=excluir`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id_epi: id }),
    });
    const dados = await resposta.json();
    if (!dados.sucesso) {
      alert(dados.mensagem || 'Não foi possível excluir o EPI.');
      return;
    }

    const row = document.querySelector(`.stock-row[data-id="${id}"]`);
    if (row) {
      row.style.transition = 'all 0.3s ease';
      row.style.opacity = '0';
      row.style.transform = 'translateX(-20px)';
      setTimeout(() => row.remove(), 300);
    }
  } catch (erro) {
    console.error('[Excluir EPI] Erro:', erro);
    alert('Erro de conexão ao excluir o EPI.');
  }
}

// ===== Base de dados dos EPIs obrigatórios por atividade ====

function verEpis(id, nome, nr, qtd, idNr) {
  var form = document.createElement('form');
  form.method = 'POST';
  form.action = 'salvar_session_adm.php';

  var campoId = document.createElement('input');
  campoId.type = 'hidden';
  campoId.name = 'id_atividade';
  campoId.value = id;
  form.appendChild(campoId);

  var campoNome = document.createElement('input');
  campoNome.type = 'hidden';
  campoNome.name = 'nome_atividade';
  campoNome.value = nome;
  form.appendChild(campoNome);

  var campoNr = document.createElement('input');
  campoNr.type = 'hidden';
  campoNr.name = 'nome_nr';
  campoNr.value = nr;
  form.appendChild(campoNr);

  var campoIdNr = document.createElement('input');
  campoIdNr.type = 'hidden';
  campoIdNr.name = 'id_nr_fk';
  campoIdNr.value = idNr;
  form.appendChild(campoIdNr);

  var campoQtdepis = document.createElement('input');
  campoQtdepis.type = 'hidden';
  campoQtdepis.name = 'quantidade_epis';
  campoQtdepis.value = qtd;
  form.appendChild(campoQtdepis);
  
  document.body.appendChild(form);
  form.submit();
}

function voltarLista() {
  document.getElementById('view-detail').style.display = 'none';
  document.getElementById('view-list').style.display = 'block';
  window.scrollTo(0, 0);
}