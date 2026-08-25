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
<title>Prova</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../templates/assets/css/prova.css">
</head>
<body data-id-treinamento="<?= $idTreinamento ?>">

<div class="page-wrap">

  <a href="treinamento.html" class="back-link" id="btnVoltarTopo">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M5 12l7 7M5 12l7-7"/></svg>
    Voltar
  </a>

  <!-- Tela 1: instruções -->
  <section id="tela-instrucoes" class="tela-prova">
    <h1 id="prova-titulo">Carregando prova...</h1>
    <p>Você precisa acertar pelo menos 70% das questões (nota mínima 7,0) para ser aprovado e gerar o certificado.</p>
    <button id="btnIniciar" class="btn-primary" disabled>Iniciar prova</button>
  </section>

  <!-- Tela 2: questões (o conteúdo de cada questão é gerado pelo prova.js) -->
  <form id="form-quiz" class="tela-prova oculto">
    <div id="questoesProva"></div>
  </form>

  <!-- Tela 3: resultado -->
  <section id="tela-resultado" class="tela-prova oculto">

    <div id="resultado-aprovado" class="resultado-box oculto">
      <h2>Aprovado! 🎉</h2>
      <p>Nota: <strong id="nota-aprovado"></strong></p>
      <p>Acertos: <span id="acertos-aprovado"></span> &bull; Erros: <span id="erros-aprovado"></span> &bull; Aproveitamento: <span id="aproveitamento-aprovado"></span></p>
      <button class="btn-secondary btn-voltar-inicio">Voltar</button>
    </div>

    <div id="resultado-reprovado" class="resultado-box oculto">
      <h2>Não foi dessa vez</h2>
      <p>Nota: <strong id="nota-reprovado"></strong></p>
      <p>Acertos: <span id="acertos-reprovado"></span> &bull; Erros: <span id="erros-reprovado"></span> &bull; Aproveitamento: <span id="aproveitamento-reprovado"></span></p>
      <button class="btn-primary" id="btnRefazer">Refazer prova</button>
      <button class="btn-secondary btn-voltar-inicio">Voltar</button>
    </div>

  </section>

  <div id="toastContainer" class="toast-container"></div>

</div>

<script src="../templates/assets/js/prova.js"></script>

</body>
</html>