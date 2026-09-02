<?php
require_once '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$pdo = new PDO("mysql:host=localhost;dbname=sisseg-sst;charset=utf8", "root", "");

$sql = "SELECT 
            id_auditoria,
            nome_auditoria,
            auditor_auditoria,
            data_auditoria,
            status_auditoria
        FROM auditoria
        ORDER BY id_auditoria DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$auditorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

$html = '
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Compliance e Auditoria</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; padding: 20px; color: #333; font-size: 11px; }
        h1 { color: #4c1d95; border-bottom: 2px solid #4c1d95; padding-bottom: 8px; font-size: 16px; }
        p { font-size: 9px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #ede9fe; color: #4c1d95; font-size: 10px; }
        tr:nth-child(even) { background-color: #fbfbfe; }
    </style>
</head>
<body>
    <h1>Evidências de Compliance e Histórico de Auditorias</h1>
    <p>Emissão: ' . date('d/m/Y H:i') . '</p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Auditoria / Processo</th>
                <th>Auditor Responsável</th>
                <th>Data</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>';

foreach ($auditorias as $item) {
    $dataAuditoria = $item['data_auditoria'] ? date('d/m/Y', strtotime($item['data_auditoria'])) : 'N/A';
    $html .= '<tr>
        <td>' . $item['id_auditoria'] . '</td>
        <td>' . htmlspecialchars($item['nome_auditoria']) . '</td>
        <td>' . htmlspecialchars($item['auditor_auditoria']) . '</td>
        <td>' . $dataAuditoria . '</td>
        <td><strong>' . htmlspecialchars($item['status_auditoria']) . '</strong></td>
    </tr>';
}

$html .= '
        </tbody>
    </table>
</body>
</html>';

$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$filename = "Relatorio_Compliance_Auditorias_" . date('Y-m-d') . ".pdf";
$dompdf->stream($filename, ["Attachment" => true]);
exit;
