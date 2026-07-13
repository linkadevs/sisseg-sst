/* ==========================================================================
   Simulador de Prova — NR-06 (EPIs)
   Todo o HTML das 5 questões e dos 2 resultados já existe em index.html.
   Este arquivo só faz 4 coisas:
     1) mostrar/esconder telas e questões (classe "oculto" / "ativa")
     2) validar se a questão atual foi respondida antes de avançar
     3) calcular a nota comparando as respostas com o gabarito
     4) exibir os toasts de aviso/erro
   ========================================================================== */

const NOTA_MINIMA = 7.0;
const PONTOS_POR_ACERTO = 2.0; // 5 questões x 2.0 = 10.0

/* Gabarito: índice (0 a 3) da alternativa correta de cada questão.
   O texto das perguntas e alternativas fica só no HTML — aqui só
   guardamos qual "name" de radio corresponde a qual resposta certa. */
const GABARITO = {
  'resposta-q1': 2, // 7.0
  'resposta-q2': 1, // Empregador
  'resposta-q3': 1, // Autorização do Ministério do Trabalho
  'resposta-q4': 2, // Danificado, extraviado ou vencido
  'resposta-q5': 2  // Diariamente antes do uso
};

const telaInstrucoes = document.getElementById('tela-instrucoes');
const formQuiz = document.getElementById('form-quiz');
const telaResultado = document.getElementById('tela-resultado');
const resultadoAprovado = document.getElementById('resultado-aprovado');
const resultadoReprovado = document.getElementById('resultado-reprovado');
const toastContainer = document.getElementById('toastContainer');

/* ---------- Toasts ---------- */
function mostrarToast(mensagem, tipo, duracaoMs = 3500) {
  const toast = document.createElement('div');
  toast.className = `toast ${tipo === 'erro' ? 'toast-erro' : 'toast-aviso'}`;

  const icone = tipo === 'erro'
    ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>'
    : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>';

  toast.innerHTML = `${icone}<span>${mensagem}</span>`;
  toastContainer.appendChild(toast);
  setTimeout(() => toast.remove(), duracaoMs);
}

/* ---------- Início da prova ---------- */
document.getElementById('btnIniciar').addEventListener('click', () => {
  telaInstrucoes.classList.add('oculto');
  formQuiz.classList.remove('oculto');
});

/* ---------- Navegação entre questões ---------- */
// Botões "Anterior" (com data-destino) e "Próxima" já existem no HTML
// de cada questão; aqui só ligamos o clique deles.
document.querySelectorAll('.btn-anterior[data-destino], .btn-proxima').forEach(botao => {
  botao.addEventListener('click', () => irParaQuestao(botao));
});

function irParaQuestao(botao) {
  const questaoAtual = botao.closest('.questao');

  // Só valida quando está avançando (Próxima), não ao voltar.
  if (botao.classList.contains('btn-proxima')) {
    const respondida = questaoAtual.querySelector('input[type="radio"]:checked');
    if (!respondida) {
      mostrarToast('Responda todas as questões antes de finalizar a prova.', 'aviso');
      return;
    }
  }

  const destino = document.getElementById(botao.dataset.destino);
  questaoAtual.classList.remove('ativa');
  destino.classList.add('ativa');
}

/* ---------- Finalizar prova ---------- */
document.getElementById('btnFinalizar').addEventListener('click', () => {
  const ultimaQuestao = document.getElementById('questao-5');
  const respondida = ultimaQuestao.querySelector('input[type="radio"]:checked');
  if (!respondida) {
    mostrarToast('Responda todas as questões antes de finalizar a prova.', 'aviso');
    return;
  }

  const { acertos, erros, nota } = calcularNota();
  const aprovado = nota >= NOTA_MINIMA;

  preencherResultado(aprovado ? resultadoAprovado : resultadoReprovado, acertos, erros, nota);

  formQuiz.classList.add('oculto');
  telaResultado.classList.remove('oculto');
  resultadoAprovado.classList.toggle('oculto', !aprovado);
  resultadoReprovado.classList.toggle('oculto', aprovado);

  if (!aprovado) {
    mostrarToast('Você não atingiu a nota mínima. Tente novamente!', 'erro');
  }
});

function calcularNota() {
  let acertos = 0;

  Object.entries(GABARITO).forEach(([nomeGrupo, indiceCorreto]) => {
    const escolhida = formQuiz.querySelector(`input[name="${nomeGrupo}"]:checked`);
    if (escolhida && Number(escolhida.value) === indiceCorreto) acertos++;
  });

  const totalQuestoes = Object.keys(GABARITO).length;
  return {
    acertos,
    erros: totalQuestoes - acertos,
    nota: acertos * PONTOS_POR_ACERTO
  };
}

function preencherResultado(bloco, acertos, erros, nota) {
  const sufixo = bloco.id.includes('reprovado') ? 'reprovado' : 'aprovado';
  const aproveitamento = Math.round((acertos / (acertos + erros)) * 100);

  document.getElementById(`nota-${sufixo}`).textContent = nota.toFixed(1);
  document.getElementById(`acertos-${sufixo}`).textContent = acertos;
  document.getElementById(`erros-${sufixo}`).textContent = erros;
  document.getElementById(`aproveitamento-${sufixo}`).textContent = `${aproveitamento}%`;
}

/* ---------- Refazer prova / Voltar ---------- */
// "Refazer Prova": limpa as respostas e volta para a Tela 1.
document.getElementById('btnRefazer').addEventListener('click', reiniciarProva);

// "Voltar" (topo da página e nas duas telas de resultado).
document.getElementById('btnVoltarTopo').addEventListener('click', reiniciarProva);
document.querySelectorAll('.btn-voltar-inicio').forEach(botao => {
  botao.addEventListener('click', reiniciarProva);
});

function reiniciarProva() {
  formQuiz.reset(); // desmarca todos os radios de uma vez

  document.querySelectorAll('.questao').forEach(q => q.classList.remove('ativa'));
  document.getElementById('questao-1').classList.add('ativa');

  telaResultado.classList.add('oculto');
  resultadoAprovado.classList.add('oculto');
  resultadoReprovado.classList.add('oculto');

  formQuiz.classList.add('oculto');
  telaInstrucoes.classList.remove('oculto');
}
