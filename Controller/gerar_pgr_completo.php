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

// 2. Busca os riscos no banco ordenados por tipo e nível
$sql = "SELECT 
            r.id_risco,
            r.tipo_risco,
            r.nivel_risco,
            r.probabilidade_risco,
            r.severidade_risco,
            r.descricao_risco,
            r.medidas_controle_risco,
            r.epis_relacionados_risco
        FROM risco r
        ORDER BY r.tipo_risco, r.nivel_risco DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$riscos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Montagem do HTML formatado para PDF
$html = '
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #333; }
        h1 { color: #854d0e; border-bottom: 2px solid #854d0e; padding-bottom: 5px; font-size: 15px; }
        p { font-size: 8px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; word-wrap: break-word; }
        th { background-color: #fef9c3; color: #854d0e; font-size: 9px; }
        tr:nth-child(even) { background-color: #fefce8; }
    </style>
</head>
<body>
    <h1>Inventário de Riscos Ocupacionais (PGR)</h1>
    <p>Documento gerado em: ' . date('d/m/Y H:i') . '</p>

    <table>
        <thead>
            <tr>
                <th>Tipo de Risco</th>
                <th>Descrição</th>
                <th>Nível</th>
                <th>Prob. x Sev.</th>
                <th>Medidas de Controle</th>
                <th>EPIs Relacionados</th>
            </tr>
        </thead>
        <tbody>';

foreach ($riscos as $item) {
    $html .= '<tr>
        <td>' . htmlspecialchars($item['tipo_risco']) . '</td>
        <td>' . htmlspecialchars($item['descricao_risco']) . '</td>
        <td><strong>' . htmlspecialchars($item['nivel_risco']) . '</strong></td>
        <td>' . $item['probabilidade_risco'] . ' x ' . $item['severidade_risco'] . '</td>
        <td>' . htmlspecialchars($item['medidas_controle_risco']) . '</td>
        <td>' . htmlspecialchars($item['epis_relacionados_risco']) . '</td>
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
$dompdf->setPaper('A4', 'landscape'); // Paisagem recomendado para relatórios de riscos detalhados
$dompdf->render();

// 5. Força o Download do PDF
$filename = "PGR_Programa_Gerenciamento_Riscos_" . date('Y-m-d') . ".pdf";
$dompdf->stream($filename, ["Attachment" => true]);
exit;
