// ==========================================================================
// PGR — Gerador de Fichas de Risco
// Consome a API PHP (ficha-risco-api.php) para CRUD real no MySQL.
// Ajuste API_URL caso a posição do arquivo no projeto seja diferente.
// ==========================================================================

const API_URL = '../View/ficharisco-api.php';

document.addEventListener('DOMContentLoaded', () => {

  // ------------------------------------------------------------------
  // ESTADO DA APLICAÇÃO
  // ------------------------------------------------------------------
  // activities: { [id_atividade]: resumo retornado por listarTodas() }
  const state = {
    activities: {},
    currentDetailActivity: null // id_atividade
  };

  let editState = null; // rascunho do modal de edição (nulo quando fechado)

  function emptyRisk() {
    return {
      id_risco: null,
      riskType: 'Acidente',
      severity: 4,
      probability: 4,
      description: '',
      controlMeasures: [],
      epis: []
    };
  }

  const createDraft = {
    risks: [emptyRisk()],
    collective: [],
    procedures: []
  };

  // ------------------------------------------------------------------
  // ELEMENTOS DOM
  // ------------------------------------------------------------------
  const modalOverlay = document.getElementById('modalOverlay');
  const btnOpenModal = document.getElementById('btnOpenModal');
  const modalClose = document.getElementById('modalClose');
  const riskForm = document.getElementById('riskForm');

  const editModalOverlay = document.getElementById('editModalOverlay');
  const editModalClose = document.getElementById('editModalClose');
  const editModalCancel = document.getElementById('editModalCancel');
  const editModalSave = document.getElementById('editModalSave');
  const editModalDelete = document.getElementById('editModalDelete');
  const editModalBody = document.getElementById('editModalBody');

  const createRisksContainer = document.getElementById('createRisksContainer');
  const btnAddCreateRisk = document.getElementById('btnAddCreateRisk');

  const collectiveInput = document.getElementById('collectiveInput');
  const procedureInput = document.getElementById('procedureInput');
  const collectiveList = document.getElementById('collectiveList');
  const proceduresList = document.getElementById('proceduresList');

  const cardsGrid = document.getElementById('cardsGrid');
  const emptyState = document.getElementById('emptyState');

  const dashboardView = document.getElementById('dashboardView');
  const detailView = document.getElementById('detailView');
  const btnVoltarDashboard = document.getElementById('btnVoltarDashboard');

  const detailActivityName = document.getElementById('detailActivityName');
  const risksList = document.getElementById('risksList');
  const collectiveMeasuresOutput = document.getElementById('collectiveMeasuresOutput');
  const proceduresOutput = document.getElementById('proceduresOutput');

  // ------------------------------------------------------------------
  // CAMADA DE API
  // ------------------------------------------------------------------
  async function apiRequest(action, { method = 'GET', params = {}, body = null } = {}) {
    const query = new URLSearchParams({ action, ...params }).toString();
    const options = { method, headers: {} };

    if (body !== null) {
      options.headers['Content-Type'] = 'application/json';
      options.body = JSON.stringify(body);
    }

    let response, payload;
    try {
      response = await fetch(`${API_URL}?${query}`, options);
      payload = await response.json();
    } catch (err) {
      throw new Error('Não foi possível se comunicar com o servidor.');
    }

    if (!payload.sucesso) {
      throw new Error(payload.mensagem || 'Erro inesperado do servidor.');
    }
    return payload.dados;
  }

  // Converte o nível textual ("Crítico", "Alto"...) vindo do banco
  // para o slug usado nas classes CSS já existentes.
  function levelSlug(label) {
    const map = { 'Crítico': 'critico', 'Alto': 'alto', 'Médio': 'medio', 'Baixo': 'baixo' };
    return map[label] || 'baixo';
  }

  // Converte um risco no formato retornado pela API (buscarPorId)
  // para o formato usado internamente pelas funções de render.
  function mapRiscoFromServer(r) {
    return {
      id_risco: r.id_risco,
      riskType: r.tipo_risco,
      severity: parseInt(r.severidade_risco, 10),
      probability: parseInt(r.probabilidade_risco, 10),
      description: r.descricao_risco,
      level: levelSlug(r.nivel_risco),
      levelLabel: r.nivel_risco,
      controlMeasures: r.medidas_controle_risco || [],
      epis: r.epis_relacionados_risco || []
    };
  }

  // Monta o payload que a API espera a partir de um risco no formato interno
  function mapRiscoToPayload(r) {
    return {
      id_risco: r.id_risco || null,
      tipo: r.riskType,
      severidade: r.severity,
      probabilidade: r.probability,
      descricao: r.description,
      medidasControle: r.controlMeasures,
      epis: r.epis
    };
  }

  // ------------------------------------------------------------------
  // NORMAS REGULAMENTADORAS (NR) — popula os selects do formulário de
  // criação e do modal de edição
  // ------------------------------------------------------------------
  const activityNrSelect = document.getElementById('activityNr');
  let nrList = [];

  async function carregarNRs() {
    try {
      nrList = await apiRequest('listarNR');
    } catch (err) {
      alert(err.message);
      return;
    }
    activityNrSelect.innerHTML = '<option value="">Selecione a NR...</option>' +
      nrList.map(nr => `<option value="${nr.id_nr}">${escapeHTML(nr.nome_nr)}</option>`).join('');
  }

  function buildNrOptions(selectedId) {
    return nrList.map(nr =>
      `<option value="${nr.id_nr}" ${Number(nr.id_nr) === Number(selectedId) ? 'selected' : ''}>${escapeHTML(nr.nome_nr)}</option>`
    ).join('');
  }

  // ------------------------------------------------------------------
  // MODAL: ABRIR / FECHAR
  // ------------------------------------------------------------------
  function openModal() {
    modalOverlay.classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  function closeModal() {
    modalOverlay.classList.remove('open');
    document.body.style.overflow = '';
  }

  btnOpenModal.addEventListener('click', openModal);
  modalClose.addEventListener('click', closeModal);

  modalOverlay.addEventListener('click', (e) => {
    if (e.target === modalOverlay) closeModal();
  });

  document.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return;
    if (editModalOverlay.classList.contains('open')) {
      closeEditModal();
    } else if (modalOverlay.classList.contains('open')) {
      closeModal();
    }
  });

  // ------------------------------------------------------------------
  // MODAL DE EDIÇÃO: ABRIR / FECHAR
  // ------------------------------------------------------------------
  async function openEditModal(idAtividade) {
    let ficha;
    try {
      ficha = await apiRequest('buscar', { params: { id_atividade: idAtividade } });
    } catch (err) {
      alert(err.message);
      return;
    }

    editState = {
      idAtividade: ficha.id_atividade,
      originalName: ficha.nome,
      name: ficha.nome,
      idNr: ficha.id_nr,
      riscos: ficha.riscos.map(mapRiscoFromServer),
      medidasColetivas: [...ficha.medidasColetivas],
      procedimentos: [...ficha.procedimentos]
    };

    renderEditModalBody();
    editModalOverlay.classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  function closeEditModal() {
    editModalOverlay.classList.remove('open');
    document.body.style.overflow = '';
    editState = null;
  }

  editModalClose.addEventListener('click', closeEditModal);
  editModalCancel.addEventListener('click', closeEditModal);

  editModalDelete.addEventListener('click', () => {
    if (!editState) return;
    const idAtividade = editState.idAtividade;
    closeEditModal();
    deleteActivity(idAtividade);
  });

  editModalOverlay.addEventListener('click', (e) => {
    if (e.target === editModalOverlay) closeEditModal();
  });

  // ------------------------------------------------------------------
  // MODAL DE EDIÇÃO: RENDERIZAÇÃO DO CONTEÚDO
  // ------------------------------------------------------------------
  function renderEditModalBody() {
    const risksHTML = editState.riscos.map((risk, idx) => `
      <div class="edit-risk-card" data-risk-index="${idx}">
        <div class="edit-risk-card-header">
          <span>Risco ${idx + 1}</span>
          <button type="button" class="btn-remove-risk" data-remove-risk="${idx}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path><path d="M10 11v6"></path><path d="M14 11v6"></path></svg>
            Remover
          </button>
        </div>

        <div class="field-row">
          <div class="field">
            <label>Tipo de Risco</label>
            <select data-risk-field="riskType" data-risk-index="${idx}">
              ${['Físico', 'Químico', 'Ergonômico', 'Acidente'].map(t => `<option value="${t}" ${risk.riskType === t ? 'selected' : ''}>${t === 'Acidente' ? 'Acidentes' : t + 's'}</option>`).join('')}
            </select>
          </div>
          <div class="field">
            <label>Nível Calculado</label>
            <span class="badge-category" id="riskBadgePreview-${idx}" style="background:${levelColor(risk.level)};">${risk.levelLabel}</span>
          </div>
        </div>

        <div class="field-row">
          <div class="field">
            <label>Severidade</label>
            <select data-risk-field="severity" data-risk-index="${idx}">
              ${[1, 2, 3, 4, 5].map(n => `<option value="${n}" ${risk.severity === n ? 'selected' : ''}>${n}</option>`).join('')}
            </select>
          </div>
          <div class="field">
            <label>Probabilidade</label>
            <select data-risk-field="probability" data-risk-index="${idx}">
              ${[1, 2, 3, 4, 5].map(n => `<option value="${n}" ${risk.probability === n ? 'selected' : ''}>${n}</option>`).join('')}
            </select>
          </div>
        </div>

        <div class="field">
          <label>Descrição do Risco</label>
          <textarea rows="2" data-risk-field="description" data-risk-index="${idx}">${escapeHTML(risk.description)}</textarea>
        </div>

        <div class="field">
          <label>Medidas de Controle</label>
          <div class="add-item-row">
            <input type="text" data-risk-add="controlMeasures" data-risk-index="${idx}" placeholder="Nova medida de controle">
            <button type="button" class="btn-add" data-risk-add-btn="controlMeasures" data-risk-index="${idx}">+</button>
          </div>
          <ul class="chip-list">
            ${risk.controlMeasures.map((item, i) => `<li><span>${escapeHTML(item)}</span><button type="button" class="chip-remove" data-risk-remove-item="controlMeasures" data-risk-index="${idx}" data-item-index="${i}">&times;</button></li>`).join('')}
          </ul>
        </div>

        <div class="field">
          <label>EPIs Relacionados</label>
          <div class="add-item-row">
            <input type="text" data-risk-add="epis" data-risk-index="${idx}" placeholder="Novo EPI">
            <button type="button" class="btn-add" data-risk-add-btn="epis" data-risk-index="${idx}">+</button>
          </div>
          <ul class="chip-list">
            ${risk.epis.map((item, i) => `<li><span>${escapeHTML(item)}</span><button type="button" class="chip-remove" data-risk-remove-item="epis" data-risk-index="${idx}" data-item-index="${i}">&times;</button></li>`).join('')}
          </ul>
        </div>
      </div>
    `).join('');

    editModalBody.innerHTML = `
      <div class="field">
        <label for="editActivityName">Nome da Atividade</label>
        <select id="editActivityName">
          <option value="placeholder" selected disabled>Selecione uma atividade</option>
          ${atividades}
        </select>
      </div>
      <div class="field">
        <label for="editActivityNr">Norma Regulamentadora (NR)</label>
        <select id="editActivityNr">
          <option value="">Selecione a NR...</option>
          ${buildNrOptions(editState.idNr)}
        </select>
      </div>

      <hr class="edit-divider">
      <h3 class="edit-section-title">Riscos Identificados</h3>
      ${risksHTML || '<p class="list-empty">Nenhum risco cadastrado para esta atividade.</p>'}
      <button type="button" class="btn-add-risk" id="btnAddEditRisk">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
          <line x1="12" y1="5" x2="12" y2="19"></line>
          <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Adicionar Risco
      </button>

      <hr class="edit-divider">
      <h3 class="edit-section-title">Medidas de Proteção Coletiva</h3>
      <div class="add-item-row">
        <input type="text" id="editCollectiveInput" placeholder="Nova medida coletiva">
        <button type="button" class="btn-add" id="editAddCollective">+</button>
      </div>
      <ul class="chip-list" id="editCollectiveList">
        ${editState.medidasColetivas.map((item, i) => `<li><span>${escapeHTML(item)}</span><button type="button" class="chip-remove" data-edit-remove="medidasColetivas" data-item-index="${i}">&times;</button></li>`).join('')}
      </ul>

      <hr class="edit-divider">
      <h3 class="edit-section-title">Procedimentos Obrigatórios</h3>
      <div class="add-item-row">
        <input type="text" id="editProcedureInput" placeholder="Novo procedimento">
        <button type="button" class="btn-add" id="editAddProcedure">+</button>
      </div>
      <ul class="chip-list" id="editProcedureList">
        ${editState.procedimentos.map((item, i) => `<li><span>${escapeHTML(item)}</span><button type="button" class="chip-remove" data-edit-remove="procedimentos" data-item-index="${i}">&times;</button></li>`).join('')}
      </ul>
    `;

    attachEditModalEvents();
  }

  // ------------------------------------------------------------------
  // MODAL DE EDIÇÃO: EVENTOS DO CONTEÚDO DINÂMICO
  // ------------------------------------------------------------------
  function attachEditModalEvents() {
    const nameInput = document.getElementById('editActivityName');
    nameInput.addEventListener('input', () => {
      editState.name = nameInput.value;
    });

    const nrSelect = document.getElementById('editActivityNr');
    nrSelect.addEventListener('change', () => {
      editState.idNr = nrSelect.value ? parseInt(nrSelect.value, 10) : null;
    });

    document.getElementById('btnAddEditRisk').addEventListener('click', () => {
      const { level, label } = calculateRiskLevel(4, 4);
      editState.riscos.push({
        id_risco: null,
        riskType: 'Acidente',
        severity: 4,
        probability: 4,
        description: '',
        level,
        levelLabel: label,
        controlMeasures: [],
        epis: []
      });
      renderEditModalBody();
    });

    editModalBody.querySelectorAll('[data-risk-field]').forEach(el => {
      const eventName = el.tagName === 'TEXTAREA' ? 'input' : 'change';
      el.addEventListener(eventName, () => {
        const idx = parseInt(el.getAttribute('data-risk-index'), 10);
        const field = el.getAttribute('data-risk-field');
        const risk = editState.riscos[idx];

        if (field === 'severity' || field === 'probability') {
          risk[field] = parseInt(el.value, 10);
          const { level, label } = calculateRiskLevel(risk.probability, risk.severity);
          risk.level = level;
          risk.levelLabel = label;
          const badgeEl = document.getElementById(`riskBadgePreview-${idx}`);
          if (badgeEl) {
            badgeEl.textContent = label;
            badgeEl.style.background = levelColor(level);
          }
        } else if (field === 'description') {
          risk.description = el.value;
        } else if (field === 'riskType') {
          risk.riskType = el.value;
        }
      });
    });

    editModalBody.querySelectorAll('[data-remove-risk]').forEach(btn => {
      btn.addEventListener('click', () => {
        const idx = parseInt(btn.getAttribute('data-remove-risk'), 10);
        editState.riscos.splice(idx, 1);
        renderEditModalBody();
      });
    });

    editModalBody.querySelectorAll('[data-risk-add-btn]').forEach(btn => {
      const field = btn.getAttribute('data-risk-add-btn');
      const idx = parseInt(btn.getAttribute('data-risk-index'), 10);
      const input = editModalBody.querySelector(`input[data-risk-add="${field}"][data-risk-index="${idx}"]`);
      const commit = () => {
        const value = input.value.trim();
        if (!value) return;
        editState.riscos[idx][field].push(value);
        renderEditModalBody();
      };
      btn.addEventListener('click', commit);
      input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') { e.preventDefault(); commit(); }
      });
    });

    editModalBody.querySelectorAll('[data-risk-remove-item]').forEach(btn => {
      btn.addEventListener('click', () => {
        const field = btn.getAttribute('data-risk-remove-item');
        const idx = parseInt(btn.getAttribute('data-risk-index'), 10);
        const itemIdx = parseInt(btn.getAttribute('data-item-index'), 10);
        editState.riscos[idx][field].splice(itemIdx, 1);
        renderEditModalBody();
      });
    });

    const editCollectiveInput = document.getElementById('editCollectiveInput');
    const editAddCollective = document.getElementById('editAddCollective');
    const commitCollective = () => {
      const value = editCollectiveInput.value.trim();
      if (!value) return;
      editState.medidasColetivas.push(value);
      renderEditModalBody();
    };
    editAddCollective.addEventListener('click', commitCollective);
    editCollectiveInput.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') { e.preventDefault(); commitCollective(); }
    });

    const editProcedureInput = document.getElementById('editProcedureInput');
    const editAddProcedure = document.getElementById('editAddProcedure');
    const commitProcedure = () => {
      const value = editProcedureInput.value.trim();
      if (!value) return;
      editState.procedimentos.push(value);
      renderEditModalBody();
    };
    editAddProcedure.addEventListener('click', commitProcedure);
    editProcedureInput.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') { e.preventDefault(); commitProcedure(); }
    });

    editModalBody.querySelectorAll('[data-edit-remove]').forEach(btn => {
      btn.addEventListener('click', () => {
        const field = btn.getAttribute('data-edit-remove');
        const itemIdx = parseInt(btn.getAttribute('data-item-index'), 10);
        editState[field].splice(itemIdx, 1);
        renderEditModalBody();
      });
    });
  }

  // ------------------------------------------------------------------
  // MODAL DE EDIÇÃO: SALVAR ALTERAÇÕES (persiste no banco)
  // ------------------------------------------------------------------
  editModalSave.addEventListener('click', async () => {
    if (!editState) return;

    const newName = editState.name.trim();
    if (!newName) {
      alert('Por favor, informe o nome da atividade.');
      return;
    }

    if (!editState.idNr) {
      alert('Por favor, selecione a NR (Norma Regulamentadora) da atividade.');
      return;
    }

    const validRisks = editState.riscos.filter(r => r.description.trim());
    if (validRisks.length === 0) {
      alert('Adicione pelo menos um risco com a descrição preenchida.');
      return;
    }

    const payload = {
      nomeAtividade: newName,
      idNr: editState.idNr,
      riscos: validRisks.map(mapRiscoToPayload),
      medidasColetivas: editState.medidasColetivas,
      procedimentos: editState.procedimentos
    };

    editModalSave.disabled = true;
    try {
      await apiRequest('atualizar', {
        method: 'POST',
        params: { id_atividade: editState.idAtividade },
        body: payload
      });

      const wasViewingThis = state.currentDetailActivity === editState.idAtividade;
      const idAtividade = editState.idAtividade;

      closeEditModal();
      await carregarFichas();

      if (wasViewingThis) {
        await showDetail(idAtividade);
      }
    } catch (err) {
      alert(err.message);
    } finally {
      editModalSave.disabled = false;
    }
  });

  // ------------------------------------------------------------------
  // MODAL DE CRIAÇÃO: RENDERIZAÇÃO DOS BLOCOS DE RISCO
  // ------------------------------------------------------------------
  function renderCreateRisks() {
    createRisksContainer.innerHTML = createDraft.risks.map((risk, idx) => {
      const { level, label } = calculateRiskLevel(risk.probability, risk.severity);
      return `
        <div class="edit-risk-card" data-risk-index="${idx}">
          <div class="edit-risk-card-header">
            <span>Risco ${idx + 1}</span>
            ${createDraft.risks.length > 1 ? `
            <button type="button" class="btn-remove-risk" data-create-remove-risk="${idx}">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path><path d="M10 11v6"></path><path d="M14 11v6"></path></svg>
              Remover
            </button>` : ''}
          </div>

          <div class="field-row">
            <div class="field">
              <label>Tipo de Risco</label>
              <select data-create-field="riskType" data-risk-index="${idx}">
                ${['Físico', 'Químico', 'Ergonômico', 'Acidente'].map(t => `<option value="${t}" ${risk.riskType === t ? 'selected' : ''}>${t === 'Acidente' ? 'Acidentes' : t + 's'}</option>`).join('')}
              </select>
            </div>
            <div class="field">
              <label>Nível Calculado</label>
              <span class="badge-category" id="createBadgePreview-${idx}" style="background:${levelColor(level)};">${label}</span>
            </div>
          </div>

          <div class="field-row">
            <div class="field">
              <label>Severidade</label>
              <select data-create-field="severity" data-risk-index="${idx}">
                ${[1, 2, 3, 4, 5].map(n => `<option value="${n}" ${risk.severity === n ? 'selected' : ''}>${n}</option>`).join('')}
              </select>
            </div>
            <div class="field">
              <label>Probabilidade</label>
              <select data-create-field="probability" data-risk-index="${idx}">
                ${[1, 2, 3, 4, 5].map(n => `<option value="${n}" ${risk.probability === n ? 'selected' : ''}>${n}</option>`).join('')}
              </select>
            </div>
          </div>

          <div class="field">
            <label>Descrição do Risco Identificado</label>
            <textarea rows="2" data-create-field="description" data-risk-index="${idx}" placeholder="Ex: Queda de altura com diferença de nível superior a 2 metros">${escapeHTML(risk.description)}</textarea>
          </div>

          <div class="field">
            <label>Medidas de Controle</label>
            <div class="add-item-row">
              <input type="text" data-create-add="controlMeasures" data-risk-index="${idx}" placeholder="Ex: Uso de cinto tipo paraquedista">
              <button type="button" class="btn-add" data-create-add-btn="controlMeasures" data-risk-index="${idx}">+</button>
            </div>
            <ul class="chip-list">
              ${risk.controlMeasures.map((item, i) => `<li><span>${escapeHTML(item)}</span><button type="button" class="chip-remove" data-create-remove-item="controlMeasures" data-risk-index="${idx}" data-item-index="${i}">&times;</button></li>`).join('')}
            </ul>
          </div>

          <div class="field">
            <label>EPIs Relacionados</label>
            <div class="add-item-row">
              <input type="text" data-create-add="epis" data-risk-index="${idx}" placeholder="Ex: Cinto de segurança tipo paraquedista">
              <button type="button" class="btn-add" data-create-add-btn="epis" data-risk-index="${idx}">+</button>
            </div>
            <ul class="chip-list">
              ${risk.epis.map((item, i) => `<li><span>${escapeHTML(item)}</span><button type="button" class="chip-remove" data-create-remove-item="epis" data-risk-index="${idx}" data-item-index="${i}">&times;</button></li>`).join('')}
            </ul>
          </div>
        </div>
      `;
    }).join('');

    attachCreateRiskEvents();
  }

  function attachCreateRiskEvents() {
    createRisksContainer.querySelectorAll('[data-create-field]').forEach(el => {
      const eventName = el.tagName === 'TEXTAREA' ? 'input' : 'change';
      el.addEventListener(eventName, () => {
        const idx = parseInt(el.getAttribute('data-risk-index'), 10);
        const field = el.getAttribute('data-create-field');
        const risk = createDraft.risks[idx];

        if (field === 'severity' || field === 'probability') {
          risk[field] = parseInt(el.value, 10);
          const { level, label } = calculateRiskLevel(risk.probability, risk.severity);
          const badgeEl = document.getElementById(`createBadgePreview-${idx}`);
          if (badgeEl) {
            badgeEl.textContent = label;
            badgeEl.style.background = levelColor(level);
          }
        } else if (field === 'description') {
          risk.description = el.value;
        } else if (field === 'riskType') {
          risk.riskType = el.value;
        }
      });
    });

    createRisksContainer.querySelectorAll('[data-create-remove-risk]').forEach(btn => {
      btn.addEventListener('click', () => {
        const idx = parseInt(btn.getAttribute('data-create-remove-risk'), 10);
        createDraft.risks.splice(idx, 1);
        renderCreateRisks();
      });
    });

    createRisksContainer.querySelectorAll('[data-create-add-btn]').forEach(btn => {
      const field = btn.getAttribute('data-create-add-btn');
      const idx = parseInt(btn.getAttribute('data-risk-index'), 10);
      const input = createRisksContainer.querySelector(`input[data-create-add="${field}"][data-risk-index="${idx}"]`);
      const commit = () => {
        const value = input.value.trim();
        if (!value) return;
        createDraft.risks[idx][field].push(value);
        renderCreateRisks();
      };
      btn.addEventListener('click', commit);
      input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') { e.preventDefault(); commit(); }
      });
    });

    createRisksContainer.querySelectorAll('[data-create-remove-item]').forEach(btn => {
      btn.addEventListener('click', () => {
        const field = btn.getAttribute('data-create-remove-item');
        const idx = parseInt(btn.getAttribute('data-risk-index'), 10);
        const itemIdx = parseInt(btn.getAttribute('data-item-index'), 10);
        createDraft.risks[idx][field].splice(itemIdx, 1);
        renderCreateRisks();
      });
    });
  }

  btnAddCreateRisk.addEventListener('click', () => {
    createDraft.risks.push(emptyRisk());
    renderCreateRisks();
  });

  // ------------------------------------------------------------------
  // MODAL DE CRIAÇÃO: LISTAS EM NÍVEL DE ATIVIDADE (Coletivas / Procedimentos)
  // ------------------------------------------------------------------
  const createListConfig = {
    collective: { input: collectiveInput, listEl: collectiveList, data: createDraft.collective },
    procedures: { input: procedureInput, listEl: proceduresList, data: createDraft.procedures }
  };

  function addCreateSimpleItem(target) {
    const cfg = createListConfig[target];
    const value = cfg.input.value.trim();
    if (!value) return;
    cfg.data.push(value);
    cfg.input.value = '';
    renderCreateSimpleList(target);
  }

  function removeCreateSimpleItem(target, index) {
    createListConfig[target].data.splice(index, 1);
    renderCreateSimpleList(target);
  }

  function renderCreateSimpleList(target) {
    const cfg = createListConfig[target];
    cfg.listEl.innerHTML = '';
    cfg.data.forEach((item, index) => {
      const li = document.createElement('li');
      const span = document.createElement('span');
      span.textContent = item;
      const removeBtn = document.createElement('button');
      removeBtn.type = 'button';
      removeBtn.className = 'chip-remove';
      removeBtn.innerHTML = '&times;';
      removeBtn.addEventListener('click', () => removeCreateSimpleItem(target, index));
      li.appendChild(span);
      li.appendChild(removeBtn);
      cfg.listEl.appendChild(li);
    });
  }

  document.getElementById('btnAddCollective').addEventListener('click', () => addCreateSimpleItem('collective'));
  collectiveInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') { e.preventDefault(); addCreateSimpleItem('collective'); }
  });

  document.getElementById('btnAddProcedure').addEventListener('click', () => addCreateSimpleItem('procedures'));
  procedureInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') { e.preventDefault(); addCreateSimpleItem('procedures'); }
  });

  // ------------------------------------------------------------------
  // CÁLCULO DA MATRIZ DE RISCO (preview no cliente; o valor salvo no
  // banco é sempre recalculado no servidor, ver Model::calcularNivel)
  // ------------------------------------------------------------------
  function calculateRiskLevel(probability, severity) {
    const score = probability * severity;
    if (score >= 15) return { level: 'critico', label: 'Crítico' };
    if (score >= 9)  return { level: 'alto', label: 'Alto' };
    if (score >= 4)  return { level: 'medio', label: 'Médio' };
    return { level: 'baixo', label: 'Baixo' };
  }

  // ------------------------------------------------------------------
  // SUBMISSÃO DO FORMULÁRIO (CRIAÇÃO — persiste no banco)
  // ------------------------------------------------------------------
  riskForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const activityName = document.getElementById('activityName').value.trim();
    if (!activityName) {
      alert('Por favor, informe o nome da atividade.');
      return;
    }

    const idNr = activityNrSelect.value;
    if (!idNr) {
      alert('Por favor, selecione a NR (Norma Regulamentadora) da atividade.');
      return;
    }

    const validRisks = createDraft.risks.filter(r => r.description.trim());
    if (validRisks.length === 0) {
      alert('Adicione pelo menos um risco com a descrição preenchida.');
      return;
    }

    const payload = {
      nomeAtividade: activityName,
      idNr: parseInt(idNr, 10),
      riscos: validRisks.map(mapRiscoToPayload),
      medidasColetivas: createDraft.collective,
      procedimentos: createDraft.procedures
    };

    const submitBtn = riskForm.querySelector('.btn-submit');
    submitBtn.disabled = true;
    try {
      await apiRequest('criar', { method: 'POST', body: payload });

      riskForm.reset();
      createDraft.risks = [emptyRisk()];
      createDraft.collective.length = 0;
      createDraft.procedures.length = 0;
      renderCreateRisks();
      renderCreateSimpleList('collective');
      renderCreateSimpleList('procedures');

      await carregarFichas();
      closeModal();
      showDashboard();
    } catch (err) {
      alert(err.message);
    } finally {
      submitBtn.disabled = false;
    }
  });

  // ------------------------------------------------------------------
  // CARREGAMENTO E RENDERIZAÇÃO: DASHBOARD (ÁREA A — Cards por Atividade)
  // ------------------------------------------------------------------
  async function carregarFichas() {
    let fichas;
    try {
      fichas = await apiRequest('listar');
    } catch (err) {
      alert(err.message);
      fichas = [];
    }

    state.activities = {};
    fichas.forEach(f => { state.activities[f.id_atividade] = f; });
    renderDashboard();
  }

  function renderDashboard() {
    const ids = Object.keys(state.activities);
    cardsGrid.innerHTML = '';

    if (ids.length === 0) {
      cardsGrid.appendChild(emptyState);
      return;
    }

    ids.forEach(id => {
      cardsGrid.appendChild(buildActivityCard(state.activities[id]));
    });
  }

  function buildActivityCard(activity) {
    const card = document.createElement('div');
    card.className = 'activity-card';

    const levelOrder = [
      ['Crítico', 'critico', 'Críticos'],
      ['Alto', 'alto', 'Altos'],
      ['Médio', 'medio', 'Médios'],
      ['Baixo', 'baixo', 'Baixos']
    ];

    let badgesHTML = '';
    levelOrder.forEach(([label, slug, plural]) => {
      const count = activity.niveis[label] || 0;
      if (count > 0) {
        badgesHTML += `<span class="badge badge-${slug}">${count} ${count > 1 ? plural : label}</span>`;
      }
    });

    card.innerHTML = `
      <div class="activity-card-header-row">
        <div class="activity-card-titles">
          <h3 class="activity-card-title">${escapeHTML(activity.nome)}</h3>
          <p class="activity-card-subtitle">${activity.totalRiscos} risco${activity.totalRiscos !== 1 ? 's' : ''} identificado${activity.totalRiscos !== 1 ? 's' : ''}</p>
        </div>
        <div class="card-actions">
          <button type="button" class="edit-icon-btn" title="Editar ficha" aria-label="Editar informações da ficha">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5z"></path>
            </svg>
          </button>
          <button type="button" class="delete-icon-btn" title="Excluir ficha" aria-label="Excluir esta ficha de risco">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="3 6 5 6 21 6"></polyline>
              <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>
              <path d="M10 11v6"></path>
              <path d="M14 11v6"></path>
            </svg>
          </button>
        </div>
      </div>
      <div class="badge-row">${badgesHTML}</div>
      <div class="stats-row">
        <span class="stat-item">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
          ${activity.totalMedidas} Medidas Coletivas
        </span>
        <span class="stat-item">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="8" y="2" width="8" height="4" rx="1"></rect><path d="M9 4H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-4"></path></svg>
          ${activity.totalProcedimentos} Procedimentos
        </span>
      </div>
      <button type="button" class="btn-view-full">Ver Ficha Completa</button>
    `;

    card.querySelector('.edit-icon-btn').addEventListener('click', (e) => {
      e.stopPropagation();
      openEditModal(activity.id_atividade);
    });

    card.querySelector('.delete-icon-btn').addEventListener('click', (e) => {
      e.stopPropagation();
      deleteActivity(activity.id_atividade);
    });

    card.querySelector('.btn-view-full').addEventListener('click', () => {
      showDetail(activity.id_atividade);
    });

    return card;
  }

  // ------------------------------------------------------------------
  // EXCLUSÃO DE FICHA DE ATIVIDADE (persiste no banco)
  // ------------------------------------------------------------------
  async function deleteActivity(idAtividade) {
    const activity = state.activities[idAtividade];
    const nome = activity ? activity.nome : 'esta ficha';

    const confirmed = confirm(`Tem certeza que deseja excluir a ficha "${nome}"? Esta ação não pode ser desfeita.`);
    if (!confirmed) return;

    try {
      await apiRequest('excluir', { method: 'POST', params: { id_atividade: idAtividade } });

      if (state.currentDetailActivity === idAtividade) {
        showDashboard();
      }
      await carregarFichas();
    } catch (err) {
      alert(err.message);
    }
  }

  // ------------------------------------------------------------------
  // RENDERIZAÇÃO: DETALHAMENTO (ÁREA B — Ficha Completa)
  // ------------------------------------------------------------------
  async function showDetail(idAtividade) {
    let ficha;
    try {
      ficha = await apiRequest('buscar', { params: { id_atividade: idAtividade } });
    } catch (err) {
      alert(err.message);
      return;
    }

    state.currentDetailActivity = idAtividade;

    detailActivityName.textContent = ficha.nome;
    risksList.innerHTML = '';

    ficha.riscos.map(mapRiscoFromServer).forEach((risk, idx) => {
      risksList.appendChild(buildRiskBlock(risk, idx));
    });

    collectiveMeasuresOutput.innerHTML = buildSimpleList(ficha.medidasColetivas, 'green');
    proceduresOutput.innerHTML = buildSimpleList(ficha.procedimentos, 'blue');

    dashboardView.classList.add('hidden');
    detailView.classList.remove('hidden');
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  function buildRiskBlock(risk, idx) {
    const block = document.createElement('div');
    block.className = 'risk-block';

    block.innerHTML = `
      <div class="risk-block-badges">
        <span class="badge-category" style="background:#6b7280;">${escapeHTML(risk.riskType)}</span>
        <span class="badge-category badge-${risk.level}" style="background:${levelColor(risk.level)};">${risk.levelLabel}</span>
      </div>
      <p class="risk-block-description">${escapeHTML(risk.description)}</p>
      <div class="risk-block-values">
        <span>Probabilidade: ${risk.probability}/5</span>
        <span>Severidade: ${risk.severity}/5</span>
      </div>
      <div class="accordion">
        <button type="button" class="accordion-header" aria-expanded="false" data-accordion="control-${idx}">
          <span>Medidas de Controle (${risk.controlMeasures.length})</span>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
        </button>
        <div class="accordion-panel" id="panel-control-${idx}-${risk.id_risco}">
          <ul>${risk.controlMeasures.length ? risk.controlMeasures.map(m => `<li>${escapeHTML(m)}</li>`).join('') : '<li>Nenhuma medida cadastrada</li>'}</ul>
        </div>
      </div>
      <div class="accordion">
        <button type="button" class="accordion-header" aria-expanded="false" data-accordion="epi-${idx}">
          <span>EPIs Relacionados (${risk.epis.length})</span>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
        </button>
        <div class="accordion-panel" id="panel-epi-${idx}-${risk.id_risco}">
          <ul>${risk.epis.length ? risk.epis.map(m => `<li>${escapeHTML(m)}</li>`).join('') : '<li>Nenhum EPI cadastrado</li>'}</ul>
        </div>
      </div>
    `;

    block.querySelectorAll('.accordion-header').forEach(header => {
      header.addEventListener('click', () => {
        const panel = header.nextElementSibling;
        const isOpen = panel.classList.contains('open');
        panel.classList.toggle('open', !isOpen);
        header.setAttribute('aria-expanded', String(!isOpen));
      });
    });

    return block;
  }

  function buildSimpleList(items, colorType) {
    if (!items || items.length === 0) {
      return `<p class="list-empty">Nenhum item cadastrado para esta atividade ainda.</p>`;
    }
    const icon = colorType === 'green'
      ? `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>`
      : `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="3"></rect><polyline points="8 12 11 15 16 9"></polyline></svg>`;

    return items.map(item => `
      <div class="list-row">
        ${icon}
        <span>${escapeHTML(item)}</span>
      </div>
    `).join('');
  }

  function levelColor(level) {
    const colors = { critico: '#ef4444', alto: '#f97316', medio: '#f59e0b', baixo: '#10b981' };
    return colors[level] || '#6b7280';
  }

  // ------------------------------------------------------------------
  // NAVEGAÇÃO ENTRE VIEWS
  // ------------------------------------------------------------------
  btnVoltarDashboard.addEventListener('click', showDashboard);

  function showDashboard() {
    detailView.classList.add('hidden');
    dashboardView.classList.remove('hidden');
    state.currentDetailActivity = null;
  }

  // ------------------------------------------------------------------
  // UTILITÁRIO: sanitização simples de texto inserido pelo usuário
  // ------------------------------------------------------------------
  function escapeHTML(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
  }

  // ------------------------------------------------------------------
  // INICIALIZAÇÃO
  // ------------------------------------------------------------------
  renderCreateRisks();
  carregarNRs();     // popula o select de NR do formulário
  carregarFichas();  // busca as fichas reais do banco via API

});