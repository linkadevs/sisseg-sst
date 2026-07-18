// ==========================================================================
// PGR — Gerador e Simulador de Cards de Risco
// Estado em memória, cálculo de matriz de risco e renderização dinâmica
// ==========================================================================

document.addEventListener('DOMContentLoaded', () => {

  // ------------------------------------------------------------------
  // ESTADO DA APLICAÇÃO
  // ------------------------------------------------------------------
  // activities: { [nomeAtividade]: { name, riscos: [], medidasColetivas: [], procedimentos: [] } }
  const state = {
    activities: {},
    currentDetailActivity: null
  };

  // Estado do modal de edição (nulo quando fechado)
  let editState = null;

  // Estado do modal de criação: suporta múltiplos riscos por ficha
  function emptyRisk() {
    return {
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

  // Fecha ao clicar fora do card do modal
  modalOverlay.addEventListener('click', (e) => {
    if (e.target === modalOverlay) closeModal();
  });

  // Fecha com a tecla Esc (fecha o modal que estiver aberto)
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
  function openEditModal(activityName) {
    const activity = state.activities[activityName];
    if (!activity) return;

    // Clona os dados da atividade para um rascunho editável independente
    editState = {
      originalName: activityName,
      name: activity.name,
      riscos: activity.riscos.map(r => ({
        ...r,
        controlMeasures: [...r.controlMeasures],
        epis: [...r.epis]
      })),
      medidasColetivas: [...activity.medidasColetivas],
      procedimentos: [...activity.procedimentos]
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
    const nameToDelete = editState.originalName;
    closeEditModal();
    deleteActivity(nameToDelete);
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
        <input type="text" id="editActivityName" value="${escapeHTML(editState.name)}">
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

    // Botão "Adicionar Risco": insere um novo bloco de risco em branco
    document.getElementById('btnAddEditRisk').addEventListener('click', () => {
      const { level, label } = calculateRiskLevel(4, 4);
      editState.riscos.push({
        id: `risk-${Date.now()}-${Math.floor(Math.random() * 1000)}`,
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

    // Campos de cada bloco de risco (tipo, severidade, probabilidade, descrição)
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

    // Remover um risco inteiro do rascunho
    editModalBody.querySelectorAll('[data-remove-risk]').forEach(btn => {
      btn.addEventListener('click', () => {
        const idx = parseInt(btn.getAttribute('data-remove-risk'), 10);
        editState.riscos.splice(idx, 1);
        renderEditModalBody();
      });
    });

    // Adicionar item às listas de cada risco (medidas de controle / EPIs)
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

    // Remover item das listas de cada risco
    editModalBody.querySelectorAll('[data-risk-remove-item]').forEach(btn => {
      btn.addEventListener('click', () => {
        const field = btn.getAttribute('data-risk-remove-item');
        const idx = parseInt(btn.getAttribute('data-risk-index'), 10);
        const itemIdx = parseInt(btn.getAttribute('data-item-index'), 10);
        editState.riscos[idx][field].splice(itemIdx, 1);
        renderEditModalBody();
      });
    });

    // Listas em nível de atividade: Medidas Coletivas
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

    // Listas em nível de atividade: Procedimentos Obrigatórios
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

    // Remover itens das listas em nível de atividade
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
  // MODAL DE EDIÇÃO: SALVAR ALTERAÇÕES
  // ------------------------------------------------------------------
  editModalSave.addEventListener('click', () => {
    if (!editState) return;

    const newName = editState.name.trim();
    if (!newName) {
      alert('Por favor, informe o nome da atividade.');
      return;
    }

    const renamed = newName !== editState.originalName;
    if (renamed && state.activities[newName]) {
      alert('Já existe uma atividade com esse nome. Escolha outro nome.');
      return;
    }

    const updatedActivity = {
      name: newName,
      riscos: editState.riscos,
      medidasColetivas: editState.medidasColetivas,
      procedimentos: editState.procedimentos
    };

    delete state.activities[editState.originalName];
    state.activities[newName] = updatedActivity;

    const wasViewingThis = state.currentDetailActivity === editState.originalName;

    closeEditModal();
    renderDashboard();

    // Se o usuário estava vendo a ficha completa desta atividade, atualiza a view
    if (wasViewingThis) {
      showDetail(newName);
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
    // Campos de cada bloco de risco (tipo, severidade, probabilidade, descrição)
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

    // Remover um bloco de risco do rascunho de criação
    createRisksContainer.querySelectorAll('[data-create-remove-risk]').forEach(btn => {
      btn.addEventListener('click', () => {
        const idx = parseInt(btn.getAttribute('data-create-remove-risk'), 10);
        createDraft.risks.splice(idx, 1);
        renderCreateRisks();
      });
    });

    // Adicionar item às listas de cada risco (medidas de controle / EPIs)
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

    // Remover item das listas de cada risco
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

  // Botão "Adicionar Risco" do modal de criação
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
  // CÁLCULO DA MATRIZ DE RISCO (Probabilidade x Severidade)
  // ------------------------------------------------------------------
  function calculateRiskLevel(probability, severity) {
    const score = probability * severity;
    if (score >= 15) return { level: 'critico', label: 'Crítico' };
    if (score >= 9)  return { level: 'alto', label: 'Alto' };
    if (score >= 4)  return { level: 'medio', label: 'Médio' };
    return { level: 'baixo', label: 'Baixo' };
  }

  // ------------------------------------------------------------------
  // SUBMISSÃO DO FORMULÁRIO (CRIAÇÃO)
  // ------------------------------------------------------------------
  riskForm.addEventListener('submit', (e) => {
    e.preventDefault();

    const activityName = document.getElementById('activityName').value.trim();
    if (!activityName) {
      alert('Por favor, informe o nome da atividade.');
      return;
    }

    // Apenas riscos com descrição preenchida são considerados válidos
    const validRisks = createDraft.risks.filter(r => r.description.trim());
    if (validRisks.length === 0) {
      alert('Adicione pelo menos um risco com a descrição preenchida.');
      return;
    }

    // Cria a atividade caso não exista, ou anexa a um registro existente
    if (!state.activities[activityName]) {
      state.activities[activityName] = {
        name: activityName,
        riscos: [],
        medidasColetivas: [],
        procedimentos: []
      };
    }
    const activity = state.activities[activityName];

    validRisks.forEach(r => {
      const { level, label } = calculateRiskLevel(r.probability, r.severity);
      activity.riscos.push({
        id: `risk-${Date.now()}-${Math.floor(Math.random() * 1000)}`,
        riskType: r.riskType,
        severity: r.severity,
        probability: r.probability,
        description: r.description.trim(),
        level,
        levelLabel: label,
        controlMeasures: [...r.controlMeasures],
        epis: [...r.epis]
      });
    });

    // Anexa medidas coletivas e procedimentos (evitando duplicados exatos)
    createDraft.collective.forEach(item => {
      if (!activity.medidasColetivas.includes(item)) activity.medidasColetivas.push(item);
    });
    createDraft.procedures.forEach(item => {
      if (!activity.procedimentos.includes(item)) activity.procedimentos.push(item);
    });

    // Reset do formulário e do rascunho de criação
    riskForm.reset();
    createDraft.risks = [emptyRisk()];
    createDraft.collective.length = 0;
    createDraft.procedures.length = 0;
    renderCreateRisks();
    renderCreateSimpleList('collective');
    renderCreateSimpleList('procedures');

    renderDashboard();

    // Fecha o modal e mantém o usuário no dashboard para ver o novo card
    closeModal();
    showDashboard();
  });

  // ------------------------------------------------------------------
  // RENDERIZAÇÃO: DASHBOARD (ÁREA A — Cards por Atividade)
  // ------------------------------------------------------------------
  function renderDashboard() {
    const activityNames = Object.keys(state.activities);
    cardsGrid.innerHTML = '';

    if (activityNames.length === 0) {
      cardsGrid.appendChild(emptyState);
      return;
    }

    activityNames.forEach(name => {
      const activity = state.activities[name];
      cardsGrid.appendChild(buildActivityCard(activity));
    });
  }

  function buildActivityCard(activity) {
    const card = document.createElement('div');
    card.className = 'activity-card';

    // Agrega contagem de riscos por nível
    const levelCounts = { critico: 0, alto: 0, medio: 0, baixo: 0 };
    activity.riscos.forEach(r => levelCounts[r.level]++);

    const levelLabels = { critico: 'Crítico', alto: 'Alto', medio: 'Médio', baixo: 'Baixo' };
    const levelOrder = ['critico', 'alto', 'medio', 'baixo'];

    let badgesHTML = '';
    levelOrder.forEach(level => {
      const count = levelCounts[level];
      if (count > 0) {
        const plural = count > 1 ? (level === 'critico' ? 'Críticos' : level === 'alto' ? 'Altos' : level === 'medio' ? 'Médios' : 'Baixos') : levelLabels[level];
        badgesHTML += `<span class="badge badge-${level}">${count} ${plural}</span>`;
      }
    });

    card.innerHTML = `
      <div class="activity-card-header-row">
        <div class="activity-card-titles">
          <h3 class="activity-card-title">${escapeHTML(activity.name)}</h3>
          <p class="activity-card-subtitle">${activity.riscos.length} risco${activity.riscos.length !== 1 ? 's' : ''} identificado${activity.riscos.length !== 1 ? 's' : ''}</p>
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
          ${activity.medidasColetivas.length} Medidas Coletivas
        </span>
        <span class="stat-item">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="8" y="2" width="8" height="4" rx="1"></rect><path d="M9 4H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-4"></path></svg>
          ${activity.procedimentos.length} Procedimentos
        </span>
      </div>
      <button type="button" class="btn-view-full">Ver Ficha Completa</button>
    `;

    card.querySelector('.edit-icon-btn').addEventListener('click', (e) => {
      e.stopPropagation();
      openEditModal(activity.name);
    });

    card.querySelector('.delete-icon-btn').addEventListener('click', (e) => {
      e.stopPropagation();
      deleteActivity(activity.name);
    });

    card.querySelector('.btn-view-full').addEventListener('click', () => {
      showDetail(activity.name);
    });

    return card;
  }

  // ------------------------------------------------------------------
  // EXCLUSÃO DE FICHA DE ATIVIDADE
  // ------------------------------------------------------------------
  function deleteActivity(activityName) {
    const activity = state.activities[activityName];
    if (!activity) return;

    const confirmed = confirm(`Tem certeza que deseja excluir a ficha "${activityName}"? Esta ação não pode ser desfeita.`);
    if (!confirmed) return;

    delete state.activities[activityName];

    // Se o usuário estava visualizando a ficha completa desta atividade, volta ao dashboard
    if (state.currentDetailActivity === activityName) {
      showDashboard();
    }

    renderDashboard();
  }

  // ------------------------------------------------------------------
  // RENDERIZAÇÃO: DETALHAMENTO (ÁREA B — Ficha Completa)
  // ------------------------------------------------------------------
  function showDetail(activityName) {
    state.currentDetailActivity = activityName;
    const activity = state.activities[activityName];
    if (!activity) return;

    detailActivityName.textContent = activity.name;
    risksList.innerHTML = '';

    activity.riscos.forEach((risk, idx) => {
      risksList.appendChild(buildRiskBlock(risk, idx));
    });

    collectiveMeasuresOutput.innerHTML = buildSimpleList(activity.medidasColetivas, 'green');
    proceduresOutput.innerHTML = buildSimpleList(activity.procedimentos, 'blue');

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
        <div class="accordion-panel" id="panel-control-${idx}-${risk.id}">
          <ul>${risk.controlMeasures.length ? risk.controlMeasures.map(m => `<li>${escapeHTML(m)}</li>`).join('') : '<li>Nenhuma medida cadastrada</li>'}</ul>
        </div>
      </div>
      <div class="accordion">
        <button type="button" class="accordion-header" aria-expanded="false" data-accordion="epi-${idx}">
          <span>EPIs Relacionados (${risk.epis.length})</span>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
        </button>
        <div class="accordion-panel" id="panel-epi-${idx}-${risk.id}">
          <ul>${risk.epis.length ? risk.epis.map(m => `<li>${escapeHTML(m)}</li>`).join('') : '<li>Nenhum EPI cadastrado</li>'}</ul>
        </div>
      </div>
    `;

    // Liga eventos dos accordions deste bloco
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
  // DADOS DE EXEMPLO (opcional — demonstra o funcionamento ao carregar)
  // ------------------------------------------------------------------
  function seedExample() {
    state.activities['Trabalho em Altura'] = {
      name: 'Trabalho em Altura',
      riscos: [
        {
          id: 'seed-1',
          riskType: 'Acidente',
          severity: 5,
          probability: 4,
          description: 'Queda de altura com diferença de nível superior a 2 metros',
          level: 'critico',
          levelLabel: 'Crítico',
          controlMeasures: ['Uso de cinto tipo paraquedista', 'Ancoragem em ponto fixo', 'Inspeção diária dos EPIs', 'Sinalização da área de risco'],
          epis: ['Cinto de segurança tipo paraquedista', 'Capacete com jugular', 'Talabarte duplo com absorvedor']
        },
        {
          id: 'seed-2',
          riskType: 'Acidente',
          severity: 4,
          probability: 3,
          description: 'Queda de materiais e ferramentas',
          level: 'alto',
          levelLabel: 'Alto',
          controlMeasures: ['Uso de bolsa porta-ferramentas', 'Isolamento da área abaixo', 'Amarração de ferramentas', 'Tela de proteção'],
          epis: ['Capacete com jugular']
        }
      ],
      medidasColetivas: ['Guarda-corpo rígido nos perímetros', 'Tela de proteção em toda a fachada', 'Plataforma de proteção a cada 3 pavimentos', 'Sistema de ancoragem permanente'],
      procedimentos: ['Análise Preliminar de Risco (APR) diária', 'Permissão de Trabalho (PT) assinada', 'Inspeção de EPIs antes do início', 'Treinamento NR-35 atualizado']
    };
    renderDashboard();
  }

  // ------------------------------------------------------------------
  // INICIALIZAÇÃO
  // ------------------------------------------------------------------
  renderCreateRisks();
  renderDashboard();
  seedExample(); // Remova esta chamada caso não deseje o exemplo pré-carregado

});
