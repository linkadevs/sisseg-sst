<?php
session_start();

if (!isset($_SESSION['id_atividade_modulo']) || $_SESSION['id_atividade_modulo'] <= 0) {
    header('Location: selecao_atividade.php');
    exit;
}

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


$id_atividade = $_SESSION['id_atividade_modulo'];
$nome_atividade = $_SESSION['nome_atividade_modulo'] ?? 'Atividade não encontrada';
$nome_nr = $_SESSION['nr_atividade_modulo'] ?? 'NR não atribuído';
$id_nr_fk = $_SESSION['idnr_atividade_modulo'] ?? 0;
$quantidade_epis = $_SESSION['qtdepis_atividade_modulo'] ?? 0;
$icone_atividade = $_SESSION['icone_atividade_modulo'] ?? '';


$setor_funcionario = $_SESSION['setor_funcionario'] ?? 'Manutenção de Máquinas';

$pontos_ganhos = 950;


require_once __DIR__ . '/../Controller/ModuloVerificacaoeEpiController.php';
$controller = new Controller\ModuloVerificacaoeEpiController();


$pontuacao_antes = $controller->obterPontuacaoSetor($setor_funcionario);


$chave_pontos = 'pontos_adicionados_' . $id_atividade . '_' . md5($setor_funcionario);

if (!isset($_SESSION[$chave_pontos]) || $_SESSION[$chave_pontos] !== true) {

    $controller->atualizarPontuacaoSetor($setor_funcionario, $pontos_ganhos);
    $_SESSION[$chave_pontos] = true;
    
  
    $pontuacao_final = $controller->obterPontuacaoSetor($setor_funcionario);
    $pontos_foram_adicionados = true;
    $mensagem_pontos = 'Você ganhou <strong>+' . $pontos_ganhos . ' pontos</strong> para o setor!';
} else {
 
    $pontuacao_final = $controller->obterPontuacaoSetor($setor_funcionario);
    $pontos_foram_adicionados = false;
    $mensagem_pontos = 'Você está contribuindo para uma obra mais segura';
}


$epis = $controller->obterepis($id_atividade);
$norma = $controller->exibirNorma($id_nr_fk);
$total_epis = count($epis);

$icone_exibicao = isset($icons[$icone_atividade]) ? $icons[$icone_atividade] : '📌';
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liberado para Trabalho</title>
    <link rel="stylesheet" href="../templates/assets/css/liberacao.css">
</head>

