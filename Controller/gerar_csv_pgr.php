<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

$pdo = new PDO("mysql:host=localhost;dbname=sisseg-sst;charset=utf8", "root", "");

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

$filename = "PGR_Riscos_" . date('Y-m-d') . ".csv";

if (ob_get_length()) ob_clean();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');
fputs($output, "\xEF\xBB\xBF");

fputcsv($output, ['Tipo Risco', 'Descricao', 'Nivel', 'Probabilidade', 'Severidade', 'Medidas de Controle', 'EPIs Relacionados'], ';', '"', "\\");

foreach ($riscos as $item) {
    fputcsv($output, [
        $item['tipo_risco'],
        $item['descricao_risco'],
        $item['nivel_risco'],
        $item['probabilidade_risco'],
        $item['severidade_risco'],
        $item['medidas_controle_risco'],
        $item['epis_relacionados_risco']
    ], ';', '"', "\\");
}

fclose($output);
exit;