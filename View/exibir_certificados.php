<?php
session_start();

// ============================================
// ID DO FUNCIONÁRIO VIA SESSION
// ============================================
$id_funcionario = $_SESSION['id_funcionario'];

require_once __DIR__ . '/../Controller/ExibirCertificadosController.php';

$controller = new Controller\ExibirCertificadosController();

$funcionario = $controller->buscarFuncionario($id_funcionario);
$treinamentos = $controller->buscarTreinamentos($id_funcionario);

$total = $controller->totalTreinamentos($id_funcionario);
$total_validos = $controller->totalValidos($id_funcionario);
$total_invalidos = $controller->totalInvalidos($id_funcionario);

// ============================================
// FILTRO VIA SESSION 
// ============================================
if (isset($_POST['filtro'])) {
    $_SESSION['filtro_certificados'] = $_POST['filtro'];
}

$filtro = $_SESSION['filtro_certificados'] ?? 'todos';

// Aplica o filtro nos treinamentos
if ($filtro === 'validos') {
    $treinamentos_filtrados = array_filter($treinamentos, function($item) {
        return $item['status_treinamento'] === 'valido';
    });
} elseif ($filtro === 'invalidos') {
    $treinamentos_filtrados = array_filter($treinamentos, function($item) {
        return $item['status_treinamento'] === 'invalido';
    });
} else {
    $treinamentos_filtrados = $treinamentos;
}

$nome_funcionario = $funcionario['nome_funcionario'] ?? 'Funcionário';
$iniciais = '';
$partes = explode(' ', $nome_funcionario);
if (count($partes) >= 2) {
    $iniciais = strtoupper(substr($partes[0], 0, 1) . substr($partes[1], 0, 1));
} else {
    $iniciais = strtoupper(substr($nome_funcionario, 0, 2));
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SST - Diagnóstico de Treinamentos do Funcionário</title>
    <link rel="stylesheet" href="../templates/assets/css/exibir certificados.css">
</head>

<body>
    <div class="container">
        <div class="nav-interna">
            <a href="Lista_funcionarios.html" class="btn-voltar">← Voltar para Lista de Funcionários</a>
        </div>

        <header class="perfil-colaborador-header">
            <div class="bloco-usuario">
                <div class="avatar-fake"><?php echo $iniciais; ?></div>
                <div class="dados-usuario">
                    <h1><?php echo htmlspecialchars($nome_funcionario); ?></h1>
                    <div class="tags-perfil">
                        <span class="tag-perfil-item cargo">Cargo: <?php echo htmlspecialchars($funcionario['cargo_funcionario'] ?? 'Não informado'); ?></span>
                        <span class="tag-perfil-item setor">Setor: <?php echo htmlspecialchars($funcionario['setor_funcionario'] ?? 'Não informado'); ?></span>
                    </div>
                </div>
            </div>
        </header>

        <section class="contador-e-filtros">
            <form method="POST" class="abas-filtros" id="form-filtro">
                <button type="submit" name="filtro" value="todos" class="aba-btn <?php echo $filtro === 'todos' ? 'active' : ''; ?>">TODOS (<?php echo $total; ?>)</button>
                <button type="submit" name="filtro" value="validos" class="aba-btn <?php echo $filtro === 'validos' ? 'active' : ''; ?>">VÁLIDOS (<?php echo $total_validos; ?>)</button>
                <button type="submit" name="filtro" value="invalidos" class="aba-btn <?php echo $filtro === 'invalidos' ? 'active' : ''; ?>">INVÁLIDOS (<?php echo $total_invalidos; ?>)</button>
            </form>
        </section>

        <div class="titulo-secao">
            <h2>Matriz de Treinamentos e Conformidade</h2>
        </div>

        <main class="lista-treinamentos">
            <?php if (!empty($treinamentos_filtrados)): ?>
                <?php foreach ($treinamentos_filtrados as $treinamento): ?>
                    <?php 
                        $status = $treinamento['status_treinamento'] === 'valido' ? 'valido' : 'invalido';
                        $status_label = $status === 'valido' ? 'VÁLIDO*' : 'INVÁLIDO';
                        $data_conclusao = isset($treinamento['data_funcionario_treinamento']) 
                            ? date('d/m/Y', strtotime($treinamento['data_funcionario_treinamento'])) 
                            : 'Não realizado';
                        $mensagem_alerta = '';
                        if ($status === 'invalido') {
                            if (isset($treinamento['data_validade']) && $treinamento['data_validade'] < date('Y-m-d')) {
                                $dias = (new DateTime($treinamento['data_validade']))->diff(new DateTime())->days;
                                $mensagem_alerta = 'Vencido há ' . $dias . ' dias';
                            } else {
                                $mensagem_alerta = 'Não Realizado / Incompleto';
                            }
                        }
                        
                        $descricao = $treinamento['subtitulo_treinamento'] ?? $treinamento['nome_prova'] ?? 'Treinamento NR';
                    ?>
                    <div class="linha-treinamento <?php echo $status; ?>">
                        <div class="col-nr">
                            <span class="badge-nr"><?php echo htmlspecialchars($treinamento['nr_treinamento'] ?? 'NR-XX'); ?></span>
                        </div>
                        <div class="col-info">
                            <h3><?php echo htmlspecialchars($treinamento['nome_prova'] ?? $treinamento['nome_treinamento']); ?></h3>
                            <p class="descricao"><?php echo htmlspecialchars($descricao); ?></p>
                        </div>
                        <div class="col-meta">
                            <span class="meta-item">🕒 <?php echo htmlspecialchars($treinamento['carga_horaria_treinamento'] ?? 'N/A'); ?> horas</span>
                            <span class="meta-item-sub <?php echo $status === 'invalido' ? 'alert' : ''; ?>">Conclusão: <?php echo $data_conclusao; ?></span>
                            <?php if ($status === 'invalido' && !empty($mensagem_alerta)): ?>
                                <span class="meta-item-sub alert"><?php echo $mensagem_alerta; ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="col-status">
                            <span class="status-tag <?php echo $status; ?>"><?php echo $status_label; ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="linha-treinamento">
                    <div class="col-info" style="width: 100%; text-align: center; padding: 40px 0;">
                        <p style="color: #666; font-size: 16px;">Nenhum treinamento encontrado para este filtro.</p>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>

</html>