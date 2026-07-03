const cursos = [
    { id:1, nr:'NR-06', titulo:'EPIs', status:'valido', desc:'Uso e responsabilidade de Equipamentos de Proteção Individual.', horas:'2 horas', temProva:true, img:'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?w=500&h=310&fit=crop' },
    { id:2, nr:'NR-10', titulo:'Eletricidade', status:'invalido', desc:'Segurança em instalações e serviços com eletricidade.', horas:'40 horas', temProva:false, img:'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?w=500&h=310&fit=crop' },
    { id:3, nr:'NR-11', titulo:'Movimentação de Cargas', status:'invalido', desc:'Transporte, movimentação, armazenagem e manuseio de materiais.', horas:'16 horas', temProva:true, img:'https://images.unsplash.com/photo-1553413077-190dd305871c?w=500&h=310&fit=crop' },
    { id:4, nr:'NR-12', titulo:'Segurança em Máquinas', status:'valido', desc:'Riscos, proteções e operação segura de máquinas e equipamentos.', horas:'8 horas', temProva:true, img:'https://images.unsplash.com/photo-1581092160607-ee22621dd758?w=500&h=310&fit=crop' },
    { id:5, nr:'NR-17', titulo:'Ergonomia', status:'valido', desc:'Adaptação das condições de trabalho às características do trabalhador.', horas:'4 horas', temProva:false, img:'https://images.unsplash.com/photo-1552664730-d307ca884978?w=500&h=310&fit=crop' },
    { id:6, nr:'NR-18', titulo:'Construção Civil', status:'invalido', desc:'Condições de segurança no canteiro de obras.', horas:'6 horas', temProva:true, img:'https://images.unsplash.com/photo-1541976590-713941681591?w=500&h=310&fit=crop' },
    { id:7, nr:'NR-20', titulo:'Inflamáveis e Combustíveis', status:'invalido', desc:'Segurança no trabalho com líquidos e gases inflamáveis.', horas:'8 horas', temProva:true, img:'https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=500&h=310&fit=crop' },
    { id:8, nr:'NR-23', titulo:'Proteção Contra Incêndio', status:'invalido', desc:'Prevenção e combate a incêndios no ambiente de trabalho.', horas:'4 horas', temProva:false, img:'https://images.unsplash.com/photo-1573497161161-c3e73707e25c?w=500&h=310&fit=crop' },
    { id:9, nr:'NR-26', titulo:'Sinalização de Segurança', status:'valido', desc:'Cores, símbolos e identificação de riscos.', horas:'2 horas', temProva:true, img:'https://images.unsplash.com/photo-1578575437130-527eed3abbec?w=500&h=310&fit=crop' },
    { id:10, nr:'NR-33', titulo:'Espaço Confinado', status:'invalido', desc:'Segurança em trabalhos em espaços confinados.', horas:'16 horas', temProva:true, img:'https://images.unsplash.com/photo-1504328345606-18bbc8c9d7d1?w=500&h=310&fit=crop' },
    { id:11, nr:'NR-35', titulo:'Trabalho em Altura', status:'invalido', desc:'Planejamento, organização e execução de trabalho em altura.', horas:'8 horas', temProva:true, img:'https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=500&h=310&fit=crop' },
    { id:12, nr:'NR-01', titulo:'Disposições Gerais', status:'valido', desc:'Princípios gerais de segurança e saúde no trabalho.', horas:'1 hora', temProva:false, img:'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=500&h=310&fit=crop' },
    { id:13, nr:'PGR', titulo:'Gerenciamento de Riscos', status:'valido', desc:'Programa de Gerenciamento de Riscos ocupacionais.', horas:'1 hora', temProva:false, img:'https://images.unsplash.com/photo-1503387762-592deb58ef4e?w=500&h=310&fit=crop' },
    { id:14, nr:'PCMSO', titulo:'Controle Médico', status:'valido', desc:'Programa de Controle Médico de Saúde Ocupacional.', horas:'1 hora', temProva:false, img:'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=500&h=310&fit=crop' },
    { id:15, nr:'APR', titulo:'Análise Preliminar de Risco', status:'invalido', desc:'Identificação de riscos antes do início das atividades.', horas:'2 horas', temProva:true, img:'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?w=500&h=310&fit=crop' },
    { id:16, nr:'FDS', titulo:'Produtos Químicos (FDS)', status:'valido', desc:'Ficha de Dados de Segurança e manuseio de químicos.', horas:'3 horas', temProva:true, img:'https://images.unsplash.com/photo-1532187863486-abf9dbad1b69?w=500&h=310&fit=crop' },
    { id:17, nr:'PT', titulo:'Permissão de Trabalho', status:'valido', desc:'Procedimentos para emissão e controle de PT.', horas:'2 horas', temProva:true, img:'https://images.unsplash.com/photo-1497366216548-37526070297c?w=500&h=310&fit=crop' },
    { id:18, nr:'5S', titulo:'Programa 5S', status:'valido', desc:'Metodologia de organização e disciplina no trabalho.', horas:'2 horas', temProva:true, img:'https://images.unsplash.com/photo-1587613864411-cbf6a9358350?w=500&h=310&fit=crop' },
  ];

  let nextId = 19;
  const IMG_PADRAO = 'https://images.unsplash.com/photo-1581094794329-c8112a89af12?w=500&h=310&fit=crop';

  function updateKpis() {
    const total = cursos.length;
    const validos = cursos.filter(c => c.status === 'valido').length;
    const invalidos = total - validos;
    document.getElementById('kpiTotal').textContent = total;
    document.getElementById('kpiValidos').textContent = validos;
    document.getElementById('kpiInvalidos').textContent = invalidos;
  }

  function renderCursos(lista) {
    const grid = document.getElementById('coursesGrid');
    grid.innerHTML = lista.map(c => `
      <div class="course-card">
        <div class="course-thumb">
          <img src="${c.img}" alt="${c.titulo}" loading="lazy">
          <span class="nr-badge">${c.nr}</span>
        </div>
        <div class="course-body">
          <div class="course-title-row">
            <h3>${c.titulo}</h3>
            <span class="status-chip ${c.status}">${c.status === 'valido' ? 'Válido' : 'Inválido'}</span>
          </div>
          <p class="course-desc">${c.desc}</p>
          <div class="course-meta">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <span>${c.horas}</span>
          </div>

          <button class="btn-editar" onclick="editarTreinamento(${c.id})">Editar</button>

          <div class="action-row">
            <button class="btn-outline-sm" onclick="${c.temProva ? `editarProva(${c.id})` : `criarProva(${c.id})`}">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
              ${c.temProva ? 'Editar prova' : 'Criar prova'}
            </button>
            <button class="btn-outline-sm" onclick="verVideoaulas(${c.id})">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg>
              Videoaulas
            </button>
          </div>
        </div>
      </div>
    `).join('');
  }

  function buscaAtual() {
    return document.getElementById('searchInput').value.trim().toLowerCase();
  }
  function rerenderComBusca() {
    const termo = buscaAtual();
    const lista = termo
      ? cursos.filter(c =>
          c.titulo.toLowerCase().includes(termo) ||
          c.desc.toLowerCase().includes(termo) ||
          c.nr.toLowerCase().includes(termo))
      : cursos;
    renderCursos(lista);
  }

  function criarProva(id) {
    const c = cursos.find(x => x.id === id);
    console.log(`[Criar prova] ${c.titulo}`);
    alert(`Criando prova para: ${c.titulo}`);
  }
  function editarProva(id) {
    const c = cursos.find(x => x.id === id);
    console.log(`[Editar prova] ${c.titulo}`);
    alert(`Editando prova de: ${c.titulo}`);
  }
  function verVideoaulas(id) {
    const c = cursos.find(x => x.id === id);
    console.log(`[Videoaulas] ${c.titulo}`);
    alert(`Abrindo videoaulas de: ${c.titulo}`);
  }

  document.getElementById('searchInput').addEventListener('input', rerenderComBusca);

  document.getElementById('btnFiltrar').addEventListener('click', function () {
    console.log('[Filtrar] abrir opções de filtro');
    alert('Opções de filtro em breve.');
  });

  renderCursos(cursos);
  updateKpis();

  // ===== Modal: Criar / Editar treinamento =====
  const modalOverlay = document.getElementById('modalOverlay');
  const modalTitle = document.getElementById('modalTitle');
  const imgUpload = document.getElementById('imgUpload');
  const inputImagem = document.getElementById('inputImagem');
  const preview = document.getElementById('imgUploadPreview');
  const removeBtn = document.getElementById('imgUploadRemove');
  const inputTitulo = document.getElementById('inputTitulo');
  const inputSubtitulo = document.getElementById('inputSubtitulo');
  const inputCarga = document.getElementById('inputCarga');
  const inputValidade = document.getElementById('inputValidade');
  const toggleSemValidade = document.getElementById('toggleSemValidade');
  const selectNR = document.getElementById('selectNR');

  let editingId = null; // null = criando novo treinamento

  function limparFormulario() {
    inputTitulo.value = '';
    inputSubtitulo.value = '';
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
    limparFormulario();
    modalOverlay.classList.add('open');
  }

  function editarTreinamento(id) {
    const c = cursos.find(x => x.id === id);
    if (!c) return;
    editingId = id;
    modalTitle.textContent = 'Editar treinamento';
    inputTitulo.value = c.titulo;
    inputSubtitulo.value = c.desc;
    inputCarga.value = c.horas.replace(/\s*horas?$/i, '');
    inputValidade.value = c.validade || '';
    toggleSemValidade.checked = !c.validade;
    inputValidade.disabled = toggleSemValidade.checked;
    // Seleciona a NR correspondente no select, se existir
    let achou = false;
    for (const opt of selectNR.options) {
      if (opt.textContent.startsWith(c.nr)) { selectNR.value = opt.value; achou = true; break; }
    }
    if (!achou) selectNR.selectedIndex = 0;

    if (c.img) {
      preview.src = c.img;
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

  toggleSemValidade.addEventListener('change', function () {
    inputValidade.disabled = this.checked;
    if (this.checked) inputValidade.value = '';
  });

  document.getElementById('btnSalvar').addEventListener('click', function () {
    const titulo = inputTitulo.value.trim();
    if (!titulo) {
      alert('Digite o título do treinamento.');
      inputTitulo.focus();
      return;
    }

    const nrTexto = selectNR.value ? selectNR.options[selectNR.selectedIndex].textContent.split(' - ')[0] : 'NR-00';
    const cargaTexto = inputCarga.value.trim() ? `${inputCarga.value.trim()} horas` : 'A definir';
    const validadeTexto = toggleSemValidade.checked ? '' : inputValidade.value.trim();
    const imagemFinal = (preview.src && !preview.hidden) ? preview.src : IMG_PADRAO;

    if (editingId !== null) {
      // Atualiza treinamento existente
      const c = cursos.find(x => x.id === editingId);
      c.titulo = titulo;
      c.desc = inputSubtitulo.value.trim() || c.desc;
      c.horas = cargaTexto;
      c.validade = validadeTexto || null;
      c.nr = nrTexto;
      c.img = imagemFinal;
      console.log('[Editar treinamento] salvo:', c);
    } else {
      // Cria novo treinamento
      const novoCurso = {
        id: nextId++,
        nr: nrTexto,
        titulo: titulo,
        status: 'valido',
        desc: inputSubtitulo.value.trim() || 'Descrição não informada.',
        horas: cargaTexto,
        validade: validadeTexto || null,
        temProva: false,
        img: imagemFinal,
      };
      cursos.unshift(novoCurso);
      console.log('[Criar treinamento] novo card:', novoCurso);
    }

    document.getElementById('searchInput').value = '';
    rerenderComBusca();
    updateKpis();
    alert('Treinamento salvo com sucesso!');
    fecharModal();
  });

  document.getElementById('btnExcluir').addEventListener('click', function () {
    if (editingId === null) {
      fecharModal();
      return;
    }
    const c = cursos.find(x => x.id === editingId);
    if (!confirm(`Excluir o treinamento "${c.titulo}"?`)) return;

    const idx = cursos.findIndex(x => x.id === editingId);
    cursos.splice(idx, 1);
    console.log('[Excluir treinamento]', c.titulo);
    rerenderComBusca();
    updateKpis();
    alert('Treinamento excluído.');
    fecharModal();
  });

  modalOverlay.addEventListener('click', function (e) {
    if (e.target === modalOverlay) fecharModal();
  });

  inputImagem.addEventListener('change', function (event) {
    const file = event.target.files && event.target.files[0];
    if (!file) return;
    if (!file.type.startsWith('image/')) {
      alert('Selecione um arquivo de imagem válido.');
      return;
    }
    const reader = new FileReader();
    reader.onload = function (e) {
      preview.src = e.target.result;
      preview.hidden = false;
      imgUpload.classList.add('has-image');
    };
    reader.readAsDataURL(file);
  });

  removeBtn.addEventListener('click', function (event) {
    event.stopPropagation();
    preview.src = '';
    preview.hidden = true;
    inputImagem.value = '';
    imgUpload.classList.remove('has-image');
  });