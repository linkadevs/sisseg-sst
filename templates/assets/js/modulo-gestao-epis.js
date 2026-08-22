// ===== VARIÁVEIS GLOBAIS =====
let editandoId = null;
let atividadeAtualSlug = null;
let streamCamera = null;
let fotoCheckinBase64 = null;
let arquivoCheckinFallback = null;

const EPI_API = '../api/epi-api.php';
const INSPECAO_API = '../api/inspecao-api.php';

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

function criarLinhaEstoque(epi) {
  const div = document.createElement('div');
  div.className = `stock-row status-${epi.status_epi}`;
  div.setAttribute('data-id', epi.id_epi);
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
  document.getElementById('epiQuantidade').value = '';
  document.getElementById('epiMinimo').value = '';
  document.getElementById('epiStatus').value = 'ok';
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
  const statusClass = row.className.match(/status-(\w+)/)[1];

  document.getElementById('modalEpiTitle').textContent = 'Editar EPI';
  document.getElementById('epiId').value = id;
  document.getElementById('epiNome').value = nome;
  document.getElementById('epiQuantidade').value = disponivel;
  document.getElementById('epiMinimo').value = minimo;
  document.getElementById('epiStatus').value = statusClass;
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
  const quantidade = parseInt(document.getElementById('epiQuantidade').value);
  const minimo = parseInt(document.getElementById('epiMinimo').value);
  const status = document.getElementById('epiStatus').value;
  const id = document.getElementById('epiId').value;

  if (!nome || isNaN(quantidade) || isNaN(minimo)) {
    alert('Preencha todos os campos corretamente.');
    return;
  }

  const payload = { nome_epi: nome, qtd_epi: quantidade, qtd_minima_epi: minimo, status_epi: status };

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

// ===== FUNÇÕES EXISTENTES (Trocas) =====

function agendarTroca(nome, epi) {
  console.log(`[Confirmar Troca] Colaborador: ${nome} | EPI: ${epi}`);
  alert(`Troca confirmada para ${nome} (${epi}).`);
}

// ===== Base de dados dos EPIs obrigatórios por atividade =====
const atividades = {
  altura: {
    nome: 'Trabalho em Altura', emoji: '🪜', nr: 'NR-35',
    epis: [
      { icon: '🧍', nome: 'Cinturão Paraquedista', desc: 'Proteção contra quedas de altura', ca: 'CA 38.570' },
      { icon: '🪝', nome: 'Talabarte com Absorvedor', desc: 'Conexão segura ao ponto de ancoragem', ca: 'CA 35.824' },
      { icon: '⛑️', nome: 'Capacete com Jugular', desc: 'Proteção da cabeça contra impactos', ca: 'CA 31.469' },
      { icon: '🧤', nome: 'Luvas Antiderrapantes', desc: 'Proteção das mãos e melhor aderência', ca: 'CA 29.837' },
      { icon: '🥾', nome: 'Bota de Segurança', desc: 'Proteção dos pés contra impactos e perfurações', ca: 'CA 42.123' },
    ]
  },
  alvenaria: {
    nome: 'Alvenaria', emoji: '🧱', nr: 'NR-18',
    epis: [
      { icon: '⛑️', nome: 'Capacete de Segurança', desc: 'Proteção da cabeça contra impactos e queda de materiais', ca: 'CA 31.469' },
      { icon: '🥽', nome: 'Óculos de Proteção', desc: 'Proteção contra respingos de argamassa e poeira', ca: 'CA 25.310' },
      { icon: '🧤', nome: 'Luvas de Raspa', desc: 'Proteção das mãos no manuseio de blocos e ferramentas', ca: 'CA 28.774' },
      { icon: '🥾', nome: 'Bota de Segurança', desc: 'Proteção dos pés contra impactos e perfurações', ca: 'CA 42.123' },
      { icon: '🎧', nome: 'Protetor Auricular', desc: 'Redução da exposição a ruídos de equipamentos', ca: 'CA 19.845' },
      { icon: '🧍', nome: 'Cinto Tipo Paraquedista', desc: 'Proteção em trabalhos sobre andaimes', ca: 'CA 38.570' },
    ]
  },
  carpintaria: {
    nome: 'Carpintaria', emoji: '🪚', nr: 'NR-18',
    epis: [
      { icon: '🥽', nome: 'Óculos de Proteção', desc: 'Proteção contra serragem e lascas de madeira', ca: 'CA 25.310' },
      { icon: '😷', nome: 'Máscara contra Poeira PFF1', desc: 'Proteção respiratória contra pó de madeira', ca: 'CA 21.556' },
      { icon: '🧤', nome: 'Luvas de Raspa', desc: 'Proteção das mãos no manuseio de madeira e ferramentas', ca: 'CA 28.774' },
      { icon: '🎧', nome: 'Protetor Auricular', desc: 'Redução da exposição a ruídos de serras elétricas', ca: 'CA 19.845' },
      { icon: '⛑️', nome: 'Capacete de Segurança', desc: 'Proteção contra queda de materiais', ca: 'CA 31.469' },
      { icon: '🥼', nome: 'Avental de Raspa', desc: 'Proteção do tronco contra cortes e farpas', ca: 'CA 30.221' },
    ]
  },
  pintura: {
    nome: 'Pintura', emoji: '🎨', nr: 'NR-18',
    epis: [
      { icon: '😷', nome: 'Máscara com Filtro Químico', desc: 'Proteção respiratória contra vapores de tinta e solvente', ca: 'CA 24.678' },
      { icon: '🥽', nome: 'Óculos Ampla Visão', desc: 'Proteção contra respingos de tinta', ca: 'CA 25.310' },
      { icon: '🧤', nome: 'Luvas de PVC', desc: 'Proteção das mãos contra produtos químicos', ca: 'CA 27.902' },
      { icon: '🥼', nome: 'Macacão de Proteção', desc: 'Proteção do corpo contra respingos de tinta', ca: 'CA 33.415' },
      { icon: '🥾', nome: 'Bota de Segurança', desc: 'Proteção dos pés contra impactos e produtos químicos', ca: 'CA 42.123' },
      { icon: '🧍', nome: 'Cinto Tipo Paraquedista', desc: 'Proteção em pintura de fachadas e altura', ca: 'CA 38.570' },
    ]
  },
  soldagem: {
    nome: 'Soldagem', emoji: '🔥', nr: 'NR-18',
    epis: [
      { icon: '🛡️', nome: 'Máscara de Solda', desc: 'Proteção dos olhos e face contra radiação e faíscas', ca: 'CA 34.201' },
      { icon: '🥼', nome: 'Avental de Raspa', desc: 'Proteção do tronco contra respingos de solda', ca: 'CA 30.221' },
      { icon: '🧤', nome: 'Luvas de Raspa Cano Longo', desc: 'Proteção das mãos e antebraços contra calor e respingos', ca: 'CA 28.774' },
      { icon: '🛡️', nome: 'Perneira de Raspa', desc: 'Proteção das pernas contra respingos de solda', ca: 'CA 32.560' },
      { icon: '🥾', nome: 'Bota com Biqueira', desc: 'Proteção dos pés contra queda de material e calor', ca: 'CA 42.123' },
      { icon: '😷', nome: 'Respirador PFF2', desc: 'Proteção contra fumos metálicos', ca: 'CA 22.897' },
    ]
  },
  eletricidade: {
    nome: 'Eletricidade', emoji: '⚡', nr: 'NR-10',
    epis: [
      { icon: '⛑️', nome: 'Capacete Classe B', desc: 'Proteção da cabeça com isolamento elétrico', ca: 'CA 31.988' },
      { icon: '🧤', nome: 'Luvas Isolantes de Borracha', desc: 'Proteção das mãos contra choque elétrico', ca: 'CA 26.412' },
      { icon: '🥽', nome: 'Óculos de Proteção', desc: 'Proteção contra arco elétrico e faíscas', ca: 'CA 25.310' },
      { icon: '🥾', nome: 'Calçado Isolante', desc: 'Proteção dos pés contra corrente elétrica', ca: 'CA 41.077' },
      { icon: '🥼', nome: 'Vestimenta Antichama', desc: 'Proteção do corpo contra arco elétrico', ca: 'CA 36.650' },
      { icon: '🎧', nome: 'Protetor Auricular', desc: 'Redução da exposição a ruídos em subestações', ca: 'CA 19.845' },
      { icon: '🧍', nome: 'Cinto Tipo Paraquedista', desc: 'Proteção em trabalhos em postes e altura', ca: 'CA 38.570' },
    ]
  },
  confinado: {
    nome: 'Espaço Confinado', emoji: '🔒', nr: 'NR-33',
    epis: [
      { icon: '📟', nome: 'Detector de Gases Portátil', desc: 'Monitoramento de atmosfera tóxica e explosiva', ca: 'CA 40.512' },
      { icon: '😷', nome: 'Máscara Autônoma', desc: 'Proteção respiratória em atmosferas deficientes de oxigênio', ca: 'CA 23.884' },
      { icon: '🧍', nome: 'Cinto Tipo Paraquedista', desc: 'Ancoragem para resgate em espaço confinado', ca: 'CA 38.570' },
      { icon: '⚙️', nome: 'Tripé de Resgate com Guincho', desc: 'Equipamento para içamento e resgate', ca: 'CA 39.221' },
      { icon: '⛑️', nome: 'Capacete com Jugular', desc: 'Proteção da cabeça contra impactos', ca: 'CA 31.469' },
      { icon: '🧤', nome: 'Luvas de Proteção Química', desc: 'Proteção das mãos contra substâncias nocivas', ca: 'CA 27.902' },
      { icon: '🔦', nome: 'Lanterna Intrinsecamente Segura', desc: 'Iluminação em áreas com risco de explosão', ca: 'CA 37.104' },
      { icon: '📻', nome: 'Rádio Comunicador', desc: 'Comunicação com equipe de vigia externo', ca: 'CA 39.900' },
    ]
  },
  demolicao: {
    nome: 'Demolição', emoji: '🔨', nr: 'NR-18',
    epis: [
      { icon: '⛑️', nome: 'Capacete de Segurança', desc: 'Proteção da cabeça contra queda de material', ca: 'CA 31.469' },
      { icon: '🥽', nome: 'Óculos de Proteção', desc: 'Proteção contra poeira e fragmentos', ca: 'CA 25.310' },
      { icon: '😷', nome: 'Máscara PFF2', desc: 'Proteção respiratória contra poeira de demolição', ca: 'CA 22.897' },
      { icon: '🧤', nome: 'Luvas de Raspa', desc: 'Proteção das mãos no manuseio de escombros', ca: 'CA 28.774' },
      { icon: '🥾', nome: 'Bota com Biqueira', desc: 'Proteção dos pés contra impactos e perfurações', ca: 'CA 42.123' },
      { icon: '🎧', nome: 'Protetor Auricular', desc: 'Redução da exposição a ruídos de equipamentos', ca: 'CA 19.845' },
      { icon: '🦺', nome: 'Colete Refletivo', desc: 'Sinalização e visibilidade na área de risco', ca: 'CA 18.330' },
    ]
  },
  armador: {
    nome: 'Armador de Ferro', emoji: '🔩', nr: 'NR-18',
    epis: [
      { icon: '⛑️', nome: 'Capacete de Segurança', desc: 'Proteção da cabeça contra impactos', ca: 'CA 31.469' },
      { icon: '🧤', nome: 'Luvas de Raspa', desc: 'Proteção das mãos no manuseio de vergalhões', ca: 'CA 28.774' },
      { icon: '🥽', nome: 'Óculos de Proteção', desc: 'Proteção contra fragmentos de arame e ferrugem', ca: 'CA 25.310' },
      { icon: '🥾', nome: 'Bota com Biqueira', desc: 'Proteção dos pés contra perfurações', ca: 'CA 42.123' },
      { icon: '🛡️', nome: 'Manga de Raspa', desc: 'Proteção dos antebraços contra cortes', ca: 'CA 30.998' },
      { icon: '🎧', nome: 'Protetor Auricular', desc: 'Redução da exposição a ruídos de máquinas de corte', ca: 'CA 19.845' },
    ]
  },
  concretagem: {
    nome: 'Concretagem', emoji: '🏗️', nr: 'NR-18',
    epis: [
      { icon: '⛑️', nome: 'Capacete de Segurança', desc: 'Proteção da cabeça contra impactos', ca: 'CA 31.469' },
      { icon: '🥾', nome: 'Bota de Borracha Cano Longo', desc: 'Proteção dos pés contra concreto e umidade', ca: 'CA 40.215' },
      { icon: '🧤', nome: 'Luvas de PVC', desc: 'Proteção das mãos contra o contato com o concreto', ca: 'CA 27.902' },
      { icon: '🥽', nome: 'Óculos de Proteção', desc: 'Proteção contra respingos de concreto', ca: 'CA 25.310' },
      { icon: '🎧', nome: 'Protetor Auricular', desc: 'Redução da exposição a ruídos da betoneira', ca: 'CA 19.845' },
      { icon: '🥼', nome: 'Avental Impermeável', desc: 'Proteção do corpo contra respingos de concreto', ca: 'CA 29.400' },
    ]
  },
  operador: {
    nome: 'Operador de Máquinas', emoji: '🚜', nr: 'NR-12',
    epis: [
      { icon: '⛑️', nome: 'Capacete de Segurança', desc: 'Proteção da cabeça contra impactos', ca: 'CA 31.469' },
      { icon: '🦺', nome: 'Colete Refletivo', desc: 'Sinalização e visibilidade no canteiro', ca: 'CA 18.330' },
      { icon: '🎧', nome: 'Protetor Auricular', desc: 'Redução da exposição a ruídos do motor', ca: 'CA 19.845' },
      { icon: '🥾', nome: 'Bota com Biqueira', desc: 'Proteção dos pés contra impactos', ca: 'CA 42.123' },
      { icon: '🧤', nome: 'Luvas de Proteção', desc: 'Proteção das mãos na operação e manutenção', ca: 'CA 26.850' },
    ]
  },
};

function verEpis(slug) {
  const atividade = atividades[slug];
  if (!atividade) {
    console.log(`[Ver EPIs] Atividade não encontrada: ${slug}`);
    return;
  }
  atividadeAtualSlug = slug;
  console.log(`[Ver EPIs] Atividade selecionada: ${atividade.nome}`);

  document.getElementById('detail-emoji').textContent = atividade.emoji;
  document.getElementById('detail-title').textContent = atividade.nome;
  document.getElementById('detail-subtitle').textContent = `EPIs obrigatórios - ${atividade.nr}`;

  const lista = document.getElementById('epi-list');
  lista.innerHTML = atividade.epis.map(epi => `
        <div class="epi-card">
            <div class="epi-icon">${epi.icon}</div>
            <div class="epi-info">
                <p class="epi-name">${epi.nome}</p>
                <p class="epi-desc">${epi.desc}</p>
                <div class="epi-tags">
                    <span class="epi-tag">${epi.ca}</span>
                    <span class="epi-tag">${atividade.nr}</span>
                </div>
            </div>
        </div>
    `).join('');

  document.getElementById('view-list').style.display = 'none';
  document.getElementById('view-detail').style.display = 'block';
  window.scrollTo(0, 0);
}

function voltarLista() {
  document.getElementById('view-detail').style.display = 'none';
  document.getElementById('view-list').style.display = 'block';
  window.scrollTo(0, 0);
}

// ===== CHECK-IN COM FOTO =====

async function realizarCheckin() {
  fotoCheckinBase64 = null;
  arquivoCheckinFallback = null;
  document.getElementById('checkinErro').style.display = 'none';
  document.getElementById('checkinPreview').style.display = 'none';
  document.getElementById('checkinUploadFallback').style.display = 'none';
  document.getElementById('btnConfirmarCheckin').disabled = true;
  document.getElementById('btnCapturarFoto').style.display = 'inline-block';

  document.getElementById('modalCheckin').classList.add('open');
  document.body.style.overflow = 'hidden';

  const video = document.getElementById('checkinVideo');

  try {
    streamCamera = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
    video.srcObject = streamCamera;
    video.style.display = 'block';
  } catch (erro) {
    console.warn('[Check-in] Câmera indisponível, usando upload manual:', erro);
    video.style.display = 'none';
    document.getElementById('btnCapturarFoto').style.display = 'none';
    document.getElementById('checkinUploadFallback').style.display = 'block';
  }
}

function capturarFotoCheckin() {
  const video = document.getElementById('checkinVideo');
  const canvas = document.getElementById('checkinCanvas');
  const preview = document.getElementById('checkinPreview');

  canvas.width = video.videoWidth;
  canvas.height = video.videoHeight;
  canvas.getContext('2d').drawImage(video, 0, 0);

  fotoCheckinBase64 = canvas.toDataURL('image/jpeg', 0.9);
  preview.src = fotoCheckinBase64;
  preview.style.display = 'block';

  pararCamera();
  video.style.display = 'none';
  document.getElementById('btnCapturarFoto').style.display = 'none';
  document.getElementById('btnConfirmarCheckin').disabled = false;
}

document.addEventListener('change', (evento) => {
  if (evento.target && evento.target.id === 'checkinFotoInput') {
    const arquivo = evento.target.files[0];
    if (!arquivo) return;

    arquivoCheckinFallback = arquivo;
    const preview = document.getElementById('checkinPreview');
    preview.src = URL.createObjectURL(arquivo);
    preview.style.display = 'block';
    document.getElementById('btnConfirmarCheckin').disabled = false;
  }
});

function pararCamera() {
  if (streamCamera) {
    streamCamera.getTracks().forEach(track => track.stop());
    streamCamera = null;
  }
}

async function confirmarCheckin() {
  const erroEl = document.getElementById('checkinErro');
  erroEl.style.display = 'none';

  const epis_verificados = atividadeAtualSlug && atividades[atividadeAtualSlug]
    ? atividades[atividadeAtualSlug].epis.length
    : 0;

  try {
    let resposta;

    if (arquivoCheckinFallback) {
      const formData = new FormData();
      formData.append('foto', arquivoCheckinFallback);
      formData.append('epis_verificados', epis_verificados);
      resposta = await fetch(`${INSPECAO_API}?acao=checkin`, { method: 'POST', body: formData });
    } else if (fotoCheckinBase64) {
      resposta = await fetch(`${INSPECAO_API}?acao=checkin`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ foto_base64: fotoCheckinBase64, epis_verificados }),
      });
    } else {
      erroEl.textContent = 'Capture ou selecione uma foto antes de confirmar.';
      erroEl.style.display = 'block';
      return;
    }

    const dados = await resposta.json();
    if (!dados.sucesso) {
      erroEl.textContent = dados.mensagem || 'Não foi possível concluir o check-in.';
      erroEl.style.display = 'block';
      return;
    }

    fecharModalCheckin();

    const alertaPendente = document.querySelector('.checkin-alert .title');
    if (alertaPendente) alertaPendente.textContent = 'Check-in concluído';
    const badgePendente = document.querySelector('.checkin-alert .badge');
    if (badgePendente) {
      badgePendente.className = 'badge ok';
      badgePendente.textContent = 'Concluído';
    }
  } catch (erro) {
    console.error('[Confirmar Check-in] Erro:', erro);
    erroEl.textContent = 'Erro de conexão ao enviar o check-in.';
    erroEl.style.display = 'block';
  }
}

function fecharModalCheckin() {
  pararCamera();
  document.getElementById('modalCheckin').classList.remove('open');
  document.body.style.overflow = 'auto';
  fotoCheckinBase64 = null;
  arquivoCheckinFallback = null;
}
