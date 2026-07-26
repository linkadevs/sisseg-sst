<?php
if (!isset($checklists) || !is_array($checklists)) {
    $checklists = [];
}
// Recupera o termo de pesquisa (já disponível na página)
$pesquisa = $_GET['pesquisa'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checklists NR-18</title>
    <link rel="stylesheet" href="../templates/assets/css/visualizacao_checklists.css">
</head>
<body>
    <div class="sombra"></div>
    <header>
        <button class="voltar" onclick="window.location.href='paginainicial.php'">
            <figure><img src="../templates/assets/img/seta_esquerda.png" alt=""></figure>
            Voltar
        </button>
    </header>
    <main>
        <div class="upper">
            <h1>Checklists NR-18</h1>
            <p class="gerencie">Confira todos os checklists realizados pelos Administradores.</p>
            <div class="atvs">
                <p>Total de checklists</p>
                <h2><?= count($checklists) ?></h2>
            </div>
        </div>
        <div class="search_div">
            <div class="search_input">
                <figure><img src="../templates/assets/img/lupa_azul.png" alt=""></figure>
                <form action="../Controller/ChecklistController.php?acao=pesquisar" method="GET" style="display: contents;">
                    <input type="text" name="pesquisa" id="search" placeholder="Busque por atividades"
                           value="<?= htmlspecialchars($pesquisa) ?>">
                </form>
            </div>
            <div class="buttons">
                <button type="button" id="btnLimpar">Limpar</button>
            </div>
        </div>

        <div class="grid">
            <?php if (empty($checklists)): ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 50px; font-size: 1.3rem; color: #666;">
                    <?php if (!empty($pesquisa)): ?>
                        <h3>Nenhum checklist encontrado.</h3>
                    <?php else: ?>
                        <h3>Nenhum checklist realizado.</h3>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <?php foreach ($checklists as $checklist):
                    $classe_status = strtolower(str_replace(' ', '_', $checklist['status_checklist']));
                ?>
                    <div class="card" style="cursor: pointer;"
                         onclick="window.location.href='../Controller/ChecklistController.php?acao=exibirResultado&id_checklist=<?= $checklist['id_checklist'] ?>'">
                        <div class="card_header <?= $classe_status ?>">
                            <h3 class="status_header"><?= strtoupper($checklist['status_checklist']) ?></h3>
                            <p class="progress_header">Progresso: <?= $checklist['progresso_checklist'] ?>%</p>
                        </div>
                        <ul>
                            <li><span>Responsável: </span><?= htmlspecialchars($checklist['nome_adm']) ?></li>
                            <li><span>Turno: </span><?= htmlspecialchars($checklist['turno_checklist']) ?></li>
                            <li><span>Data: </span><?= date('d/m/Y', strtotime($checklist['data_checklist'])) ?></li>
                            <li><span>Status: </span><?= htmlspecialchars($checklist['status_checklist']) ?></li>
                        </ul>
                        <div class="progress_bar">
                            <div class="progress_background <?= $classe_status ?>"
                                 style="width: <?= $checklist['progresso_checklist'] ?>%;">
                                <p class="progress_number"><?= $checklist['progresso_checklist'] ?>%</p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnLimpar = document.getElementById('btnLimpar');
            if (btnLimpar) {
                btnLimpar.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Limpa o campo e recarrega a lista sem filtro
                    document.getElementById('search').value = '';
                    window.location.href = '../Controller/ChecklistController.php?acao=listar';
                });
            }
        });
    </script>
</body>
</html>