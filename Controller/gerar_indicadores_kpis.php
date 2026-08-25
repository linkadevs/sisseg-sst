<?php
// Carrega o autoload do Composer (ajuste o caminho se necessário)
require_once '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// 1. Conexão com o banco de dados
try {
    $pdo = new PDO("mysql:host=localhost;dbname=sisseg_sst;charset=utf8", "maia", "280805");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

// 2. Busca os indicadores de performance no banco ordenados por pontuação
$sql = "SELECT 
            nome_equipe_indicadores,
            treinamento_percentual_indicadores,
            dias_sem_acidentes_indicadores,
            epi_percentual_indicadores,
            pontos_indicadores
        FROM indicadores
        ORDER BY pontos_indicadores DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$kpis = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Montagem do HTML formatado para PDF
$html = '
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; }
        h1 { color: #312e81; border-bottom: 2px solid #312e81; padding-bottom: 5px; font-size: 16px; }
        p { font-size: 9px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #e0e7ff; color: #312e81; font-size: 10px; }
        tr:nth-child(even) { background-color: #f5f3ff; }
    </style>
</head>
<body>
    <h1>Relatório de Indicadores de Performance (KPIs - SST)</h1>
    <p>Gerado em: ' . date('d/m/Y H:i') . '</p>

    <table>
        <thead>
            <tr>
                <th>Equipe / Setor</th>
                <th>Dias sem Acidentes</th>
                <th>Conformidade Treinamentos</th>
                <th>Uso Correto de EPIs</th>
                <th>Pontuação Total</th>
            </tr>
        </thead>
        <tbody>';

foreach ($kpis as $item) {
    $html .= '<tr>
        <td><strong>' . htmlspecialchars($item['nome_equipe_indicadores']) . '</strong></td>
        <td>' . $item['dias_sem_acidentes_indicadores'] . ' dias</td>
        <td>' . $item['treinamento_percentual_indicadores'] . '%</td>
        <td>' . $item['epi_percentual_indicadores'] . '%</td>
        <td>' . $item['pontos_indicadores'] . ' pts</td>
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
$dompdf->setPaper('A4', 'portrait'); // Retrato atende perfeitamente este relatório
$dompdf->render();

// 5. Força o Download do PDF
$filename = "Indicadores_Performance_SST_" . date('Y-m-d') . ".pdf";
$dompdf->stream($filename, ["Attachment" => true]);
exit;
