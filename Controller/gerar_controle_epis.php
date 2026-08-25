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

// 2. Busca os EPIs no banco ordenados por nome
$sql = "SELECT 
            id_epi,
            nome_epi,
            ca_epi,
            qtd_epi,
            qtd_minima_epi,
            status_epi
        FROM epi
        ORDER BY nome_epi ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$epis = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Montagem do HTML formatado para PDF
$html = '
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; }
        h1 { color: #065f46; border-bottom: 2px solid #065f46; padding-bottom: 5px; font-size: 16px; }
        p { font-size: 9px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #d1fae5; color: #065f46; font-size: 10px; }
        tr:nth-child(even) { background-color: #f0fdf4; }
        .alerta { color: #dc2626; font-weight: bold; }
        .ok { color: #065f46; }
    </style>
</head>
<body>
    <h1>Controle de Equipamentos de Proteção Individual (EPI)</h1>
    <p>Data da consulta: ' . date('d/m/Y H:i') . '</p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Equipamento</th>
                <th>C.A.</th>
                <th>Qtd Atual</th>
                <th>Qtd Mínima</th>
                <th>Status do Estoque</th>
            </tr>
        </thead>
        <tbody>';

foreach ($epis as $item) {
    $statusHtml = ($item['qtd_epi'] <= $item['qtd_minima_epi']) 
        ? '<span class="alerta">REPOSIÇÃO NECESSÁRIA</span>' 
        : '<span class="ok">OK</span>';

    $html .= '<tr>
        <td>' . $item['id_epi'] . '</td>
        <td>' . htmlspecialchars($item['nome_epi']) . '</td>
        <td>' . htmlspecialchars($item['ca_epi']) . '</td>
        <td>' . $item['qtd_epi'] . '</td>
        <td>' . $item['qtd_minima_epi'] . '</td>
        <td>' . $statusHtml . '</td>
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
$dompdf->setPaper('A4', 'portrait'); // Retrato é suficiente para esta tabela
$dompdf->render();

// 5. Força o Download do PDF
$filename = "Controle_Estoque_EPIs_" . date('Y-m-d') . ".pdf";
$dompdf->stream($filename, ["Attachment" => true]);
exit;
