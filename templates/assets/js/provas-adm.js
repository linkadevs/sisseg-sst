// ===== PROVAS: estado =====
const PROVA_API_URL = 'prova-api.php';

let provaQuestoes = [];
let questaoIdSeq = 0;
let editingProvaId = null;            // id_prova (do banco) quando em modo edição, null quando criando
let editingProvaTreinamentoId = null; // id_treinamento do card que abriu o modal

// Cache local só pra atualizar o texto do botão no card sem precisar recarregar tudo
let provasPorTreinamento = {};

const LETRAS_ALTERNATIVAS = ['a', 'b', 'c', 'd', 'e'];

// ===== Elementos =====
const modalProvaOverlay = document.getElementById('modalProvaOverlay');
const modalProvaTitle = document.getElementById('modalProvaTitle');
const provaTreinamentoSelect = document.getElementById('provaTreinamento');
const provaTituloInput = document.getElementById('provaTitulo');
const questoesContainer = document.getElementById('questoesContainer');
const btnAdicionarQuestao = document.getElementById('btnAdicionarQuestao');
const btnSalvarProva = document.getElementById('btnSalvarProva');
const btnExcluirProva = document.getElementById('btnExcluirProva');
const modalProvaBackBtn = document.getElementById('modalProvaBackBtn');

// ===== Popula o select de treinamentos vinculados =====
// Depende do array global `treinamentos`, carregado em treinamento_funcionario-adm.js
function popularSelectTreinamentos() {
  if (typeof treinamentos === 'undefined' || !treinamentos.length) {
    provaTreinamentoSelect.innerHTML = '<option value="" disabled selected>Nenhum treinamento cadastrado</option>';
    return;
  }
  provaTreinamentoSelect.innerHTML = treinamentos
    .map(t => `<option value="${t.id_treinamento}">${escapeHtml(t.nome_treinamento)}</option>`)
    .join('');
}

// ===== Estado de uma questão vazia =====
function novaQuestaoVazia() {
  questaoIdSeq += 1;
  return {
    id: questaoIdSeq,
    idQuestao: null, // id real da questão no banco (preenchido quando vem do backend)
    enunciado: '',
    alternativas: { a: '', b: '', c: '', d: '', e: '' },
    correta: ''
  };
}

function adicionarQuestao() {
  provaQuestoes.push(novaQuestaoVazia());
  renderQuestoes();
}

function removerQuestao(id) {
  provaQuestoes = provaQuestoes.filter(q => q.id !== id);
  renderQuestoes();
}

// ===== Render =====
function renderQuestoes() {
  if (provaQuestoes.length === 0) {
    questoesContainer.innerHTML = '<p class="questoes-vazio">Nenhuma questão adicionada ainda.</p>';
    return;
  }

  questoesContainer.innerHTML = provaQuestoes.map((q, index) => `
    <div class="questao-card" data-questao-id="${q.id}">
      <div class="questao-header">
        <span class="questao-numero">Questão ${index + 1}</span>
        <button type="button" class="btn-remover-questao" data-id="${q.id}" title="Remover questão">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>
      </div>

      <div class="form-group">
        <label class="form-label">Enunciado</label>
        <textarea class="form-input questao-enunciado" data-id="${q.id}" rows="3" placeholder="Digite o enunciado da questão">${escapeHtml(q.enunciado)}</textarea>
      </div>

      <div class="alternativas-list">
        ${LETRAS_ALTERNATIVAS.map(letra => `
          <div class="alternativa-row">
            <span class="alternativa-radio-wrap">
              <input type="radio" name="correta-${q.id}" value="${letra}" class="alternativa-radio" data-id="${q.id}" ${q.correta === letra ? 'checked' : ''}>
            </span>
            <span class="alternativa-letra">${letra.toUpperCase()}</span>
            <input type="text" class="form-input alternativa-texto" data-id="${q.id}" data-letra="${letra}" placeholder="Texto da alternativa ${letra.toUpperCase()}" value="${escapeHtml(q.alternativas[letra])}">
          </div>
        `).join('')}
      </div>
    </div>
  `).join('');
}

// ===== Delegação de eventos nas questões =====
questoesContainer.addEventListener('click', (e) => {
  const btnRemover = e.target.closest('.btn-remover-questao');
  if (btnRemover) removerQuestao(Number(btnRemover.dataset.id));
});

questoesContainer.addEventListener('input', (e) => {
  const id = Number(e.target.dataset.id);
  const questao = provaQuestoes.find(q => q.id === id);
  if (!questao) return;

  if (e.target.classList.contains('questao-enunciado')) {
    questao.enunciado = e.target.value;
  }
  if (e.target.classList.contains('alternativa-texto')) {
    questao.alternativas[e.target.dataset.letra] = e.target.value;
  }
});

questoesContainer.addEventListener('change', (e) => {
  if (e.target.classList.contains('alternativa-radio')) {
    const id = Number(e.target.dataset.id);
    const questao = provaQuestoes.find(q => q.id === id);
    if (questao) questao.correta = e.target.value;
  }
});

// ===== Carrega os dados vindos do backend (acao=buscar) no formulário =====
function carregarProvaNoFormulario(prova, questoes) {
  provaTituloInput.value = prova.nome_prova;
  provaQuestoes = questoes.map(q => {
    questaoIdSeq += 1;
    return {
      id: questaoIdSeq,
      idQuestao: q.id_questao,
      enunciado: q.enunciado_questao,
      alternativas: {
        a: q.alt_a_questao,
        b: q.alt_b_questao,
        c: q.alt_c_questao,
        d: q.alt_d_questao,
        e: q.alt_e_questao
      },
      correta: q.alternativa_questao
    };
  });
  renderQuestoes();
}

