<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

$pdo = new PDO("mysql:host=localhost;dbname=sisseg_sst;charset=utf8", "maia", "280805");

$sql = "SELECT 
            id_epi,
            nome_epi,
            ca_epi,
            qtd_epi,
            qtd_minima_epi
        FROM epi
        ORDER BY nome_epi ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$epis = $stmt->fetchAll(PDO::FETCH_ASSOC);

$filename = "Controle_EPIs_" . date('Y-m-d') . ".csv";

if (ob_get_length()) ob_clean();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');
fputs($output, "\xEF\xBB\xBF");

fputcsv($output, ['ID', 'Equipamento', 'C.A.', 'Qtd Atual', 'Qtd Minima', 'Status Estoque'], ';', '"', "\\");

foreach ($epis as $item) {
    $status = ($item['qtd_epi'] <= $item['qtd_minima_epi']) ? 'REPOSICAO NECESSARIA' : 'OK';

    fputcsv($output, [
        $item['id_epi'],
        $item['nome_epi'],
        $item['ca_epi'],
        $item['qtd_epi'],
        $item['qtd_minima_epi'],
        $status
    ], ';', '"', "\\");
}

fclose($output);
exit;