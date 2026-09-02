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

// 2. Busca os treinamentos e a contagem de colaboradores
$sql = "SELECT 
            t.id_treinamento,
            t.nome_treinamento,
            t.nr_treinamento,
            t.carga_horaria_treinamento,
            t.data_limite_treinamento,
            COUNT(ft.id_funcionario_fk) AS total_participantes
        FROM treinamento t
        LEFT JOIN funcionario_treinamento ft ON t.id_treinamento = ft.id_treinamento_fk
        GROUP BY t.id_treinamento";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$treinamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Montagem do HTML que virará PDF
$html = '
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        h1 { color: #1e3a8a; border-bottom: 2px solid #1e3a8a; padding-bottom: 5px; font-size: 18px; }
        p { font-size: 10px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f3f4f6; color: #1f2937; }
        tr:nth-child(even) { background-color: #f9fafb; }
    </style>
</head>
<body>
    <h1>Relatório de Treinamentos (Realizados e Pendentes)</h1>
    <p>Data de geração: ' . date('d/m/Y H:i') . '</p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Treinamento</th>
                <th>NR</th>
                <th>Carga</th>
                <th>Data Limite</th>
                <th>Inscritos</th>
            </tr>
        </thead>
        <tbody>';

foreach ($treinamentos as $item) {
    $dataLimite = $item['data_limite_treinamento'] ? date('d/m/Y', strtotime($item['data_limite_treinamento'])) : 'N/A';
    $html .= '<tr>
        <td>' . $item['id_treinamento'] . '</td>
        <td>' . htmlspecialchars($item['nome_treinamento']) . '</td>
        <td>' . htmlspecialchars($item['nr_treinamento']) . '</td>
        <td>' . $item['carga_horaria_treinamento'] . ' hrs</td>
        <td>' . $dataLimite . '</td>
        <td>' . $item['total_participantes'] . ' Colab.</td>
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
$dompdf->setPaper('A4', 'landscape'); // Paisagem para caber bem a tabela
$dompdf->render();

// 5. Força o Download do PDF gerado
$filename = "Relatorio_Treinamentos_" . date('Y-m-d') . ".pdf";
$dompdf->stream($filename, ["Attachment" => true]);
exit;
