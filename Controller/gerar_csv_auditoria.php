<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

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

$filename = "Relatorio_Auditoria_" . date('Y-m-d') . ".csv";

if (ob_get_length()) ob_clean();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');
fputs($output, "\xEF\xBB\xBF");

fputcsv($output, ['ID', 'Auditoria / Processo', 'Auditor Responsavel', 'Data', 'Status'], ';', '"', "\\");

foreach ($auditorias as $item) {
    fputcsv($output, [
        $item['id_auditoria'],
        $item['nome_auditoria'],
        $item['auditor_auditoria'],
        $item['data_auditoria'],
        $item['status_auditoria']
    ], ';', '"', "\\");
}

fclose($output);
exit;