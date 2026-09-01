/* ==========================================================================
   Simulador de Prova — busca a prova real (título + questões) cadastrada
   no banco para o treinamento aberto e só faz:
     1) montar as questões na tela (a partir dos dados vindos da API)
     2) mostrar/esconder telas e questões (classe "oculto" / "ativa")
     3) validar se a questão atual foi respondida antes de avançar
     4) calcular a nota comparando as respostas com o gabarito do banco
     5) enviar o resultado para a API (gera o certificado quando aprovado)
   ========================================================================== */

const PROVA_API_URL = 'prova-api.php';
const NOTA_MINIMA = 7.0;
const LETRAS_ALTERNATIVAS = ['a', 'b', 'c', 'd', 'e'];

const idTreinamento = document.body.dataset.idTreinamento;

let questoes = [];   // vindas do banco: { id_questao, enunciado_questao, alt_a_questao..alt_e_questao, alternativa_questao }
let idProva = null;

const telaInstrucoes = document.getElementById('tela-instrucoes');
const provaTitulo = document.getElementById('prova-titulo');
const btnIniciar = document.getElementById('btnIniciar');
const formQuiz = document.getElementById('form-quiz');
const questoesProva = document.getElementById('questoesProva');
const telaResultado = document.getElementById('tela-resultado');
const resultadoAprovado = document.getElementById('resultado-aprovado');
const resultadoReprovado = document.getElementById('resultado-reprovado');
const toastContainer = document.getElementById('toastContainer');

/* ---------- Toasts ---------- */
function mostrarToast(mensagem, tipo, duracaoMs = 3500) {
  const toast = document.createElement('div');
  toast.className = `toast ${tipo === 'erro' ? 'toast-erro' : 'toast-aviso'}`;
  toast.innerHTML = `<span>${mensagem}</span>`;
  toastContainer.appendChild(toast);
  setTimeout(() => toast.remove(), duracaoMs);
}

function escapeHtml(str) {
  const div = document.createElement('div');
  div.textContent = str ?? '';
  return div.innerHTML;
}

/* ---------- Carregamento da prova ---------- */
async function carregarProva() {
  try {
    const resp = await fetch(`${PROVA_API_URL}?acao=buscar&id_treinamento=${idTreinamento}`);
    const json = await resp.json();
    if (!json.success) throw new Error(json.message || 'Erro ao buscar a prova.');

    if (!json.data) {
      provaTitulo.textContent = 'Nenhuma prova cadastrada para este treinamento ainda.';
      return;
    }

    idProva = json.data.prova.id_prova;
    questoes = json.data.questoes;

    provaTitulo.textContent = json.data.prova.nome_prova;
    montarQuestoes();
    btnIniciar.disabled = false;
  } catch (err) {
    console.error(err);
    provaTitulo.textContent = 'Não foi possível carregar a prova.';
    mostrarToast('Erro ao carregar a prova. Tente novamente.', 'erro');
  }
}

/* ---------- Montagem das questões na tela ---------- */
function montarQuestoes() {
  questoesProva.innerHTML = questoes.map((q, index) => {
    const alternativasHtml = LETRAS_ALTERNATIVAS.map(letra => `
      <label class="alternativa-opcao">
        <input type="radio" name="resposta-${q.id_questao}" value="${letra}">
        <span>${escapeHtml(q[`alt_${letra}_questao`])}</span>
      </label>
    `).join('');

    return `
      <div class="questao ${index === 0 ? 'ativa' : ''}" id="questao-${index + 1}" data-id-questao="${q.id_questao}">
        <span class="questao-numero">Questão ${index + 1} de ${questoes.length}</span>
        <h3 class="questao-enunciado">${escapeHtml(q.enunciado_questao)}</h3>
        <div class="alternativas-list">${alternativasHtml}</div>
        <div class="questao-nav">
          ${index > 0 ? `<button type="button" class="btn-anterior" data-destino="questao-${index}">Anterior</button>` : '<span></span>'}
          ${index < questoes.length - 1
            ? `<button type="button" class="btn-proxima" data-destino="questao-${index + 2}">Próxima</button>`
            : `<button type="button" class="btn btn-azul" id="btnFinalizar">Finalizar prova</button>`}
        </div>
      </div>
    `;
  }).join('');

  document.querySelectorAll('.btn-anterior, .btn-proxima').forEach(botao => {
    botao.addEventListener('click', () => irParaQuestao(botao));
  });

  const btnFinalizar = document.getElementById('btnFinalizar');
  if (btnFinalizar) btnFinalizar.addEventListener('click', finalizarProva);
}

