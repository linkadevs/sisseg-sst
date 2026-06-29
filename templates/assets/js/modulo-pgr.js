/* ── SVG helpers ─────────────────────────────────────── */
const svgCheck = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>`;
const svgShield = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>`;
const svgClipboard = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>`;
const svgChevron = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>`;

/* ── Activity data ────────────────────────────────────── */
const DATA = {
  "Trabalho em Altura": {
    riscos: [
      {
        tipo: "Acidente",
        nivel: "Crítico",
        desc: "Queda de altura com diferença de nível superior a 2 metros",
        prob: "4/5",
        sev: "5/5",
        medidas: [
          "Uso obrigatório de cinto de segurança tipo paraquedista",
          "Instalação de linha de vida certificada",
          "Plataforma de trabalho com guarda-corpo",
          "Sinalização e isolamento da área abaixo"
        ],
        epis: [
          "Capacete com jugular",
          "Cinto de segurança tipo paraquedista",
          "Trava-quedas retrátil"
        ]
      },
      {
        tipo: "Acidente",
        nivel: "Alto",
        desc: "Queda de materiais e ferramentas",
        prob: "3/5",
        sev: "4/5",
        medidas: [
          "Uso de bolsas para ferramentas com mosquetão",
          "Instalação de tela de proteção na periferia",
          "Cordas de segurança para ferramentas",
          "Capacetes obrigatórios para todos na área"
        ],
        epis: [
          "Capacete de segurança"
        ]
      }
    ],
    medidas: [
      "Guarda-corpo rígido nos perímetros",
      "Tela de proteção em toda a fachada",
      "Plataforma de proteção a cada 3 pavimentos",
      "Sistema de ancoragem permanente"
    ],
    procedimentos: [
      "Análise Preliminar de Risco (APR) diária",
      "Permissão de Trabalho (PT) assinada",
      "Inspeção de EPIs antes do início",
      "Treinamento NR-35 atualizado"
    ]
  },
  "Eletricidade": {
    riscos: [
      {
        tipo: "Acidente",
        nivel: "Crítico",
        desc: "Choque elétrico por contato direto com partes energizadas",
        prob: "4/5",
        sev: "5/5",
        medidas: [
          "Bloqueio e etiquetagem (LOTO) antes de qualquer intervenção",
          "Uso de equipamentos de teste de tensão calibrados",
          "Delimitação da zona controlada",
          "Execução somente por eletricista habilitado NR-10"
        ],
        epis: [
          "Luva isolante classe adequada à tensão",
          "Calçado de segurança dielétrico",
          "Óculos de proteção com absorção UV"
        ]
      },
      {
        tipo: "Acidente",
        nivel: "Crítico",
        desc: "Arco elétrico em painéis e quadros energizados",
        prob: "3/5",
        sev: "5/5",
        medidas: [
          "Uso de EPIs para proteção contra arco elétrico",
          "Estudos de arco elétrico conforme NR-10",
          "Procedimento de energização documentado",
          "Distâncias seguras estabelecidas e sinalizadas"
        ],
        epis: [
          "Roupa de proteção contra arco elétrico",
          "Face shield com proteção para arco",
          "Luva isolante com sobreposta de raspa"
        ]
      }
    ],
    medidas: [
      "Instalação de aterramento temporário antes de intervenções",
      "Sinalização de áreas energizadas com fitas e cones",
      "Bloqueio físico de painéis com cadeados e hastes LOTO",
      "Verificação de ausência de tensão com detector calibrado"
    ],
    procedimentos: [
      "Emissão de Permissão de Trabalho (PT) específica para elétrica",
      "Verificação de habilitação NR-10 de todos os envolvidos",
      "Análise do risco de arco elétrico antes da intervenção",
      "Registro fotográfico do estado dos painéis pré/pós serviço"
    ]
  },
  "Espaço Confinado": {
    riscos: [
      {
        tipo: "Acidente",
        nivel: "Crítico",
        desc: "Atmosfera deficiente em oxigênio (O₂ < 19,5%)",
        prob: "4/5",
        sev: "5/5",
        medidas: [
          "Monitoramento contínuo da atmosfera com detector 4 gases",
          "Ventilação forçada antes e durante toda a operação",
          "Linha de resgate e tripé instalados externamente",
          "Vigilante externo capacitado em NR-33 durante todo o trabalho"
        ],
        epis: [
          "Máscara de ar mandado ou SCBA",
          "Arnês de resgate com ponto dorsal e esternal",
          "Detector de gases pessoal (4 gases)"
        ]
      },
      {
        tipo: "Acidente",
        nivel: "Crítico",
        desc: "Atmosfera inflamável ou tóxica por acúmulo de gases",
        prob: "3/5",
        sev: "5/5",
        medidas: [
          "Purga e limpeza do espaço antes da entrada",
          "Proibição de uso de ferramentas que gerem faísca",
          "Equipamentos elétricos à prova de explosão (EX)",
          "Comunicação contínua entre interno e vigilante"
        ],
        epis: [
          "Detector explosímetro calibrado",
          "Roupas antiestáticas",
          "Proteção respiratória adequada ao agente químico"
        ]
      }
    ],
    medidas: [
      "Isolamento e bloqueio de todas as tubulações que alimentam o espaço",
      "Ventilação mecânica contínua durante toda a operação",
      "Tripé de resgate com talha e corda de vida instalados no acesso",
      "Comunicação via rádio entre interno e vigilante externo"
    ],
    procedimentos: [
      "Emissão de Permissão de Entrada e Trabalho (PET) conforme NR-33",
      "Medição da atmosfera antes da entrada e a cada 30 minutos",
      "Briefing de emergência e resgate com toda a equipe",
      "Presença obrigatória de supervisor de entrada capacitado"
    ]
  },
  "Soldagem": {
    riscos: [
      {
        tipo: "Químico",
        nivel: "Alto",
        desc: "Inalação de fumos metálicos e gases tóxicos da soldagem",
        prob: "4/5",
        sev: "4/5",
        medidas: [
          "Exaustão localizada na fonte (DEL) no ponto de soldagem",
          "Ventilação geral diluidora do ambiente",
          "Uso de eletrodos com menor emissão de fumos",
          "Rodízio de trabalhadores para reduzir exposição individual"
        ],
        epis: [
          "Respirador semifacial com filtro P2 e carvão ativado",
          "Máscara de solda com visor auto-escurecente DIN 11",
          "Avental de raspa de couro"
        ]
      },
      {
        tipo: "Acidente",
        nivel: "Alto",
        desc: "Queimaduras por respingos e radiação do arco elétrico",
        prob: "3/5",
        sev: "4/5",
        medidas: [
          "Uso de biombos e anteparos de proteção ao redor do posto",
          "Sinalização de área de soldagem para outros trabalhadores",
          "Inspeção prévia de mangueiras e conexões do maçarico",
          "Extintor de incêndio posicionado a não mais de 5 m do posto"
        ],
        epis: [
          "Luvas de raspa cano longo",
          "Calçado de segurança com biqueira de aço e solado resistente a calor",
          "Perneira de raspa"
        ]
      }
    ],
    medidas: [
      "Instalação de biombos de proteção ao redor do posto de soldagem",
      "Sistema de exaustão localizada com captação na fonte",
      "Armazenamento correto de cilindros de gás (encadeados e ventilados)",
      "Inspeção diária de mangueiras, conexões e maçaricos"
    ],
    procedimentos: [
      "Verificação de gases inflamáveis na área antes de acender o maçarico",
      "Inspeção de EPIs específicos antes de cada jornada",
      "Análise Preliminar de Risco (APR) no início de cada turno",
      "Registro de ocorrências e near misses no diário de obra"
    ]
  },
  "Demolição": {
    riscos: [
      {
        tipo: "Acidente",
        nivel: "Crítico",
        desc: "Colapso estrutural não controlado durante a demolição",
        prob: "4/5",
        sev: "5/5",
        medidas: [
          "Laudo de estabilidade estrutural assinado por engenheiro",
          "Sequência de demolição documentada e aprovada",
          "Escoramento de estruturas adjacentes antes do início",
          "Monitoramento de trincas e deformações com sensores"
        ],
        epis: [
          "Capacete com jugular",
          "Colete de comunicação com GPS",
          "Calçado de segurança com metatarso"
        ]
      },
      {
        tipo: "Acidente",
        nivel: "Crítico",
        desc: "Projeção de fragmentos e detritos durante a demolição",
        prob: "4/5",
        sev: "4/5",
        medidas: [
          "Telas de proteção na periferia da área de demolição",
          "Umidificação dos escombros para reduzir poeira e fragmentos",
          "Isolamento de raio mínimo de 1,5× a altura da estrutura",
          "Uso de equipamentos de demolição com proteções adequadas"
        ],
        epis: [
          "Óculos de proteção ampla visão",
          "Protetor facial de policarbonato",
          "Respirador semifacial PFF2 para poeiras"
        ]
      },
      {
        tipo: "Físico",
        nivel: "Alto",
        desc: "Exposição a poeiras com sílica cristalina (risco silicose)",
        prob: "4/5",
        sev: "4/5",
        medidas: [
          "Umidificação contínua dos pontos de geração de poeira",
          "Aspiração industrial acoplada às ferramentas de corte",
          "Limite de tempo de exposição sem proteção respiratória",
          "Monitoramento ambiental de poeiras respiráveis"
        ],
        epis: [
          "Respirador PFF2 ou PFF3 para sílica",
          "Óculos de proteção",
          "Macacão descartável para ambientes com poeira"
        ]
      }
    ],
    medidas: [
      "Isolamento do canteiro com tapumes e sinalização de obra",
      "Umidificação sistemática para controle de poeira",
      "Escoramento de estruturas vizinhas antes da demolição",
      "Plano de demolição aprovado por responsável técnico"
    ],
    procedimentos: [
      "Vistoria estrutural diária antes do início dos trabalhos",
      "Briefing de segurança com toda a equipe no início do turno",
      "Monitoramento meteorológico (suspender em ventos > 40 km/h)",
      "Plano de emergência e evacuação afixado na entrada da obra"
    ]
  }
};

