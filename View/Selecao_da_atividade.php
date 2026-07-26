<?php

require_once __DIR__ . '/../Controller/SelecaodaatividadeController.php';

$conexao = new Controller\SelecaodaatividadeController();
$atividades = $conexao->obteratividade();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleção da atividade</title>
    <link rel="stylesheet" href="../templates/assets/css/Selecao_da_atividade.css">
</head>

<body>
    <header>
        <button onclick="window.history.back()">
            <figure>
                <img src="../templates/assets/img/seta-cinza-esquerda.png" alt="Voltar">
            </figure>
        </button>
    </header>

    <div class="container">
        <div class="conteudo">
            <h1>Selecione sua atividade</h1>
            <p class="descricao">Escolha a atividade que você irá realizar hoje</p>
        </div>

        <main>
            <div class="grid">
                <?php if (!empty($atividades)): ?>
                    <?php foreach ($atividades as $atividade): ?>
                        <div class="card" onclick="selecionarAtividade(<?php echo $atividade['id_atividade']; $_SESSION['id_atividade_modulo'] = $atividade['id_atividade'];  $_SESSION['nr_atividade_modulo'] = $atividade['nome_nr'];?>)">
                            <div class="icones">
                                <figure>
                                    <?php if (!empty($atividade['foto_atividade'])): ?>

                                        <span style="font-size: 40px;"><?php echo $atividade['foto_atividade']; ?></span>
                                    <?php else: ?>
                                        <img src="../templates/assets/img/escada-de-mao.png" alt="Ícone padrão">
                                    <?php endif; ?>
                                </figure>
                                <figure class="seta">
                                    <img src="../templates/assets/img/seta-cinza.png" alt="seta cinza">
                                </figure>
                            </div>

                            <div class="titulo">
                                <p><?php echo htmlspecialchars($atividade['nome_atividade']); ?></p>
                            </div>

                            <div class="dados">
                                <div class="nr">
                                    <p><?php echo htmlspecialchars($atividade['nome_nr']); ?></p>
                                </div>
                                <div class="qtdepis">
                                    <p><?php echo $atividade['quantidade_epis']; ?> EPIs</p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="sem-atividades">Nenhuma atividade encontrada</p>
                <?php endif; ?>
            </div>

            <div class="msg">
                <p><span>Importante:</span> Selecione apenas a atividade principal que você executará. Os EPIs obrigatórios serão exibidos na próxima etapa.</p>
            </div>
        </main>
    </div>

    <script>
        function selecionarAtividade(id) {
            // Redireciona para a página de confirmação
            window.location.href = 'confirmacao_epis.php';
        
        }
    </script>
</body>

</html>