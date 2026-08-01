<?php
session_start();

require_once __DIR__ . '/../Controller/ModuloInspecaoController.php';

$controller = new Controller\ModuloInspecaoController();

$termo_busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';

if (!empty($termo_busca)) {
    $inspecoes = $controller->buscarInspecoes($termo_busca);
} else {
    $inspecoes = $controller->listarInspecoes();
}

$total_inspecoes = $controller->contarTotalInspecoes();

function formatarStatus($status)
{
    switch ($status) {
        case 'Aprovado':
            return ['label' => 'Liberado', 'classe' => 'verde'];
        case 'Reprovado':
            return ['label' => 'Recusado', 'classe' => 'vermelho'];
        case 'Pendente':
            return ['label' => 'Pendente', 'classe' => 'amarelo'];
        default:
            return ['label' => $status, 'classe' => 'cinza'];
    }
}

function formatarDataHora($data_hora)
{
    $data = new DateTime($data_hora);
    return $data->format('d/m/Y \à\s H:i');
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Inspeções</title>
    <link rel="stylesheet" href="../templates/assets/css/registro_inspecoes.css">
</head>

<body>
    <div class="container">
        <div class="nav-interna">
            <a href="modulo_funcoes.php" class="btn-voltar">
                <img src="../templates/assets/img/seta-cinza-esquerda.png" alt="Voltar"> Voltar ao Painel
            </a>
        </div>

        <header class="header-registro">
            <div class="titulo-sessao">
                <h1>Registro de Inspeções</h1>
                <p>Histórico completo de inspeções de EPIs por função</p>
            </div>
            <div class="resumo-cards-dia">
                <div class="mini-card azul">
                    <p class="qtd"><?php echo $total_inspecoes; ?></p>
                    <p class="legenda">Total de Inspeções</p>
                </div>
            </div>
        </header>

        <main>
            <div class="barra-ferramentas">
                <div class="campo-busca">
                    <img src="../templates/assets/img/lupa_azul.png" alt="Buscar" class="lupa-busca">
                    <input type="text" placeholder="Busque por colaborador, função, setor ou status" id="busca-inspecao" value="<?php echo htmlspecialchars($termo_busca); ?>">
                </div>
            </div>

            <div class="card-tabela-container">
                <table class="tabela-inspecoes">
                    <thead>
                        <tr>
                            <th>Colaborador</th>
                            <th>Função</th>
                            <th>Setor</th>
                            <th>Data e Hora</th>
                            <th>EPIs Verificados</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($inspecoes)): ?>
                            <?php foreach ($inspecoes as $inspecao): ?>
                                <?php 
                                    $status_info = formatarStatus($inspecao['status_inspecao']);
                                    $data_hora = formatarDataHora($inspecao['data_hora_inspecao']);
                                    $total_epis = $inspecao['epis_verificados_inspecao'] ?? 0;
                                    $total_esperado = 6;
                                    $epis_text = $total_epis . ' / ' . $total_esperado;
                                    $classe_epis = ($total_epis < $total_esperado) ? 'de-recusa' : '';
                                ?>
                                <tr>
                                    <td>
                                        <div class="colaborador-info">
                                            <p class="nome"><?php echo htmlspecialchars($inspecao['nome_funcionario']); ?></p>
                                        </div>
                                    </td>
                                    <td><span class="tag-funcao"><?php echo htmlspecialchars($inspecao['cargo_funcionario']); ?></span></td>
                                    <td><span class="tag-setor"><?php echo htmlspecialchars($inspecao['setor_funcionario']); ?></span></td>
                                    <td class="data-hora"><?php echo $data_hora; ?></td>
                                    <td class="epis-status <?php echo $classe_epis; ?>"><?php echo $epis_text; ?></td>
                                    <td><span class="status-badge <?php echo $status_info['classe']; ?>"><?php echo $status_info['label']; ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 40px 0; color: #6B7280; font-size: 1.1rem;">
                                    Nenhuma inspeção encontrada.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const buscaInput = document.getElementById('busca-inspecao');
            let timeoutId = null;

            if (buscaInput) {
                buscaInput.addEventListener('keyup', function() {
                    clearTimeout(timeoutId);
                    timeoutId = setTimeout(function() {
                        const termo = buscaInput.value.trim();
                        const url = new URL(window.location.href);
                        if (termo) {
                            url.searchParams.set('busca', termo);
                        } else {
                            url.searchParams.delete('busca');
                        }
                        window.location.href = url.toString();
                    }, 300);
                });
            }
        });
    </script>
</body>

</html>