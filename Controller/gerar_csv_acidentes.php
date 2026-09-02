<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

$pdo = new PDO("mysql:host=localhost;dbname=sisseg-sst;charset=utf8", "root", "");

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

$filename = "Relatorio_Acidentes_" . date('Y-m-d') . ".csv";

if (ob_get_length()) ob_clean();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');
fputs($output, "\xEF\xBB\xBF");

fputcsv($output, ['Data', 'Tipo', 'Atividade/Setor', 'Local', 'Gravidade', 'Descricao', 'Acao Imediata'], ';', '"', "\\");

foreach ($incidentes as $item) {
    fputcsv($output, [
        date('d/m/Y', strtotime($item['data_incidente'])),
        $item['tipo_incidente'],
        $item['nome_atividade'] ?? 'N/A',
        $item['local_incidente'],
        $item['gravidade_incidente'],
        $item['descricao_incidente'],
        $item['acao_imediata_incidente']
    ], ';', '"', "\\");
}

fclose($output);
exit;