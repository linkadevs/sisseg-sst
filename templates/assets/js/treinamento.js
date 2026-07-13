const cursos = [
    { nr:'NR-06', titulo:'EPIs', status:'valido', desc:'Uso e responsabilidade de Equipamentos de Proteção Individual.', horas:'2 horas', validade:null, conclusao:'14/06/2024', informativo:false, img:'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?w=600&h=340&fit=crop' },
    { nr:'NR-10', titulo:'Eletricidade', status:'invalido', desc:'Segurança em instalações e serviços com eletricidade.', horas:'40 horas (Básico)', validade:'24 meses', conclusao:null, informativo:false, img:'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?w=600&h=340&fit=crop' },
    { nr:'NR-11', titulo:'Movimentação de Cargas', status:'invalido', desc:'Transporte, movimentação, armazenagem e manuseio de materiais.', horas:'16 horas', validade:'24 meses', conclusao:null, informativo:false, img:'https://images.unsplash.com/photo-1553413077-190dd305871c?w=600&h=340&fit=crop' },
    { nr:'NR-12', titulo:'Segurança em Máquinas', status:'valido', desc:'Riscos, proteções e operação segura de máquinas e equipamentos.', horas:'8 horas', validade:null, conclusao:null, informativo:false, img:'https://images.unsplash.com/photo-1581092160607-ee22621dd758?w=600&h=340&fit=crop' },
    { nr:'NR-17', titulo:'Ergonomia', status:'valido', desc:'Adaptação das condições de trabalho às características do trabalhador.', horas:'4 horas', validade:null, conclusao:null, informativo:false, img:'https://images.unsplash.com/photo-1552664730-d307ca884978?w=600&h=340&fit=crop' },
    { nr:'NR-18', titulo:'Construção Civil', status:'invalido', desc:'Condições de segurança no canteiro de obras.', horas:'6 horas', validade:'12 meses', conclusao:null, informativo:false, img:'https://images.unsplash.com/photo-1541976590-713941681591?w=600&h=340&fit=crop' },
    { nr:'NR-20', titulo:'Inflamáveis e Combustíveis', status:'invalido', desc:'Segurança no trabalho com líquidos e gases inflamáveis.', horas:'8 horas (Básico)', validade:'36 meses', conclusao:null, informativo:false, img:'https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=600&h=340&fit=crop' },
    { nr:'NR-23', titulo:'Proteção Contra Incêndio', status:'invalido', desc:'Prevenção e combate a incêndios no ambiente de trabalho.', horas:'4 horas', validade:'12 meses', conclusao:null, informativo:false, img:'https://images.unsplash.com/photo-1573497161161-c3e73707e25c?w=600&h=340&fit=crop' },
    { nr:'NR-26', titulo:'Sinalização de Segurança', status:'valido', desc:'Cores, símbolos e identificação de riscos.', horas:'2 horas', validade:null, conclusao:'04/09/2024', informativo:false, img:'https://images.unsplash.com/photo-1578575437130-527eed3abbec?w=600&h=340&fit=crop' },
    { nr:'NR-33', titulo:'Espaço Confinado', status:'invalido', desc:'Segurança em trabalhos em espaços confinados.', horas:'16 horas', validade:'12 meses', conclusao:null, informativo:false, img:'https://images.unsplash.com/photo-1504328345606-18bbc8c9d7d1?w=600&h=340&fit=crop' },
    { nr:'NR-35', titulo:'Trabalho em Altura', status:'invalido', desc:'Planejamento, organização e execução de trabalho em altura.', horas:'8 horas', validade:'24 meses', conclusao:null, informativo:false, img:'https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=600&h=340&fit=crop' },
    { nr:'NR-01', titulo:'Disposições Gerais', status:'valido', desc:'Princípios gerais de segurança e saúde no trabalho.', horas:'1 hora', validade:null, conclusao:'30/04/2024', informativo:true, img:'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=600&h=340&fit=crop' },
    { nr:'PGR', titulo:'Gerenciamento de Riscos', status:'valido', desc:'Programa de Gerenciamento de Riscos ocupacionais.', horas:'1 hora', validade:null, conclusao:null, informativo:true, img:'https://images.unsplash.com/photo-1503387762-592deb58ef4e?w=600&h=340&fit=crop' },
    { nr:'PCMSO', titulo:'Controle Médico', status:'valido', desc:'Programa de Controle Médico de Saúde Ocupacional.', horas:'1 hora', validade:null, conclusao:null, informativo:true, img:'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=600&h=340&fit=crop' },
    { nr:'APR', titulo:'Análise Preliminar de Risco', status:'invalido', desc:'Identificação de riscos antes do início das atividades.', horas:'2 horas', validade:'12 meses', conclusao:null, informativo:false, img:'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?w=600&h=340&fit=crop' },
    { nr:'FDS', titulo:'Produtos Químicos (FDS)', status:'valido', desc:'Ficha de Dados de Segurança e manuseio de químicos.', horas:'3 horas', validade:null, conclusao:'11/10/2024', informativo:false, img:'https://images.unsplash.com/photo-1532187863486-abf9dbad1b69?w=600&h=340&fit=crop' },
    { nr:'PT', titulo:'Permissão de Trabalho', status:'valido', desc:'Procedimentos para emissão e controle de PT.', horas:'2 horas', validade:null, conclusao:'31/10/2024', informativo:false, img:'https://images.unsplash.com/photo-1497366216548-37526070297c?w=600&h=340&fit=crop' },
    { nr:'5S', titulo:'Programa 5S', status:'valido', desc:'Metodologia de organização e disciplina no trabalho.', horas:'2 horas', validade:null, conclusao:'14/11/2024', informativo:false, img:'https://images.unsplash.com/photo-1587613864411-cbf6a9358350?w=600&h=340&fit=crop' },
  ];

  let filtroAtual = 'todos';

  function renderCursos() {
    const grid = document.getElementById('coursesGrid');
    const lista = cursos.filter(c => filtroAtual === 'todos' ? true : c.status === filtroAtual);

    grid.innerHTML = lista.map(c => `
      <div class="course-card">
        <div class="course-thumb">
          <img src="${c.img}" alt="${c.titulo}" loading="lazy">
          <span class="nr-badge">${c.nr}</span>
        </div>
        <div class="course-body">
          <div class="course-title-row">
            <h3>${c.titulo}</h3>
            <span class="status-badge ${c.status === 'valido' ? 'valido' : 'invalido'}">${c.status === 'valido' ? 'VÁLIDO*' : 'INVÁLIDO'}</span>
          </div>
          <p class="course-desc">${c.desc}</p>
          <div class="course-meta">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <span>${c.horas}${c.validade ? ` &bull; Validade: ${c.validade}` : ''}</span>
          </div>

          <button class="btn-assistir" onclick="assistir('${c.titulo}')">
            <svg viewBox="0 0 24 24" fill="currentColor"><polygon points="6 3 20 12 6 21 6 3"/></svg>
            Assistir
          </button>

          ${ c.informativo ? '' : `
          <div class="action-row">
            <button class="btn-outline-sm" onclick="fazerProva('${c.titulo}')">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
              Fazer Prova
            </button>
            <button class="btn-outline-sm ${c.status === 'invalido' ? 'disabled' : ''}" onclick="${c.status === 'invalido' ? '' : `verCertificado('${c.titulo}')`}">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M8.21 13.89 7 23l5-3 5 3-1.21-9.12"/></svg>
              Certificado
            </button>
          </div>` }

          ${ c.conclusao ? `<div class="conclusao-row"><span>Conclusão:</span><span>${c.conclusao}</span></div>` : '' }
        </div>
      </div>
    `).join('');
  }

  function setFiltro(f) {
    filtroAtual = f;
    document.querySelectorAll('.tab-btn').forEach(btn => {
      btn.classList.toggle('active', btn.dataset.filtro === f);
    });
    renderCursos();
    console.log(`[Filtro] ${f}`);
  }

  function assistir(titulo) {
    console.log(`[Assistir] ${titulo}`);
    alert(`Abrindo vídeo do curso: ${titulo}`);
  }

  function fazerProva() {
    window.location.href = '../View/prova.html'
  }

  function verCertificado(titulo) {
    console.log(`[Certificado] ${titulo}`);
    alert(`Exibindo certificado do curso: ${titulo}`);
  }

  renderCursos();