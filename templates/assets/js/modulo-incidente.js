
  /* ------------------------------------------------------------------
     DADOS MOCK
  ------------------------------------------------------------------ */
  const INCIDENTS = [
    {
      id: 'INC-001',
      severidade: 'media',
      status: 'investigando',
      descricao: 'Trabalhador quase sofreu queda ao pisar em prancha solta no andaime. O andaime não estava adequadamente travado.',
      data: '2025-12-10 às 14:30',
      local: 'Pavimento 3 - Torre A',
      atividade: 'Trabalho em Altura',
      tipo: 'Quase Acidente',
      responsavel: 'Eng. Carlos Mendes',
      testemunhas: 'João Silva, Pedro Santos',
      acaoImediata: 'Paralisação imediata do andaime, travamento correto das pranchas e reforço da estrutura.',
    },
    {
      id: 'INC-002',
      severidade: 'baixa',
      status: 'concluido',
      descricao: 'Materiais armazenados de forma inadequada, bloqueando saída de emergência.',
      data: '2025-12-08 às 10:15',
      local: 'Almoxarifado',
      atividade: 'Alvenaria',
      tipo: 'Condição Insegura',
      responsavel: 'Eng. Ana Souza',
      testemunhas: 'Marcos Lima',
      acaoImediata: 'Remoção imediata dos materiais e reorganização do espaço de armazenagem.',
    },
    {
      id: 'INC-003',
      severidade: 'baixa',
      status: 'concluido',
      descricao: 'Carpinteiro sofreu corte superficial na mão ao manusear serra circular. Estava sem luvas adequadas.',
      data: '2025-12-05 às 16:45',
      local: 'Subsolo - Área de Carpintaria',
      atividade: 'Carpintaria',
      tipo: 'Acidente com Lesão',
      responsavel: 'Eng. Roberto Faria',
      testemunhas: 'Carlos Oliveira, Fábio Nunes',
      acaoImediata: 'Primeiros socorros aplicados no local, fornecimento de luvas adequadas e reforço do treinamento de EPI.',
    },
    {
      id: 'INC-004',
      severidade: 'alta',
      status: 'concluido',
      descricao: 'Painel elétrico encontrado aberto e energizado sem sinalização adequada.',
      data: '2025-12-03 às 09:20',
      local: 'Pavimento 5',
      atividade: 'Eletricidade',
      tipo: 'Risco Elétrico',
      responsavel: 'Eng. Patrícia Costa',
      testemunhas: 'Rafael Mendes',
      acaoImediata: 'Desenergização imediata do painel, instalação de sinalização e bloqueio de acesso à área.',
    },
  ];

  /* ------------------------------------------------------------------
     MAPEAMENTOS
  ------------------------------------------------------------------ */
  const SEVERIDADE_LABEL = { alta: 'Alta', media: 'Média', baixa: 'Baixa' };
  const SEVERIDADE_CLASS  = { alta: 'badge--alta', media: 'badge--media', baixa: 'badge--baixa' };
  const STATUS_LABEL      = { investigando: 'Investigando', concluido: 'Concluído' };
  const STATUS_CLASS      = { investigando: 'badge--investigando', concluido: 'badge--concluido' };

  /* ------------------------------------------------------------------
     NAVEGAÇÃO SPA
  ------------------------------------------------------------------ */
  function showPage(id) {
    document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
    document.getElementById(id).classList.add('active');
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  /* ------------------------------------------------------------------
     ABRIR DETALHE
  ------------------------------------------------------------------ */
  function openDetail(inc) {
    // Preenche campos
    document.getElementById('detailId').textContent = inc.id;
    document.getElementById('detailDate').textContent = inc.data;

    const badgeSev = document.getElementById('detailBadgeSev');
    badgeSev.textContent  = SEVERIDADE_LABEL[inc.severidade];
    badgeSev.className    = `badge ${SEVERIDADE_CLASS[inc.severidade]}`;

    const badgeStatus = document.getElementById('detailBadgeStatus');
    badgeStatus.textContent = STATUS_LABEL[inc.status];
    badgeStatus.className   = `badge ${STATUS_CLASS[inc.status]}`;

    document.getElementById('detailTipo').textContent        = inc.tipo;
    document.getElementById('detailLocal').textContent       = inc.local;
    document.getElementById('detailAtividade').textContent   = inc.atividade;
    document.getElementById('detailResponsavel').textContent = inc.responsavel;
    document.getElementById('detailDescricao').textContent   = inc.descricao;
    document.getElementById('detailTestemunhas').textContent = inc.testemunhas;
    document.getElementById('detailAcao').textContent        = inc.acaoImediata;

    showPage('page-detail');
  }

  /* ------------------------------------------------------------------
     VOLTAR À LISTA
  ------------------------------------------------------------------ */
  document.getElementById('btnBackToList').addEventListener('click', () => {
    showPage('page-list');
  });

  /* ------------------------------------------------------------------
     RENDERIZAÇÃO DOS CARDS
  ------------------------------------------------------------------ */
  function renderIncidents(list) {
    const container = document.getElementById('incidentsList');
    const empty     = document.getElementById('emptyState');
    container.innerHTML = '';

    if (list.length === 0) {
      empty.style.display = 'block';
      return;
    }
    empty.style.display = 'none';

    list.forEach(inc => {
      const card = document.createElement('article');
      card.className = 'incident-card';
      card.innerHTML = `
        <div class="incident-card__body">
          <div class="incident-card__top">
            <span class="incident-id">${inc.id}</span>
            <span class="badge ${SEVERIDADE_CLASS[inc.severidade]}">${SEVERIDADE_LABEL[inc.severidade]}</span>
            <span class="badge ${STATUS_CLASS[inc.status]}">${STATUS_LABEL[inc.status]}</span>
          </div>
          <p class="incident-card__desc">${inc.descricao}</p>
          <div class="incident-card__meta">
            <span>${inc.data}</span>
            <span>Local: ${inc.local}</span>
            <span>Atividade: ${inc.atividade}</span>
          </div>
        </div>
        <button class="btn-details" aria-label="Ver detalhes do incidente ${inc.id}">
          Ver Detalhes
        </button>
      `;

      card.querySelector('.btn-details').addEventListener('click', () => openDetail(inc));
      container.appendChild(card);
    });
  }

  /* ------------------------------------------------------------------
     FILTROS
  ------------------------------------------------------------------ */
  function applyFilters() {
    const query  = document.getElementById('searchInput').value.toLowerCase().trim();
    const status = document.getElementById('statusFilter').value;

    const filtered = INCIDENTS.filter(inc => {
      const matchesSearch =
        !query ||
        inc.descricao.toLowerCase().includes(query) ||
        inc.local.toLowerCase().includes(query) ||
        inc.id.toLowerCase().includes(query);
      const matchesStatus = !status || inc.status === status;
      return matchesSearch && matchesStatus;
    });

    renderIncidents(filtered);
  }

  document.getElementById('searchInput').addEventListener('input', applyFilters);
  document.getElementById('statusFilter').addEventListener('change', applyFilters);

  /* ------------------------------------------------------------------
     INIT
  ------------------------------------------------------------------ */
  renderIncidents(INCIDENTS);
