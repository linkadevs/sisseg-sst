<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

$pdo = new PDO("mysql:host=localhost;dbname=sisseg-sst;charset=utf8", "root", "");

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

$filename = "Indicadores_KPIs_" . date('Y-m-d') . ".csv";

if (ob_get_length()) ob_clean();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');
fputs($output, "\xEF\xBB\xBF");

fputcsv($output, ['Equipe / Setor', 'Dias Sem Acidentes', 'Conformidade Treinamento (%)', 'Uso EPI (%)', 'Pontuacao Total'], ';', '"', "\\");

foreach ($kpis as $item) {
    fputcsv($output, [
        $item['nome_equipe_indicadores'],
        $item['dias_sem_acidentes_indicadores'],
        $item['treinamento_percentual_indicadores'],
        $item['epi_percentual_indicadores'],
        $item['pontos_indicadores']
    ], ';', '"', "\\");
}

fclose($output);
exit;