<?php
session_start();

if (!isset($_SESSION['id_funcionario']) || $_SESSION['id_funcionario'] <= 0) {
    $_SESSION['id_funcionario'] = 1;
}

$id_funcionario = $_SESSION['id_funcionario'];

require_once __DIR__ . '/../Controller/PrincipalFuncionarioController.php';

$controller = new Controller\PrincipalFuncionarioController();

$funcionario = $controller->buscarFuncionario($id_funcionario);

// Progresso de treinamentos
$progresso = $controller->progressoTreinamentos($id_funcionario);

// Dias sem incidentes
$dias_sem_incidentes = $controller->diasSemIncidentes();

// Incidentes abertos
$incidentes_abertos = $controller->incidentesAbertos();

// Notificações (últimas 3)
$notificacoes = $controller->notificacoes();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Principal</title>
    <link rel="stylesheet" href="../templates/assets/css/pagina_principal_funcionario.css">
</head>

<body>
    <main>
        <div class="container">

            <div class="linha1">
                <div class="bloco1">
                    <div class="bloco1-foto">
                        <figure>
                            <img src="../templates/assets/img/empresa.png" alt="empresa">
                        </figure>
                    </div>
                    <div class="bloco1-texto">
                        <p class="texto-titulo">SISSEG SST</p>
                        <p class="texto-subtitulo">Sistema de Segurança do Trabalho</p>
                    </div>
                </div>
                <div class="bloco2">
                    <p class="titulo">Certificado</p>
                    <p class="subtitulo">ISO 45001:2018</p>
                </div>
            </div>

            <div class="linha2">
                <div class="bloco3">
                    <p class="titulo">Bem-vindo(a)</p>
                    <p class="subtitulo"><?php echo htmlspecialchars($funcionario['nome_funcionario'] ?? 'Funcionário'); ?></p>
                    <a href="#" class="perfil-link">Ver perfil</a>
                </div>
            </div>

            <div class="linha3">
                <div class="bloco4">
                    <p class="titulo">Sistema de Gestão SST</p>
                    <p class="subtitulo">Conforme ISO 45001:2018</p>
                </div>
                <div class="bloco5">
                    <p class="titulo">Normas Regulamentadoras</p>
                    <p class="subtitulo">NR-01, 06, 10, 12, 18, 23, 33, 35</p>
                </div>
                <div class="bloco6">
                    <p class="titulo">Compromisso</p>
                    <p class="subtitulo">Zero Acidentes | Melhoria Contínua</p>
                </div>
            </div>

        </div>
    </main>

    <section class="informacoes">

        <div class="card-treinamento">
            <div class="card-superior">
                <p class="card-titulo">Treinamento</p>
                <div class="card-subtitulo">
                    <p class="card-infor"><?php echo $progresso['concluidos']; ?>/<?php echo $progresso['total']; ?></p>
                </div>
            </div>
            <div class="card-inferior">
                <div class="barra-progresso">
                    <div class="barra-preenchida" style="width: <?php echo $progresso['percentual']; ?>%;"></div>
                </div>
                <p class="card-treinamento-msg">
                    <?php echo $progresso['percentual']; ?>% concluídos
                </p>
            </div>
        </div>

        <div class="card-acidentes">
            <div class="card-superior">
                <p class="card-titulo">Dias sem incidentes</p>
                <div class="card-subtitulo">
                    <figure>
                        <img src="../templates/assets/img/relogio.png" alt="sem acidentes">
                    </figure>
                    <p class="card-infor"><?php echo $dias_sem_incidentes; ?></p>
                </div>
            </div>
            <div class="card-inferior">
                <p class="card-acidentes-msg">
                    <?php echo $dias_sem_incidentes > 10 ? 'Excelente!' : 'Acompanhe os incidentes'; ?>
                </p>
            </div>
        </div>

        <div class="card-incidentes">
            <div class="card-superior">
                <p class="card-titulo">Incidentes Abertos</p>
                <div class="card-subtitulo">
                    <figure>
                        <img src="../templates/assets/img/risco-vermelho.png" alt="risco vermelho">
                    </figure>
                    <p class="card-infor"><?php echo $incidentes_abertos; ?></p>
                </div>
            </div>
            <div class="card-inferior">
                <p class="card-incidentes-msg">
                    <?php echo $incidentes_abertos > 0 ? 'Requer atenção' : 'Nenhum incidente aberto'; ?>
                </p>
            </div>
        </div>
    </section>

    <section class="alertas">
        <h2>Alertas e Notificações</h2>

        <?php if (!empty($notificacoes)): ?>
            <?php foreach ($notificacoes as $notificacao): ?>
                <?php 
                    $classe = $notificacao['tipo'] === 'incidente' ? 'notificacao' : 'notificacao-aviso';
                    $icone = $notificacao['icone'] ?? 'risco-azul.png';
                    $classe_label = $notificacao['classe'] ?? 'notificacao-informacao';
                    $label = $notificacao['label'] ?? 'Info';
                ?>
                <div class="<?php echo $classe; ?>">
                    <div class="notificacao-infor">
                        <figure>
                            <img src="../templates/assets/img/<?php echo $icone; ?>" alt="alerta">
                        </figure>
                        <p><?php echo htmlspecialchars($notificacao['mensagem']); ?></p>
                    </div>
                    <div class="<?php echo $classe_label; ?>">
                        <p><?php echo $label; ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="notificacao-aviso">
                <div class="notificacao-infor">
                    <figure>
                        <img src="../templates/assets/img/risco-azul.png" alt="info">
                    </figure>
                    <p>Nenhuma notificação no momento.</p>
                </div>
                <div class="notificacao-informacao">
                    <p>Info</p>
                </div>
            </div>
        <?php endif; ?>

    </section>

    <section class="modulos-do-sistema">
        <h1>Módulos do Sistema</h1>
        <div class="todos-os-modulos">

            <div class="modulo">
                <div class="modulo-verificacao-img">
                    <figure>
                        <img src="../templates/assets/img/marca-de-verificacao.png" alt="Verificação">
                    </figure>
                </div>
                <div class="modulo-textos">
                    <p class="modulo-titulo">Verificação & EPIs</p>
                    <p class="modulo-descricao">Verificar aptidão e liberar trabalho</p>
                </div>
                <button class="modulo-botao" onclick="window.location.href='selecao_atividade.php'">Acessar</button>
            </div>

            <div class="modulo">
                <div class="modulo-treinamento-img">
                    <figure>
                        <img src="../templates/assets/img/treinamento.png" alt="Treinamento">
                    </figure>
                </div>
                <div class="modulo-textos">
                    <p class="modulo-titulo">Treinamentos</p>
                    <p class="modulo-descricao">Cursos e certificações</p>
                </div>
                <button class="modulo-botao" onclick="window.location.href='exibir_certificados.php'">Acessar</button>
            </div>

            <div class="modulo">
                <div class="modulo-inspecao-img">
                    <figure>
                        <img src="../templates/assets/img/marca-de-inspecao.png" alt="Inspeção">
                    </figure>
                </div>
                <div class="modulo-textos">
                    <p class="modulo-titulo">Inspeção de EPIs</p>
                    <p class="modulo-descricao">Inspeção diária por função</p>
                </div>
                <button class="modulo-botao" onclick="window.location.href='modulo_funcoes.php'">Acessar</button>
            </div>

        </div>
    </section>

    <footer class="footer-simples">
        <div class="footer-conteudo">
            <p>&copy; 2026 SISSEG SST - Todos os direitos reservados.</p>
        </div>
    </footer>

</body>

</html>