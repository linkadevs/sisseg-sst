<?php
// Carrega o autoload do Composer (ajuste o caminho se necessário)
require_once '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// 1. Conexão com o banco de dados
try {
    $pdo = new PDO("mysql:host=localhost;dbname=sisseg-sst;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

// 2. Busca os incidentes e atividades vinculadas
$sql = "SELECT 
            i.id_incidente,
            i.data_incidente,
            i.tipo_incidente,
            i.local_incidente,
            i.gravidade_incidente,
            i.descricao_incidente,
            i.acao_imediata_incidente,
            a.nome_atividade
        FROM incidente i
        LEFT JOIN atividade a ON i.id_atividade_fk = a.id_atividade
        ORDER BY i.data_incidente DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$incidentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Montagem do HTML formatado para PDF
$html = '
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #333; }
        h1 { color: #991b1b; border-bottom: 2px solid #991b1b; padding-bottom: 5px; font-size: 16px; }
        p { font-size: 9px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; word-wrap: break-word; }
        th { background-color: #fee2e2; color: #991b1b; font-size: 9px; }
        tr:nth-child(even) { background-color: #fcf8f8; }
    </style>
</head>
<body>
    <h1>Histórico de Acidentes e Análise de Causas</h1>
    <p>Data de emissão: ' . date('d/m/Y H:i') . '</p>

    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Tipo</th>
                <th>Atividade</th>
                <th>Local</th>
                <th>Gravidade</th>
                <th>Descrição</th>
                <th>Ação Imediata</th>
            </tr>
        </thead>
        <tbody>';

foreach ($incidentes as $item) {
    $dataIncidente = $item['data_incidente'] ? date('d/m/Y', strtotime($item['data_incidente'])) : 'N/A';
    $html .= '<tr>
        <td>' . $dataIncidente . '</td>
        <td>' . htmlspecialchars($item['tipo_incidente']) . '</td>
        <td>' . htmlspecialchars($item['nome_atividade'] ?? 'N/A') . '</td>
        <td>' . htmlspecialchars($item['local_incidente']) . '</td>
        <td><strong>' . htmlspecialchars($item['gravidade_incidente']) . '</strong></td>
        <td>' . htmlspecialchars($item['descricao_incidente']) . '</td>
        <td>' . htmlspecialchars($item['acao_imediata_incidente']) . '</td>
    </tr>';
}

$html .= '
        </tbody>
    </table>
</body>
</html>';

// 4. Configuração e Renderização do Dompdf
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape'); // Formato paisagem recomendado devido à quantidade de colunas
$dompdf->render();

// 5. Força o Download do PDF
$filename = "Relatorio_Acidentes_Incidentes_" . date('Y-m-d') . ".pdf";
$dompdf->stream($filename, ["Attachment" => true]);
exit;
