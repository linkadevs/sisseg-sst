<?php
session_start();

// TODO: substituir por sessão real quando o login de funcionário for implementado.
if (!isset($_SESSION['id_funcionario'])) {
    $_SESSION['id_funcionario'] = 1;
}

$idTreinamento = isset($_GET['id_treinamento']) ? (int) $_GET['id_treinamento'] : 0;
if ($idTreinamento <= 0) {
    header('Location: treinamento.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Simulador de Prova</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../templates/assets/css/prova.css">
</head>
<body data-id-treinamento="<?= $idTreinamento ?>">

<div class="page">
  <a href="treinamento-funcionario.html" class="back-link-wrap">
    <button class="back-link" id="btnVoltarTopo" type="button">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M5 12l7 7M5 12l7-7"/></svg>
      Voltar
    </button>
  </a>

  <main id="app">

    <!-- TELA 1 — Instruções -->
    <section id="tela-instrucoes" class="tela-instrucoes">

      <div class="cabecalho card">
        <h1 id="prova-titulo">Carregando prova...</h1>
        <p id="prova-subtitulo">Aguarde a busca das informações do treinamento.</p>
      </div>

      <div class="box-instrucoes">
        <h3>Instruções:</h3>

        <div class="item-instrucao">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
          <span>Leia atentamente todas as questões antes de responder</span>
        </div>

        <div class="item-instrucao">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
          <span>Nota mínima para aprovação: <strong>7.0 (70%)</strong></span>
        </div>

        <div class="item-instrucao">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
          <span>Você pode navegar entre as questões antes de finalizar</span>
        </div>

        <div class="item-instrucao">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
          <span>Todas as questões devem ser respondidas</span>
        </div>

        <div class="item-instrucao">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
          <span>Ao aprovar, o certificado será liberado automaticamente</span>
        </div>
      </div>

      <button class="btn btn-azul" id="btnIniciar" type="button" disabled>Iniciar Prova</button>
    </section>

    <!-- TELA 2 — Quiz (Renderizado via JS com a estrutura do Front) -->
    <form id="form-quiz" class="oculto">
      <div id="questoesProva"></div>
    </form>

    <!-- TELA 3 — Resultado -->
    <section id="tela-resultado" class="oculto">

      <!-- Resultado: Aprovado -->
      <div id="resultado-aprovado" class="card card-resultado aprovado oculto">
        <div class="icone-resultado">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
        </div>

        <h2 class="titulo-resultado">Aprovado! 🎉</h2>
        <div class="badge-nota">Nota: <span id="nota-aprovado">0.0</span>/10</div>

        <div class="grid-metricas">
          <div class="metrica">
            <div class="metrica-label">Acertos</div>
            <div class="metrica-valor verde" id="acertos-aprovado">0</div>
          </div>
          <div class="metrica">
            <div class="metrica-label">Erros</div>
            <div class="metrica-valor vermelho" id="erros-aprovado">0</div>
          </div>
          <div class="metrica">
            <div class="metrica-label">Aproveitamento</div>
            <div class="metrica-valor azul" id="aproveitamento-aprovado">0%</div>
          </div>
        </div>

        <div class="aviso-final">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
          <div>
            <strong>Parabéns!</strong>
            Você atingiu a nota mínima e seu certificado já está disponível.
          </div>
        </div>

        <div class="rodape-nav">
          <button class="btn btn-verde btn-voltar-inicio" type="button">Voltar aos Treinamentos</button>
        </div>
      </div>

      <!-- Resultado: Reprovado -->
      <div id="resultado-reprovado" class="card card-resultado reprovado oculto">
        <div class="icone-resultado">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </div>

        <h2 class="titulo-resultado">Não foi dessa vez</h2>
        <div class="badge-nota">Nota: <span id="nota-reprovado">0.0</span>/10</div>

        <div class="grid-metricas">
          <div class="metrica">
            <div class="metrica-label">Acertos</div>
            <div class="metrica-valor verde" id="acertos-reprovado">0</div>
          </div>
          <div class="metrica">
            <div class="metrica-label">Erros</div>
            <div class="metrica-valor vermelho" id="erros-reprovado">0</div>
          </div>
          <div class="metrica">
            <div class="metrica-label">Aproveitamento</div>
            <div class="metrica-valor azul" id="aproveitamento-reprovado">0%</div>
          </div>
        </div>

        <div class="aviso-final">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          <div>
            <strong>Atenção</strong>
            Você não atingiu a nota mínima de 7.0. Revise o conteúdo e tente novamente.
          </div>
        </div>

        <div class="rodape-nav">
          <button class="btn btn-azul" id="btnRefazer" type="button">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-2.64-6.36"/><path d="M21 3v6h-6"/></svg>
            Refazer Prova
          </button>
          <button class="btn btn-outline btn-voltar-inicio" type="button">Voltar</button>
        </div>
      </div>

    </section>

  </main>
</div>

<!-- Toasts fixos -->
<div id="toastContainer" class="toast-container"></div>

<script src="../templates/assets/js/prova-funcionario.js"></script>
</body>
</html>