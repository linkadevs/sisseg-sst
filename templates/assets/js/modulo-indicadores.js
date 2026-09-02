/* ── Shared axis labels ── */
  

  /* ── Shared grid/tick style ── */
  const gridStyle = {
    color: 'rgba(0,0,0,0.06)',
    lineWidth: 1,
  };
  const tickStyle = {
    color: '#9ca3af',
    font: { family: "'Inter', sans-serif", size: 11 },
  };

  /* ── Responsive X-axis label skip helper ── */
  function xTickCallback(val, index, ticks) {
    // On very small screens skip alternate labels
    if (window.innerWidth < 420) {
      return index % 2 === 0 ? months[index] : null;
    }
    return months[index];
  }

  /* ────────────────────────────────────
     CHART 1 — Linha: Taxa de Frequência
  ──────────────────────────────────── */
  const ctxLine = document.getElementById('chartFrequencia').getContext('2d');

  new Chart(ctxLine, {
    type: 'line',
    data: {
      labels: months,
      datasets: [{
        label: 'Taxa de Frequência',
        data: dadosLinha,
        borderColor: '#7c3aed',
        backgroundColor: 'transparent',
        borderWidth: 2.5,
        tension: 0.4,                 // smooth spline
        pointRadius: 5,
        pointHoverRadius: 7,
        pointBackgroundColor: '#ffffff',
        pointBorderColor: '#7c3aed',
        pointBorderWidth: 2.5,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            usePointStyle: true,
            pointStyle: 'circle',
            color: '#7c3aed',
            font: { family: "'Inter', sans-serif", size: 12 },
            padding: 16,
          }
        },
        tooltip: {
          backgroundColor: '#fff',
          borderColor: '#e5e7eb',
          borderWidth: 1,
          titleColor: '#111827',
          bodyColor: '#6b7280',
          padding: 10,
        }
      },
      scales: {
        x: {
          grid: { ...gridStyle, borderDash: [4, 4] },
          ticks: { ...tickStyle, callback: xTickCallback, maxRotation: 0 },
          border: { display: false },
        },
        y: {
          min: 0,
          max: max_qtd_linha + 1,
          ticks: { ...tickStyle, stepSize: 6 },
          grid: { ...gridStyle, borderDash: [4, 4] },
          border: { display: false },
        }
      }
    }
  });

  /* ────────────────────────────────────
     CHART 2 — Barras: Treinamentos
  ──────────────────────────────────── */
  const ctxBar = document.getElementById('chartTreinamentos').getContext('2d');
  new Chart(ctxBar, {
    type: 'bar',
    data: {
      labels: months,
      datasets: [{
        label: 'Treinamentos',
        data: dadosBarra,
        backgroundColor: '#10b981',
        borderRadius: 4,
        borderSkipped: false,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            usePointStyle: true,
            pointStyle: 'rect',
            color: '#10b981',
            font: { family: "'Inter', sans-serif", size: 12 },
            padding: 16,
          }
        },
        tooltip: {
          backgroundColor: '#fff',
          borderColor: '#e5e7eb',
          borderWidth: 1,
          titleColor: '#111827',
          bodyColor: '#6b7280',
          padding: 10,
        }
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: { ...tickStyle, callback: xTickCallback, maxRotation: 0 },
          border: { display: false },
        },
        y: {
          min: 0,
          max: max_qtd,
          ticks: { ...tickStyle, stepSize: 20 },
          grid: { ...gridStyle, borderDash: [4, 4] },
          border: { display: false },
        }
      }
    }
  });
  // Elementos da modal e formulário
  const modalContainer = document.getElementById('modalCriarEquipe');
  const modalTitulo = document.getElementById('modalTitulo');
  const formCriarEquipe = document.getElementById('formCriarEquipe');
  const inputIdIndicador = document.getElementById('id_indicador');
  const inputNomeEquipe = document.getElementById('nome_equipe');
  const btnDeletarEquipe = document.getElementById('btnDeletarEquipe');

  const selectFuncionario = document.getElementById('select_funcionario');
  const btnAdicionar = document.getElementById('btnAdicionarFuncionario');
  const listaFuncionarios = document.getElementById('listaFuncionarios');

  // Botão para criar nova equipe
  const botaoAbrirModal = document.querySelector('.criar_equipe');

  // Funções de controle da modal
  function abrirModal() {
    modalContainer.classList.add('active');
    modalContainer.setAttribute('aria-hidden', 'false');
  }

  function fecharModal() {
    modalContainer.classList.remove('active');
    modalContainer.setAttribute('aria-hidden', 'true');
  }

  // Prepara a modal no MODO DE CRIAÇÃO
  botaoAbrirModal.addEventListener('click', () => {
    modalTitulo.textContent = 'Criar equipe';
    inputIdIndicador.value = '';
    formCriarEquipe.reset();
    listaFuncionarios.innerHTML = '';
    btnDeletarEquipe.style.display = 'none';
    abrirModal();
  });

  // Prepara a modal no MODO DE EDIÇÃO ao clicar num Card de Equipe
  document.querySelectorAll('.rank-card').forEach(card => {
    card.addEventListener('click', () => {
      const id = card.dataset.id;
      const nome = card.dataset.nome;
      const funcionariosString = card.dataset.funcionarios; // Retorna ex: "Ana Souza, Carlos Eduardo"

      // Configura o cabeçalho e campos
      modalTitulo.textContent = 'Editar equipe';
      inputIdIndicador.value = id;
      inputNomeEquipe.value = nome;
      btnDeletarEquipe.style.display = 'inline-block';
      listaFuncionarios.innerHTML = '';

      // Mapeia e preenche a lista de funcionários vinculados
      if (funcionariosString && funcionariosString.trim() !== '') {
        const nomes = funcionariosString.split(',').map(n => n.trim());

        nomes.forEach(nomeFunc => {
          // Busca a opção correspondente dentro do <select> pelo nome do funcionário
          const option = Array.from(selectFuncionario.options).find(opt => opt.text.trim() === nomeFunc);
          if (option) {
            adicionarTagFuncionario(option.value, option.text);
          }
        });
      }

      abrirModal();
    });
  });

  // Função utilitária para adicionar tags na lista de integrantes
  function adicionarTagFuncionario(id, nome) {
    if (document.querySelector(`li[data-id="${id}"]`)) {
      return; // Evita duplicados
    }

    const li = document.createElement('li');
    li.className = 'employee-tag';
    li.dataset.id = id;
    li.innerHTML = `
      <span>${nome}</span>
      <input type="hidden" name="funcionarios[]" value="${id}">
      <button type="button" class="btn-remove-tag" aria-label="Remover">&times;</button>
    `;

    li.querySelector('.btn-remove-tag').addEventListener('click', (e) => {
      e.stopPropagation(); // Evita disparar eventos de clique pai
      li.remove();
    });

    listaFuncionarios.appendChild(li);
  }

  // Ação do botão "+" no select de funcionários
  btnAdicionar.addEventListener('click', () => {
    const valor = selectFuncionario.value;
    const texto = selectFuncionario.options[selectFuncionario.selectedIndex]?.text;

    if (!valor) return;

    if (document.querySelector(`li[data-id="${valor}"]`)) {
      alert('Este funcionário já foi adicionado.');
      return;
    }

    adicionarTagFuncionario(valor, texto);
    selectFuncionario.value = '';
  });