// ===== Limpa o formulário para criar uma nova prova =====
function resetarFormularioProva() {
  provaTituloInput.value = '';
  provaQuestoes = [];
  questaoIdSeq = 0;
  adicionarQuestao(); // já abre com uma questão pronta para preencher
}

// ===== Abrir o modal a partir do botão do card de um treinamento específico =====
async function abrirModalProva(idTreinamento) {
  editingProvaTreinamentoId = idTreinamento;
  editingProvaId = null;

  popularSelectTreinamentos();
  provaTreinamentoSelect.value = idTreinamento;
  provaTreinamentoSelect.disabled = true; // a prova já nasce vinculada ao treinamento do card

  modalProvaTitle.textContent = 'Carregando...';
  btnExcluirProva.style.display = 'none';
  provaTituloInput.value = '';
  provaQuestoes = [];
  questoesContainer.innerHTML = '';
  modalProvaOverlay.classList.add('open');

  try {
    const resp = await fetch(`${PROVA_API_URL}?acao=buscar&id_treinamento=${idTreinamento}`);
    const json = await resp.json();
    if (!json.success) throw new Error(json.message || 'Erro ao buscar a prova.');

    if (json.data) {
      editingProvaId = json.data.prova.id_prova;
      provasPorTreinamento[idTreinamento] = json.data.prova;
      modalProvaTitle.textContent = 'Editar prova';
      btnExcluirProva.style.display = 'inline-block';
      carregarProvaNoFormulario(json.data.prova, json.data.questoes);
    } else {
      modalProvaTitle.textContent = 'Criar nova prova';
      resetarFormularioProva();
    }
  } catch (err) {
    console.error(err);
    modalProvaTitle.textContent = 'Criar nova prova';
    resetarFormularioProva();
    alert('Não foi possível verificar se já existe uma prova para este treinamento. Você pode continuar criando uma nova.');
  }
}

function fecharModalProva() {
  modalProvaOverlay.classList.remove('open');
  editingProvaId = null;
  editingProvaTreinamentoId = null;
}

modalProvaBackBtn.addEventListener('click', fecharModalProva);
modalProvaOverlay.addEventListener('click', (e) => {
  if (e.target === modalProvaOverlay) fecharModalProva();
});
btnAdicionarQuestao.addEventListener('click', adicionarQuestao);

// ===== Validação =====
function validarProva() {
  const titulo = provaTituloInput.value.trim();
  const idTreinamento = provaTreinamentoSelect.value;

  if (!titulo) { alert('Digite o título da prova.'); provaTituloInput.focus(); return null; }
  if (!idTreinamento) { alert('Treinamento vinculado inválido.'); return null; }
  if (provaQuestoes.length === 0) { alert('Adicione ao menos uma questão.'); return null; }

  for (let i = 0; i < provaQuestoes.length; i++) {
    const q = provaQuestoes[i];
    const n = i + 1;

    if (!q.enunciado.trim()) { alert(`Digite o enunciado da questão ${n}.`); return null; }

    for (const letra of LETRAS_ALTERNATIVAS) {
      if (!q.alternativas[letra].trim()) {
        alert(`Preencha a alternativa ${letra.toUpperCase()} da questão ${n}.`);
        return null;
      }
    }

    if (!q.correta) { alert(`Marque a alternativa correta da questão ${n}.`); return null; }
  }

  // Formato alinhado com ProvaController::criarProva / editarProva
  const payload = {
    id_treinamento: idTreinamento,
    nome_prova: titulo,
    questoes: provaQuestoes.map(q => {
      const questao = {
        enunciado: q.enunciado.trim(),
        alternativa: q.correta,
        alt_a: q.alternativas.a.trim(),
        alt_b: q.alternativas.b.trim(),
        alt_c: q.alternativas.c.trim(),
        alt_d: q.alternativas.d.trim(),
        alt_e: q.alternativas.e.trim()
      };
      if (q.idQuestao) questao.id_questao = q.idQuestao;
      return questao;
    })
  };

  if (editingProvaId) payload.id_prova = editingProvaId;

  return payload;
}

// ===== Salvar (criar ou editar) =====
btnSalvarProva.addEventListener('click', async function () {
  const payload = validarProva();
  if (!payload) return;

  const acao = editingProvaId ? 'editar' : 'criar';

  this.disabled = true;
  try {
    const resp = await fetch(PROVA_API_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ acao, ...payload })
    });
    const json = await resp.json();
    if (!json.success) { alert(json.message || 'Erro ao salvar a prova.'); return; }

    provasPorTreinamento[payload.id_treinamento] = {
      id_prova: json.id_prova || editingProvaId,
      nome_prova: payload.nome_prova
    };

    fecharModalProva();
    if (typeof aplicarFiltros === 'function') aplicarFiltros(); // atualiza o texto do botão no card
  } catch (err) {
    console.error(err);
    alert('Erro de conexão ao salvar a prova.');
  } finally {
    this.disabled = false;
  }
});

// ===== Excluir =====
btnExcluirProva.addEventListener('click', async function () {
  if (!editingProvaId) { fecharModalProva(); return; }
  if (!confirm('Excluir esta prova? Essa ação não pode ser desfeita.')) return;

  this.disabled = true;
  try {
    const resp = await fetch(PROVA_API_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ acao: 'excluir', id_prova: editingProvaId })
    });
    const json = await resp.json();
    if (!json.success) { alert(json.message || 'Erro ao excluir a prova.'); return; }

    delete provasPorTreinamento[editingProvaTreinamentoId];
    fecharModalProva();
    if (typeof aplicarFiltros === 'function') aplicarFiltros(); // atualiza o texto do botão no card
  } catch (err) {
    console.error(err);
    alert('Erro de conexão ao excluir a prova.');
  } finally {
    this.disabled = false;
  }
});
