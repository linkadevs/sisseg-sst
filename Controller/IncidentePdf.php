<?php
/**
 * incidente-pdf.php
 * Gera o PDF de um incidente (?id=123) e envia para download/visualização.
 * Usa a biblioteca FPDF (vendor/fpdf/fpdf.php).
 */

require_once __DIR__ . "/vendor/fpdf/fpdf.php";
require_once __DIR__ . "/Controller/IncidenteController.php";

use Controller\IncidenteController;

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(400);
    die('ID do incidente inválido.');
}

$controller = new IncidenteController();
$inc = $controller->findById($id);

if (!$inc) {
    http_response_code(404);
    die('Incidente não encontrado.');
}

/** Remove acentuação básica pra evitar caracteres quebrados na fonte padrão do FPDF (Helvetica/Latin-1) */
function pdf_txt($texto){
    $texto = (string) $texto;
    $convertido = @iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $texto);
    return $convertido !== false ? $convertido : $texto;
}

class IncidentePDF extends FPDF {
    public $subtitulo = '';

    function Header(){
        $this->SetFillColor(37, 99, 235); // #2563eb
        $this->Rect(0, 0, 210, 22, 'F');
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Helvetica', 'B', 15);
        $this->SetXY(10, 6);
        $this->Cell(0, 8, pdf_txt('SISSEG OBRA - Relatorio de Incidente'), 0, 1);
        $this->SetFont('Helvetica', '', 10);
        $this->SetXY(10, 14);
        $this->Cell(0, 6, pdf_txt($this->subtitulo), 0, 1);
        $this->SetTextColor(0, 0, 0);
        $this->Ln(14);
    }

    function Footer(){
        $this->SetY(-15);
        $this->SetFont('Helvetica', 'I', 8);
        $this->SetTextColor(120, 120, 120);
        $this->Cell(0, 10, pdf_txt('Pagina ' . $this->PageNo() . ' - Gerado em ' . date('d/m/Y H:i')), 0, 0, 'C');
    }

    function campoLabel($label){
        $this->SetFont('Helvetica', 'B', 9);
        $this->SetTextColor(90, 90, 90);
        $this->Cell(0, 5, pdf_txt(mb_strtoupper($label, 'UTF-8')), 0, 1);
    }

    function campoValor($valor, $altura = 6){
        $this->SetFont('Helvetica', '', 11);
        $this->SetTextColor(20, 20, 20);
        $this->MultiCell(0, $altura, pdf_txt($valor !== '' ? $valor : '-'));
        $this->Ln(2);
    }

    function badge($texto, $r, $g, $b){
        $this->SetFont('Helvetica', 'B', 9);
        $largura = $this->GetStringWidth(pdf_txt($texto)) + 6;
        $this->SetFillColor($r, $g, $b);
        $this->SetTextColor(255, 255, 255);
        $this->Cell($largura, 6, pdf_txt($texto), 0, 0, 'C', true);
        $this->SetTextColor(0, 0, 0);
    }
}

$gravidadeCores = [
    'alta'  => [220, 38, 38],
    'media' => [217, 119, 6],
    'baixa' => [22, 163, 74],
];
$statusCores = [
    'Aberto'       => [220, 38, 38],
    'Investigando' => [217, 119, 6],
    'Concluído'    => [22, 163, 74],
];

$gravKey = strtolower($inc['gravidade_incidente'] ?? '');
$corGrav = $gravidadeCores[$gravKey] ?? [100, 100, 100];
$corStatus = $statusCores[$inc['status_incidente'] ?? ''] ?? [100, 100, 100];

$pdf = new IncidentePDF();
$pdf->subtitulo = $inc['codigo_incidente'] . '  -  Emitido em ' . date('d/m/Y');
$pdf->AliasNbPages();
$pdf->AddPage();

// Código + badges de gravidade/status
$pdf->SetFont('Helvetica', 'B', 16);
$pdf->Cell(60, 8, pdf_txt($inc['codigo_incidente']), 0, 0);
$pdf->badge(mb_strtoupper($inc['gravidade_incidente'] ?? '-', 'UTF-8'), ...$corGrav);
$pdf->Cell(3, 6, '', 0, 0);
$pdf->badge(mb_strtoupper($inc['status_incidente'] ?? '-', 'UTF-8'), ...$corStatus);
$pdf->Ln(12);

// Grid de campos simples (2 colunas)
$colWidth = 90;
$pdf->campoLabel('Data');
$pdf->campoValor(date('d/m/Y', strtotime($inc['data_incidente'])));

$pdf->campoLabel('Tipo');
$pdf->campoValor($inc['tipo_incidente']);

$pdf->campoLabel('Local');
$pdf->campoValor($inc['local_incidente']);

$pdf->campoLabel('Atividade');
$pdf->campoValor($inc['atividade_incidente']);

$pdf->campoLabel('Descricao do Incidente');
$pdf->campoValor($inc['descricao_incidente']);

$pdf->campoLabel('Testemunhas');
$pdf->campoValor($inc['testemunhas_incidente'] ?: 'Nenhuma testemunha informada');

$pdf->campoLabel('Acao Imediata Tomada');
$pdf->campoValor($inc['acao_imediata_incidente']);

$pdf->campoLabel('Treinamento de Reciclagem Necessario');
$pdf->campoValor($inc['treinamento_reciclagem_incidente'] ?: 'Nao informado');

// Foto do local, se houver
if (!empty($inc['fotos_incidente'])) {
    $tmpImg = sys_get_temp_dir() . '/inc_' . $inc['id_incidente'] . '_' . uniqid() . '.jpg';
    file_put_contents($tmpImg, $inc['fotos_incidente']);

    $pdf->campoLabel('Foto do Local');
    $tamanho = @getimagesize($tmpImg);
    if ($tamanho) {
        $larguraMax = 120; // mm
        $wPx = $tamanho[0];
        $hPx = $tamanho[1];
        $larguraFinal = $larguraMax;
        $alturaFinal = ($hPx / $wPx) * $larguraFinal;

        if ($pdf->GetY() + $alturaFinal > 270) {
            $pdf->AddPage();
        }
        $pdf->Image($tmpImg, $pdf->GetX(), $pdf->GetY(), $larguraFinal);
        $pdf->Ln($alturaFinal + 4);
    }
    @unlink($tmpImg);
}

$nomeArquivo = 'Relatorio_' . $inc['codigo_incidente'] . '.pdf';
$pdf->Output('I', $nomeArquivo);