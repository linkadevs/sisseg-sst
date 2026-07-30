<?php

session_start();

require_once __DIR__ . '/../Controller/ModuloVerificacaoeEpiController.php';

// ============================================
// ARRAY COM OS ÍCONES
// ============================================
$icons = [
    'chave_inglesa' => '🔧️',
    'guindaste' => '🏗️',
    'ferramentas' => '🛠️',
    'alta_tensao' => '⚡️',
    'engrenagem' => '⚙️',
    'fogo' => '🔥',
    'escada' => '🪜',
    'trator' => '🚜',
    'caixa_pacote' => '📦',
    'caminhao' => '🚛',
    'deposito_galpao' => '🏬',
    'etiqueta' => '🏷️',
    'colete_seguranca' => '🦺',
    'bota_protecao' => '🥾',
    'oculos_protecao' => '🥽',
    'protetor_auricular' => '🎧',
    'luvas' => '🧤',
    'mascara_protecao' => '😷',
    'corda_no' => '🪢',
    'capacete_obras' => '👷‍♀️',
    'capacete_obras_sol' => '👷‍♂️'
];

$conexao = new Controller\ModuloVerificacaoeEpiController();
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

                        <div class="card" onclick="selecionarAtividade(
                            <?php echo $atividade['id_atividade']; ?>,
                            '<?php echo addslashes($atividade['nome_atividade']); ?>',
                            '<?php echo addslashes($atividade['nome_nr']); ?>',
                            '<?php echo addslashes($atividade['quantidade_epis']); ?>',
                            <?php echo $atividade['id_nr_fk']; ?>
                        )">
                            <div class="icones">
                                <figure>
                                    <?php if (!empty($atividade['icone_atividade'])): ?>
                                        <?php 
                                            
                                            $nome_icone = $atividade['icone_atividade'];
                                            $icone = isset($icons[$nome_icone]) ? $icons[$nome_icone] : '📌';
                                        ?>
                                        <span style="font-size: 35px;"><?php echo $icone; ?></span>
                                    <?php else: ?>
                                        <span style="font-size: 35px;">📌</span>
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
        function selecionarAtividade(id, nome, nr, qtd, idNr) {

            var form = document.createElement('form');
            form.method = 'POST';
            form.action = 'salvar_session.php';

            var campoId = document.createElement('input');
            campoId.type = 'hidden';
            campoId.name = 'id_atividade';
            campoId.value = id;
            form.appendChild(campoId);

            var campoNome = document.createElement('input');
            campoNome.type = 'hidden';
            campoNome.name = 'nome_atividade';
            campoNome.value = nome;
            form.appendChild(campoNome);

            var campoNr = document.createElement('input');
            campoNr.type = 'hidden';
            campoNr.name = 'nome_nr';
            campoNr.value = nr;
            form.appendChild(campoNr);

            var campoIdNr = document.createElement('input');
            campoIdNr.type = 'hidden';
            campoIdNr.name = 'id_nr_fk';
            campoIdNr.value = idNr;
            form.appendChild(campoIdNr);

            var campoQtdepis = document.createElement('input');
            campoQtdepis.type = 'hidden';
            campoQtdepis.name = 'quantidade_epis';
            campoQtdepis.value = qtd;
            form.appendChild(campoQtdepis);
            
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>

</html>