/* ---------- Início da prova ---------- */
btnIniciar.addEventListener('click', () => {
  telaInstrucoes.classList.add('oculto');
  formQuiz.classList.remove('oculto');
});

/* ---------- Navegação entre questões ---------- */
function irParaQuestao(botao) {
  const questaoAtual = botao.closest('.questao');

  if (botao.classList.contains('btn-proxima')) {
    const respondida = questaoAtual.querySelector('input[type="radio"]:checked');
    if (!respondida) {
      mostrarToast('Responda a questão antes de avançar.', 'aviso');
      return;
    }
  }

  const destino = document.getElementById(botao.dataset.destino);
  questaoAtual.classList.remove('ativa');
  destino.classList.add('ativa');
}

/* ---------- Finalizar prova ---------- */
async function finalizarProva() {
  const ultimaQuestao = questoesProva.querySelector('.questao:last-child');
  const respondida = ultimaQuestao.querySelector('input[type="radio"]:checked');
  if (!respondida) {
    mostrarToast('Responda a última questão antes de finalizar.', 'aviso');
    return;
  }

  const { acertos, erros, nota, respostas } = calcularNota();
  const aprovado = nota >= NOTA_MINIMA;

  preencherResultado(aprovado ? resultadoAprovado : resultadoReprovado, acertos, erros, nota);

  formQuiz.classList.add('oculto');
  telaResultado.classList.remove('oculto');
  resultadoAprovado.classList.toggle('oculto', !aprovado);
  resultadoReprovado.classList.toggle('oculto', aprovado);

  if (!aprovado) {
    mostrarToast('Você não atingiu a nota mínima. Tente novamente!', 'erro');
  }

  await enviarResultado(nota, aprovado, respostas);
}

function calcularNota() {
  let acertos = 0;
  const respostas = [];

  questoes.forEach(q => {
    const escolhida = questoesProva.querySelector(`input[name="resposta-${q.id_questao}"]:checked`);
    const letraEscolhida = escolhida ? escolhida.value : null;
    if (letraEscolhida === q.alternativa_questao) acertos++;
    respostas.push({ id_questao: q.id_questao, resposta: letraEscolhida });
  });

  const total = questoes.length;
  const pontosPorAcerto = 10 / total;

  return {
    acertos,
    erros: total - acertos,
    nota: Number((acertos * pontosPorAcerto).toFixed(1)),
    respostas
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

/* ---------- Envia o resultado para o backend ---------- */
async function enviarResultado(nota, aprovado, respostas) {
  try {
    const resp = await fetch(PROVA_API_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        acao: 'registrarResultado',
        id_prova: idProva,
        id_treinamento: idTreinamento,
        nota,
        aprovado,
        respostas
      })
    });
    const json = await resp.json();
    if (!json.success) {
      console.error(json.message);
      mostrarToast('Não foi possível salvar o resultado da prova.', 'erro');
    }
  } catch (err) {
    console.error(err);
    mostrarToast('Erro de conexão ao salvar o resultado da prova.', 'erro');
  }
}

/* ---------- Refazer prova / Voltar ---------- */
document.getElementById('btnRefazer').addEventListener('click', reiniciarProva);
document.getElementById('btnVoltarTopo').addEventListener('click', reiniciarProva);
document.querySelectorAll('.btn-voltar-inicio').forEach(botao => {
  botao.addEventListener('click', () => {
    window.location.href = 'treinamento-funcionario.html'
  });
});

function reiniciarProva() {
  formQuiz.reset();
  document.querySelectorAll('.questao').forEach((q, i) => q.classList.toggle('ativa', i === 0));

  telaResultado.classList.add('oculto');
  resultadoAprovado.classList.add('oculto');
  resultadoReprovado.classList.add('oculto');

  formQuiz.classList.add('oculto');
  telaInstrucoes.classList.remove('oculto');
}

/* ---------- Inicialização ---------- */
carregarProva();