<?php
session_start();

require_once __DIR__ . '/../Controller/PrincipalAdminController.php';

use Controller\PrincipalAdminController;

$controller = new PrincipalAdminController();

$idAdm = $_SESSION['id_adm'];

$admin = $controller->buscarAdmin($idAdm);

$totalTreinamentos = $controller->totalTreinamentos();

$diasSemAcidentes = $controller->diasSemIncidentes();

$incidentes = $controller->incidentesqtd();

$notificacoes = $controller->notificacoes();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Principal do Administrador(a)</title>
    <link rel="stylesheet" href="../templates/assets/css/principal_adm.css">
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

                    <p class="subtitulo">
                        <?= htmlspecialchars($admin['nome_adm']) ?>
                    </p>

                    <a href="perfil-adm.php" class="perfil-link">
                        Ver perfil
                    </a>
                </div>

                <div class="bloco7">
                    <p class="titulo">
                        Visualize e Gerencie os <br> funcionários da sua empresa
                    </p>

                    <a href="gerenciamento_funcionarios.php" class="funcionarios-link">
                        Funcionários
                    </a>
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

                <p class="card-titulo">Total de Treinamentos</p>

                <div class="card-subtitulo">
                    <p class="card-infor">
                        <?= $totalTreinamentos ?>
                    </p>
                </div>

            </div>

    

        </div>

        <div class="card-acidentes">

            <div class="card-superior">

                <p class="card-titulo">Dias sem Incidentes</p>

                <div class="card-subtitulo">

                    <figure>
                        <img src="../templates/assets/img/relogio.png" alt="sem acidentes">
                    </figure>

                    <p class="card-infor">
                        <?= $diasSemAcidentes ?>
                    </p>

                </div>

            </div>

            <div class="card-inferior">
                <p class="card-acidentes-msg">
                    Excelente!
                </p>
            </div>

        </div>

       

        <div class="card-incidentes">

            <div class="card-superior">

                <p class="card-titulo"> Total de Incidentes </p>

                <div class="card-subtitulo">

                    <figure>
                        <img src="../templates/assets/img/risco-vermelho.png" alt="risco vermelho">
                    </figure>

                    <p class="card-infor">
                        <?= $incidentes ?>
                    </p>

                </div>

            </div>

            <div class="card-inferior">
                <p class="card-incidentes-msg">
                    Requer atenção
                </p>
            </div>

        </div>

    </section>

    <section class="alertas">

        <h2>Alertas e Notificações</h2>

        <?php if (!empty($notificacoes)): ?>

            <?php foreach ($notificacoes as $notificacao): ?>

                <div class="notificacao">

                    <div class="notificacao-infor">

                        <figure>
                            <img src="../templates/assets/img/<?= htmlspecialchars($notificacao['icone']) ?>" alt="Notificação">
                        </figure>

                        <p><?= htmlspecialchars($notificacao['mensagem']) ?></p>

                    </div>

                    <div class="<?= htmlspecialchars($notificacao['classe']) ?>">
                        <p><?= htmlspecialchars($notificacao['label']) ?></p>
                    </div>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <div class="notificacao">

                <div class="notificacao-infor">

                    <figure>
                        <img src="../templates/assets/img/risco-azul.png" alt="Notificação">
                    </figure>

                    <p>Nenhuma notificação disponível.</p>

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

                <a href="verificacao_epi_adm.php">
                    <button class="modulo-botao">Acessar</button>
                </a>
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

                <a href="treinamento-adm.php">
                    <button class="modulo-botao">Acessar</button>
                </a>
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

                <a href="modulo_funcoes.php">
                    <button class="modulo-botao">Acessar</button>
                </a>
            </div>

            <div class="modulo">
                <div class="modulo-checklistnr18-img">
                    <figure>
                        <img src="../templates/assets/img/verificacao_nr.png" alt="Checklist">
                    </figure>
                </div>

                <div class="modulo-textos">
                    <p class="modulo-titulo">Checklist NR-18</p>
                    <p class="modulo-descricao">Inspeção do canteiro de obras</p>
                </div>

                <a href="checklistpart1.php">
                    <button class="modulo-botao">Acessar</button>
                </a>
            </div>

            <div class="modulo">
                <div class="modulo-capacete-img">
                    <figure>
                        <img src="../templates/assets/img/capacete.png" alt="Capacete">
                    </figure>
                </div>

                <div class="modulo-textos">
                    <p class="modulo-titulo">Gestão de EPIs</p>
                    <p class="modulo-descricao">Controle e Check-in diário</p>
                </div>

                <a href="modulo-gestao-epis.php">
                    <button class="modulo-botao">Acessar</button>
                </a>
            </div>

            <div class="modulo">
                <div class="modulo-pgr-img">
                    <figure>
                        <img src="../templates/assets/img/pgr.png" alt="PGR">
                    </figure>
                </div>

                <div class="modulo-textos">
                    <p class="modulo-titulo">PGR / Riscos</p>
                    <p class="modulo-descricao">Programa de gerenciamento</p>
                </div>

                <a href="modulo-pgr.php">
                    <button class="modulo-botao">Acessar</button>
                </a>
            </div>

            <div class="modulo">
                <div class="modulo-atencao-img">
                    <figure>
                        <img src="../templates/assets/img/atencao.png" alt="Incidentes">
                    </figure>
                </div>

                <div class="modulo-textos">
                    <p class="modulo-titulo">Incidentes</p>
                    <p class="modulo-descricao">Registro e Análise</p>
                </div>

                <a href="modulo-incidente.html">
                    <button class="modulo-botao">Acessar</button>
                </a>
            </div>

            <div class="modulo">
                <div class="modulo-grafico-img">
                    <figure>
                        <img src="../templates/assets/img/grafico-de-barras.png" alt="Indicadores">
                    </figure>
                </div>

                <div class="modulo-textos">
                    <p class="modulo-titulo">Indicadores</p>
                    <p class="modulo-descricao">Métricas e Gamificação</p>
                </div>

                <a href="modulo-indicadores.php">
                    <button class="modulo-botao">Acessar</button>
                </a>
            </div>

            <div class="modulo">
                <div class="modulo-configuracao-img">
                    <figure>
                        <img src="../templates/assets/img/configuracao.png" alt="Admin">
                    </figure>
                </div>

                <div class="modulo-textos">
                    <p class="modulo-titulo">Admin / Compliance</p>
                    <p class="modulo-descricao">Relatórios e auditoria</p>
                </div>

                <a href="admin_compliance.php">
                    <button class="modulo-botao">Acessar</button>
                </a>
            </div>

        </div>

    </section>

    <footer class="footer-simples">
        <div class="footer-conteudo">
            <p class="footer2026">&copy; 2026 SISSEG SST - Todos os direitos reservados.</p>
        </div>
    </footer>

    <script src="../templates/assets/js/principal_adm.js"></script>

</body>

</html>