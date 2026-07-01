function agendarTroca(nome, epi) {
    console.log(`[Agendar Troca] Colaborador: ${nome} | EPI: ${epi}`);
    alert(`Troca agendada para ${nome} (${epi}).`);
  }

  // Base de dados dos EPIs obrigatórios por atividade
  const atividades = {
    altura: {
      nome: 'Trabalho em Altura', emoji: '🪜', nr: 'NR-35',
      epis: [
        { icon:'🧍', nome:'Cinturão Paraquedista', desc:'Proteção contra quedas de altura', ca:'CA 38.570' },
        { icon:'🪝', nome:'Talabarte com Absorvedor', desc:'Conexão segura ao ponto de ancoragem', ca:'CA 35.824' },
        { icon:'⛑️', nome:'Capacete com Jugular', desc:'Proteção da cabeça contra impactos', ca:'CA 31.469' },
        { icon:'🧤', nome:'Luvas Antiderrapantes', desc:'Proteção das mãos e melhor aderência', ca:'CA 29.837' },
        { icon:'🥾', nome:'Bota de Segurança', desc:'Proteção dos pés contra impactos e perfurações', ca:'CA 42.123' },
      ]
    },
    alvenaria: {
      nome:'Alvenaria', emoji:'🧱', nr:'NR-18',
      epis: [
        { icon:'⛑️', nome:'Capacete de Segurança', desc:'Proteção da cabeça contra impactos e queda de materiais', ca:'CA 31.469' },
        { icon:'🥽', nome:'Óculos de Proteção', desc:'Proteção contra respingos de argamassa e poeira', ca:'CA 25.310' },
        { icon:'🧤', nome:'Luvas de Raspa', desc:'Proteção das mãos no manuseio de blocos e ferramentas', ca:'CA 28.774' },
        { icon:'🥾', nome:'Bota de Segurança', desc:'Proteção dos pés contra impactos e perfurações', ca:'CA 42.123' },
        { icon:'🎧', nome:'Protetor Auricular', desc:'Redução da exposição a ruídos de equipamentos', ca:'CA 19.845' },
        { icon:'🧍', nome:'Cinto Tipo Paraquedista', desc:'Proteção em trabalhos sobre andaimes', ca:'CA 38.570' },
      ]
    },
    carpintaria: {
      nome:'Carpintaria', emoji:'🪚', nr:'NR-18',
      epis: [
        { icon:'🥽', nome:'Óculos de Proteção', desc:'Proteção contra serragem e lascas de madeira', ca:'CA 25.310' },
        { icon:'😷', nome:'Máscara contra Poeira PFF1', desc:'Proteção respiratória contra pó de madeira', ca:'CA 21.556' },
        { icon:'🧤', nome:'Luvas de Raspa', desc:'Proteção das mãos no manuseio de madeira e ferramentas', ca:'CA 28.774' },
        { icon:'🎧', nome:'Protetor Auricular', desc:'Redução da exposição a ruídos de serras elétricas', ca:'CA 19.845' },
        { icon:'⛑️', nome:'Capacete de Segurança', desc:'Proteção contra queda de materiais', ca:'CA 31.469' },
        { icon:'🥼', nome:'Avental de Raspa', desc:'Proteção do tronco contra cortes e farpas', ca:'CA 30.221' },
      ]
    },
    pintura: {
      nome:'Pintura', emoji:'🎨', nr:'NR-18',
      epis: [
        { icon:'😷', nome:'Máscara com Filtro Químico', desc:'Proteção respiratória contra vapores de tinta e solvente', ca:'CA 24.678' },
        { icon:'🥽', nome:'Óculos Ampla Visão', desc:'Proteção contra respingos de tinta', ca:'CA 25.310' },
        { icon:'🧤', nome:'Luvas de PVC', desc:'Proteção das mãos contra produtos químicos', ca:'CA 27.902' },
        { icon:'🥼', nome:'Macacão de Proteção', desc:'Proteção do corpo contra respingos de tinta', ca:'CA 33.415' },
        { icon:'🥾', nome:'Bota de Segurança', desc:'Proteção dos pés contra impactos e produtos químicos', ca:'CA 42.123' },
        { icon:'🧍', nome:'Cinto Tipo Paraquedista', desc:'Proteção em pintura de fachadas e altura', ca:'CA 38.570' },
      ]
    },
    soldagem: {
      nome:'Soldagem', emoji:'🔥', nr:'NR-18',
      epis: [
        { icon:'🛡️', nome:'Máscara de Solda', desc:'Proteção dos olhos e face contra radiação e faíscas', ca:'CA 34.201' },
        { icon:'🥼', nome:'Avental de Raspa', desc:'Proteção do tronco contra respingos de solda', ca:'CA 30.221' },
        { icon:'🧤', nome:'Luvas de Raspa Cano Longo', desc:'Proteção das mãos e antebraços contra calor e respingos', ca:'CA 28.774' },
        { icon:'🛡️', nome:'Perneira de Raspa', desc:'Proteção das pernas contra respingos de solda', ca:'CA 32.560' },
        { icon:'🥾', nome:'Bota com Biqueira', desc:'Proteção dos pés contra queda de material e calor', ca:'CA 42.123' },
        { icon:'😷', nome:'Respirador PFF2', desc:'Proteção contra fumos metálicos', ca:'CA 22.897' },
      ]
    },
    eletricidade: {
      nome:'Eletricidade', emoji:'⚡', nr:'NR-10',
      epis: [
        { icon:'⛑️', nome:'Capacete Classe B', desc:'Proteção da cabeça com isolamento elétrico', ca:'CA 31.988' },
        { icon:'🧤', nome:'Luvas Isolantes de Borracha', desc:'Proteção das mãos contra choque elétrico', ca:'CA 26.412' },
        { icon:'🥽', nome:'Óculos de Proteção', desc:'Proteção contra arco elétrico e faíscas', ca:'CA 25.310' },
        { icon:'🥾', nome:'Calçado Isolante', desc:'Proteção dos pés contra corrente elétrica', ca:'CA 41.077' },
        { icon:'🥼', nome:'Vestimenta Antichama', desc:'Proteção do corpo contra arco elétrico', ca:'CA 36.650' },
        { icon:'🎧', nome:'Protetor Auricular', desc:'Redução da exposição a ruídos em subestações', ca:'CA 19.845' },
        { icon:'🧍', nome:'Cinto Tipo Paraquedista', desc:'Proteção em trabalhos em postes e altura', ca:'CA 38.570' },
      ]
    },
    confinado: {
      nome:'Espaço Confinado', emoji:'🔒', nr:'NR-33',
      epis: [
        { icon:'📟', nome:'Detector de Gases Portátil', desc:'Monitoramento de atmosfera tóxica e explosiva', ca:'CA 40.512' },
        { icon:'😷', nome:'Máscara Autônoma', desc:'Proteção respiratória em atmosferas deficientes de oxigênio', ca:'CA 23.884' },
        { icon:'🧍', nome:'Cinto Tipo Paraquedista', desc:'Ancoragem para resgate em espaço confinado', ca:'CA 38.570' },
        { icon:'⚙️', nome:'Tripé de Resgate com Guincho', desc:'Equipamento para içamento e resgate', ca:'CA 39.221' },
        { icon:'⛑️', nome:'Capacete com Jugular', desc:'Proteção da cabeça contra impactos', ca:'CA 31.469' },
        { icon:'🧤', nome:'Luvas de Proteção Química', desc:'Proteção das mãos contra substâncias nocivas', ca:'CA 27.902' },
        { icon:'🔦', nome:'Lanterna Intrinsecamente Segura', desc:'Iluminação em áreas com risco de explosão', ca:'CA 37.104' },
        { icon:'📻', nome:'Rádio Comunicador', desc:'Comunicação com equipe de vigia externo', ca:'CA 39.900' },
      ]
    },
    demolicao: {
      nome:'Demolição', emoji:'🔨', nr:'NR-18',
      epis: [
        { icon:'⛑️', nome:'Capacete de Segurança', desc:'Proteção da cabeça contra queda de material', ca:'CA 31.469' },
        { icon:'🥽', nome:'Óculos de Proteção', desc:'Proteção contra poeira e fragmentos', ca:'CA 25.310' },
        { icon:'😷', nome:'Máscara PFF2', desc:'Proteção respiratória contra poeira de demolição', ca:'CA 22.897' },
        { icon:'🧤', nome:'Luvas de Raspa', desc:'Proteção das mãos no manuseio de escombros', ca:'CA 28.774' },
        { icon:'🥾', nome:'Bota com Biqueira', desc:'Proteção dos pés contra impactos e perfurações', ca:'CA 42.123' },
        { icon:'🎧', nome:'Protetor Auricular', desc:'Redução da exposição a ruídos de equipamentos', ca:'CA 19.845' },
        { icon:'🦺', nome:'Colete Refletivo', desc:'Sinalização e visibilidade na área de risco', ca:'CA 18.330' },
      ]
    },
    armador: {
      nome:'Armador de Ferro', emoji:'🔩', nr:'NR-18',
      epis: [
        { icon:'⛑️', nome:'Capacete de Segurança', desc:'Proteção da cabeça contra impactos', ca:'CA 31.469' },
        { icon:'🧤', nome:'Luvas de Raspa', desc:'Proteção das mãos no manuseio de vergalhões', ca:'CA 28.774' },
        { icon:'🥽', nome:'Óculos de Proteção', desc:'Proteção contra fragmentos de arame e ferrugem', ca:'CA 25.310' },
        { icon:'🥾', nome:'Bota com Biqueira', desc:'Proteção dos pés contra perfurações', ca:'CA 42.123' },
        { icon:'🛡️', nome:'Manga de Raspa', desc:'Proteção dos antebraços contra cortes', ca:'CA 30.998' },
        { icon:'🎧', nome:'Protetor Auricular', desc:'Redução da exposição a ruídos de máquinas de corte', ca:'CA 19.845' },
      ]
    },
    concretagem: {
      nome:'Concretagem', emoji:'🏗️', nr:'NR-18',
      epis: [
        { icon:'⛑️', nome:'Capacete de Segurança', desc:'Proteção da cabeça contra impactos', ca:'CA 31.469' },
        { icon:'🥾', nome:'Bota de Borracha Cano Longo', desc:'Proteção dos pés contra concreto e umidade', ca:'CA 40.215' },
        { icon:'🧤', nome:'Luvas de PVC', desc:'Proteção das mãos contra o contato com o concreto', ca:'CA 27.902' },
        { icon:'🥽', nome:'Óculos de Proteção', desc:'Proteção contra respingos de concreto', ca:'CA 25.310' },
        { icon:'🎧', nome:'Protetor Auricular', desc:'Redução da exposição a ruídos da betoneira', ca:'CA 19.845' },
        { icon:'🥼', nome:'Avental Impermeável', desc:'Proteção do corpo contra respingos de concreto', ca:'CA 29.400' },
      ]
    },
    operador: {
      nome:'Operador de Máquinas', emoji:'🚜', nr:'NR-12',
      epis: [
        { icon:'⛑️', nome:'Capacete de Segurança', desc:'Proteção da cabeça contra impactos', ca:'CA 31.469' },
        { icon:'🦺', nome:'Colete Refletivo', desc:'Sinalização e visibilidade no canteiro', ca:'CA 18.330' },
        { icon:'🎧', nome:'Protetor Auricular', desc:'Redução da exposição a ruídos do motor', ca:'CA 19.845' },
        { icon:'🥾', nome:'Bota com Biqueira', desc:'Proteção dos pés contra impactos', ca:'CA 42.123' },
        { icon:'🧤', nome:'Luvas de Proteção', desc:'Proteção das mãos na operação e manutenção', ca:'CA 26.850' },
      ]
    },
  };

  function verEpis(slug) {
    const atividade = atividades[slug];
    if (!atividade) {
      console.log(`[Ver EPIs] Atividade não encontrada: ${slug}`);
      return;
    }
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

  function realizarCheckin() {
    const atividade = document.getElementById('detail-title').textContent;
    console.log(`[Check-in com Selfie] Atividade: ${atividade}`);
    alert(`Abrindo câmera para check-in de EPIs — ${atividade}`);
  }