/* ── Render detail page ─────────────────────────────── */
function renderDetail(activityName) {
  const data = DATA[activityName];
  document.getElementById('detail-title').textContent = activityName;

  /* Riscos */
  const riscosList = document.getElementById('riscos-list');
  riscosList.innerHTML = data.riscos.map((r, i) => {
    const nivelClass = r.nivel === 'Crítico' ? 'badge--critico-pill' : 'badge--alto-pill';
    const medidasItems = r.medidas.map(m =>
      `<li>${svgShield} ${m}</li>`).join('');
    const episItems = r.epis.map(e =>
      `<li>${svgCheck} ${e}</li>`).join('');
    return `
      <div class="risco-item">
        <div class="risco-badges">
          <span class="badge--acidente">Acidente</span>
          <span class="${nivelClass}">${r.nivel}</span>
        </div>
        <p class="risco-desc">${r.desc}</p>
        <p class="risco-meta">
          <span>Probabilidade: ${r.prob}</span>
          <span>Severidade: ${r.sev}</span>
        </p>
        <div class="accordion-row">
          <button class="accordion-trigger" aria-expanded="false" data-target="acc-m-${i}">
            Medidas de Controle (${r.medidas.length}) ${svgChevron}
          </button>
          <div class="accordion-body" id="acc-m-${i}">
            <ul class="accordion-list">${medidasItems}</ul>
          </div>
        </div>
        <div class="accordion-row">
          <button class="accordion-trigger" aria-expanded="false" data-target="acc-e-${i}">
            EPIs Relacionados (${r.epis.length}) ${svgChevron}
          </button>
          <div class="accordion-body" id="acc-e-${i}">
            <ul class="accordion-list">${episItems}</ul>
          </div>
        </div>
      </div>`;
  }).join('');

  /* Medidas coletivas */
  document.getElementById('medidas-list').innerHTML = data.medidas.map(m =>
    `<div class="medida-item">${svgCheck} ${m}</div>`
  ).join('');

  /* Procedimentos */
  document.getElementById('procedimentos-list').innerHTML = data.procedimentos.map(p =>
    `<div class="procedimento-item">${svgClipboard} ${p}</div>`
  ).join('');

  /* Wire up accordions */
  document.querySelectorAll('.accordion-trigger').forEach(btn => {
    btn.addEventListener('click', function() {
      const target = document.getElementById(this.dataset.target);
      const isOpen = target.classList.contains('open');
      target.classList.toggle('open', !isOpen);
      this.setAttribute('aria-expanded', String(!isOpen));
    });
  });
}

/* ── Navigation ─────────────────────────────────────── */
function showDetail(activityName) {
  renderDetail(activityName);
  document.getElementById('list-page').style.display = 'none';
  document.getElementById('detail-page').style.display = 'block';
  window.scrollTo(0, 0);
}

function showList() {
  document.getElementById('detail-page').style.display = 'none';
  document.getElementById('list-page').style.display = 'block';
  window.scrollTo(0, 0);
}

/* ── Listeners ─────────────────────────────────────── */
document.getElementById('btn-back-dashboard').addEventListener('click', function(e) {
  e.preventDefault();
  console.log('[PGR] Voltar ao Dashboard');
});

document.getElementById('btn-back-fichas').addEventListener('click', function(e) {
  e.preventDefault();
  showList();
});

document.querySelectorAll('.btn-ficha').forEach(function(btn) {
  btn.addEventListener('click', function() {
    showDetail(this.dataset.activity);
  });
});
