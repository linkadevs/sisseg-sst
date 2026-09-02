<?php
// Opcional: Oculta avisos de Deprecated para não sujar o download do CSV
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

$pdo = new PDO("mysql:host=localhost;dbname=sisseg-sst;charset=utf8", "root", "");

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

$filename = "Relatorio_Treinamentos_" . date('Y-m-d') . ".csv";

// Limpa qualquer saída/espaço em branco anterior para não corromper o arquivo
if (ob_get_length()) ob_clean();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');

// BOM UTF-8 para o Excel reconhecer acentuação
fputs($output, "\xEF\xBB\xBF");

// Passando todos os parâmetros explicitamente para evitar a mensagem de Deprecated:
// fputcsv($stream, $fields, $separator, $enclosure, $escape)
fputcsv($output, ['ID', 'Treinamento', 'NR', 'Carga Horaria (h)', 'Data Limite', 'Inscritos'], ';', '"', "\\");

foreach ($treinamentos as $item) {
    $dataLimite = $item['data_limite_treinamento'] ? date('d/m/Y', strtotime($item['data_limite_treinamento'])) : 'N/A';
    
    fputcsv($output, [
        $item['id_treinamento'],
        $item['nome_treinamento'],
        $item['nr_treinamento'],
        $item['carga_horaria_treinamento'],
        $dataLimite,
        $item['total_participantes']
    ], ';', '"', "\\");
}

fclose($output);
exit;