<body>

    <nav class="navbar">
        <div class="nav-conteudo">
            <div class="logo">
                <figure>
                    <img src="../templates/assets/img/marca-de-verificacao.png" alt="Verificado">
                </figure>
                <div class="logo-texto">
                    <p class="app-nome">SISSEG OBRA</p>
                    <p class="app-sub">Sistema de Segurança</p>
                </div>
            </div>
            <div class="inicio">
                <figure>
                    <img src="../templates/assets/img/botao-de-inicio.png" alt="Início">
                </figure>
            </div>
        </div>
    </nav>

    <div class="container">
        <header class="status-header">
            <div class="status-topo">
                <figure class="icone-check">
                    <span style="font-size: 60px;"><?php echo $icone_exibicao; ?></span>
                </figure>
                <div class="status-titulo">
                    <h1>Liberado para Trabalho</h1>
                    <p>Você está apto e com os EPIs corretos</p>
                </div>
            </div>

            <div class="status-detalhes">
                <div class="linha-detalhe">
                    <span class="label">Atividade:</span>
                    <span class="valor"><?php echo htmlspecialchars($nome_atividade); ?></span>
                </div>
                <div class="linha-detalhe">
                    <span class="label">EPIs Confirmados:</span>
                    <span class="valor"><?php echo $total_epis; ?></span>
                </div>
                <div class="linha-detalhe">
                    <span class="label">NR:</span>
                    <span class="valor"><?php echo htmlspecialchars($nome_nr); ?></span>
                </div>
                <div class="linha-detalhe">
                    <span class="label">Setor:</span>
                    <span class="valor"><?php echo htmlspecialchars($setor_funcionario); ?></span>
                </div>
            </div>
        </header>

        <main>
            <div class="grid-info">

                <div class="card-info">
                    <div class="card-info-topo">
                        <figure class="icone-box azul">
                            <img src="../templates/assets/img/capacete-de-seguranca.png" alt="Ícone Capacete">
                        </figure>
                        <h2>EPIs Verificados</h2>
                    </div>
                    <p class="descricao">Todos os <?php echo $total_epis; ?> EPIs obrigatórios foram confirmados para <?php echo htmlspecialchars($nome_atividade); ?></p>
                    <div class="tags-container">
                        <?php 
                        $contador = 0;
                        foreach ($epis as $epi): 
                            $contador++;
                            if ($contador <= 3): 
                        ?>
                            <span class="tag-verde"><?php echo htmlspecialchars($epi['nome_epi']); ?></span>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                        <?php if ($total_epis > 3): ?>
                            <span class="tag-cinza">+<?php echo ($total_epis - 3); ?> mais</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card-info">
                    <div class="card-info-topo">
                        <figure class="icone-box roxo">
                            <img src="../templates/assets/img/arquivo.png" alt="Ícone Documento">
                        </figure>
                        <h2>Norma Aplicável</h2>
                    </div>
                    <p class="descricao">Esta atividade está regulamentada pela <?php echo htmlspecialchars($nome_nr); ?></p>
                    <?php if (!empty($norma['descricao_nr'])): ?>
                        <p class="descricao-norma" style="font-size: 14px; color: #666; margin-top: 5px;">
                            <?php echo htmlspecialchars(substr($norma['descricao_nr'], 0, 100)) . '...'; ?>
                        </p>
                    <?php endif; ?>
                    <a href="#" class="link-detalhes" onclick="verDetalhesNorma()">Ver detalhes da norma →</a>
                </div>

            </div>

            <section class="indicadores-secao">
                <h3>Indicadores de Segurança</h3>
                <div class="grid-indicadores">

                    <div class="card-indicador verde">
                        <p class="numero">15</p>
                        <p class="legenda">Dias sem incidentes</p>
                    </div>

                    <div class="card-indicador azul">
                        <p class="numero">100%</p>
                        <p class="legenda">Adesão EPIs</p>
                    </div>

                </div>
            </section>

            <!-- ============================================ -->
            <!-- CARD DE GAMIFICAÇÃO                -->
            <!-- ============================================ -->
            <footer class="gamificacao-footer">
                <div class="premiacao-esquerda">
                    <figure class="icone-medalha">
                        <img src="../templates/assets/img/distintivo.png" alt="Ícone de Medalha">
                    </figure>
                    <div class="premiacao-texto">
                        <p class="titulo-parabens">Parabéns!</p>
                        <p class="sub-parabens">
                            <?php echo $mensagem_pontos; ?>
                        </p>
                        <?php if ($pontos_foram_adicionados): ?>
                            <p style="font-size: 12px; color: #fbfefc; margin-top: 2px;">
                                Pontuação atualizada com sucesso!
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="premiacao-direita">
                    <p class="pontos"><?php echo number_format($pontuacao_final, 0, ',', '.'); ?> pts</p>
                    <p class="pontos-label">
                        <?php if ($pontos_foram_adicionados): ?>
                            Pontuação atual do setor
                        <?php else: ?>
                            Pontos do setor
                        <?php endif; ?>
                    </p>
                   
                </div>
            </footer>
        </main>
    </div>

    <script>
        const btninicio = document.querySelector('.inicio');

        if (btninicio) {
            btninicio.addEventListener('click', function() {
                window.location.href = 'pagina_principal_funcionario.html';
            });
        }

        function verDetalhesNorma() {
            window.location.href = 'norma_aplicavel.php';
        }

        const normaaplicavel = document.querySelector('.link-detalhes');

        if (normaaplicavel) {
            normaaplicavel.addEventListener('click', function(e) {
                e.preventDefault();
                verDetalhesNorma();
            });
        }
    </script>
</body>

</html>