<?php

require_once __DIR__ . '/../Controller/NormaaplicavelController.php';
$_SESSION['id_norma'] = 1;
$id_norma = $_SESSION['id_norma'] ?? '';
$conexao = new Controller\NormaaplicavelController;
$informacao = $conexao->exibirNorma($id_norma);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Norma Aplicável</title>
    <link rel="stylesheet" href="../templates/assets/css/norma_aplicavel.css">
</head>

<body>
    <div class="container">
        <header class="conteudo-norma">
            <a href="#" class="btn-voltar-topo" onclick="window.history.back()">
                <img src="../templates/assets/img/setabranca_voltar.png" alt="Voltar">
            </a>
            <h1>Norma Aplicável</h1>
        </header>

        <main>
            <div class="card-detalhe">
                <h2> <?php  echo htmlspecialchars($informacao['nome_nr'] ?? 'Não encontrada')?></h2>

                <div class="texto-explicativo">
                    <p><?php echo htmlspecialchars($informacao['descricao_nr'] ?? 'Não encontrada')?></p>
                </div>
            </div>
        </main>
    </div>
</body>

</html>