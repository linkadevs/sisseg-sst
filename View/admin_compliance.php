<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Controller/FuncionarioController.php';
require_once __DIR__ . '/../Controller/IncidenteController.php';
require_once __DIR__ . '/../Controller/FuncionarioTreinamentoController.php';
require_once __DIR__ . '/../Controller/EpiController.php';
require_once __DIR__ . '/../Controller/DocumentoController.php';
require_once __DIR__ . '/../Controller/AuditoriaController.php';

if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!empty($_SESSION['message'])) {
    echo '<script>alert("'. $_SESSION['message'] .'")</script>';
    unset($_SESSION['message']);
}
  
use Controller\FuncionarioController;
use Controller\IncidenteController;
use Controller\FuncionarioTreinamentoController;
use Controller\EpiController;
use Controller\DocumentoController;
use Controller\AuditoriaController;

$funcionario_controller = new FuncionarioController();
$incidente_controller = new IncidenteController();
$funcionario_treinamento_controller = new FuncionarioTreinamentoController();
$epi_controller = new EpiController();
$documento_controller = new DocumentoController();
$auditoria_controller = new AuditoriaController();

$funcionarios = $funcionario_controller->selecionarTodosOsFuncionarios();
$incidentes = $incidente_controller->selecionarTodosOsIncidentes();
$funcionarios_treinados = $funcionario_treinamento_controller->selecionarFuncionariosTreinados();
$epis = $epi_controller->selecionarTodosOsEpis();
$documentos = $documento_controller->selecionarTodosOsDocumentos();
$auditorias = $auditoria_controller->selecionarTodasAsAuditorias();

$qtd_epis = 0;
foreach($epis as $epi) {
    $qtd_epis += $epi['qtd_epi'];
}

$timezone = new DateTimeZone('America/Sao_Paulo');

$hoje = new DateTime('today', $timezone);

$incidentes_mes = 0;

foreach($incidentes as $incidente) {
    $data_incidente = new DateTime($incidente['data_incidente'], $timezone);
    if($data_incidente->format('Y-m') === $hoje->format('Y-m')) {
        $incidentes_mes++;
    }
}

$qtd_funcionarios = count($funcionarios);

if(!empty($_GET['download_id'])) {
    foreach($documentos as $documento) {
        if($documento['id_documento'] == $_GET['download_id']) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . rawurlencode($documento['nome_documento']) . '"');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . strlen($documento['arquivo_documento']));
            echo $documento['arquivo_documento'];
            exit;
        }
    }
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(!empty($_POST['titulo']) && !empty($_POST['responsavel']) && !empty($_POST['data']) && !empty($_POST['status']) && empty($_POST['id_auditoria'])) {
        $auditoria_controller->criarNovaAuditoria(
            $_POST['titulo'],
            $_POST['responsavel'],
            $_POST['data'],
            $_POST['status']
        );
    }
    if(!empty($_POST['nome']) && !empty($_POST['data_atualizacao']) && !empty($_POST['status_doc']) && !empty($_FILES['arquivo']['tmp_name']) && empty($_POST['id_documento'])) {
        $doc = file_get_contents($_FILES['arquivo']['tmp_name']);
        $documento_controller->criarNovoDocumento(
            $_POST['nome'],
            $_POST['data_atualizacao'],
            $_POST['status_doc'],
            $doc
        );
    }
    if(!empty($_POST['titulo']) && !empty($_POST['responsavel']) && !empty($_POST['data']) && !empty($_POST['status']) && !empty($_POST['id_auditoria'])) {
        $auditoria_controller->atualizarAuditoria(
            $_POST['id_auditoria'],
            $_POST['titulo'],
            $_POST['responsavel'],
            $_POST['data'],
            $_POST['status']
        );
    }
    if(!empty($_POST['nome']) && !empty($_POST['data_atualizacao']) && !empty($_POST['status_doc']) && !empty($_POST['id_documento'])) {
        if(!empty($_FILES['arquivo']['tmp_name'])) {
            $doc = file_get_contents($_FILES['arquivo']['tmp_name']);
            $documento_controller->atualizarDocumento(
                $_POST['id_documento'],
                $_POST['nome'],
                $_POST['data_atualizacao'],
                $_POST['status_doc'],
                $doc
            );
        } else {
            $documento_controller->atualizarDocumentoSemArquivo(
                $_POST['id_documento'],
                $_POST['nome'],
                $_POST['data_atualizacao'],
                $_POST['status_doc']
            );
        }
    }
    if(!empty($_POST['delete_aud'])) {
        $auditoria_controller->deletarAuditoria($_POST['delete_aud']);
    }
    if(!empty($_POST['delete_doc'])) {
        $documento_controller->deletarDocumento($_POST['delete_doc']);
    }
    header('Location: admin_compliance.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin / Compliance</title>
        <link rel="stylesheet" href="../templates/assets/css/admin_compliance.css">
    </head>
    <body>
        <button class="voltar" onclick="window.location.href = 'principal_adm.php'">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left w-4 h-4 mr-2" data-fg-bzec3="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:95:11:2606:38:e:ArrowLeft::::::EIC8" data-fgid-bzec3=":rnc:"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg>
            Voltar ao dashboard
        </button>
        <div class="title">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-settings w-10 h-10 text-gray-600" data-fg-bzec7="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:101:13:2794:48:e:Settings::::::D4nh" data-fgid-bzec7=":rnf:"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path><circle cx="12" cy="12" r="3"></circle></svg>
            <h3>Admin / Compliance</h3>
        </div>
        <p class="gestao">Gestão, relatórios e auditoria</p>
        <div class="upper">
            <div class="mini_card">
                <div class="mini_card_title">
                    <svg class="svg1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users w-5 h-5 text-blue-600" data-fg-bzec17="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:112:17:3257:43:e:Users::::::DV8M" data-fgid-bzec17=":rno:"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <p>Trabalhadores</p>
                </div>
                <h2><?= $qtd_funcionarios;?></h2>
                <p class="normal_p"><?= round(($funcionarios_treinados['count_funcionarios']/$qtd_funcionarios)*100)?>% treinados</p>
            </div>
            <div class="mini_card">
                <div class="mini_card_title">
                    <svg class="svg2" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users w-5 h-5 text-blue-600" data-fg-bzec17="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:112:17:3257:43:e:Users::::::DV8M" data-fgid-bzec17=":rno:"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <p>EPIs Ativos</p>
                </div>
                <h2><?= htmlspecialchars($qtd_epis)?></h2>
            </div>
            <div class="mini_card">
                <div class="mini_card_title">
                    <svg class="svg3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users w-5 h-5 text-blue-600" data-fg-bzec17="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:112:17:3257:43:e:Users::::::DV8M" data-fgid-bzec17=":rno:"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <p>Incidentes (mês)</p>
                </div>
                <h2><?= $incidentes_mes;?></h2>
                <?php if($incidentes_mes<=5):?>
                    <p class="green_p">Baixo</p>
                <?php endif;?>
            </div>
        </div>
        <div class="relatorios">
            <h3>Relatórios para Exportação</h3>
            <p class="relatorios_p">Gere relatórios em PDF ou Excel para auditorias e compliance</p>
            <div class="grid">
                <div class="card">
                    <div class="svg svg-verde">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text w-6 h-6" data-fg-bzec70="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:174:25:6041:28:e:Icon" data-fgid-bzec70=":rp9:"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path></svg>
                    </div>
                    <h3>Relatório de treinamentos</h3>
                    <p>Lista completa de treinamentos realizados e pendentes</p>
                    <div class="buttons">
                        <a href="../Controller/gerar_relatorio_treinamentos.php">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-down w-4 h-4 mr-1" data-fg-bzec77="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:185:27:6610:37:e:FileDown::::::EGMW" data-fgid-bzec77=":rpf:"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M12 18v-6"></path><path d="m9 15 3 3 3-3"></path></svg>
                            PDF
                        </a>
                        <a href="../Controller/gerar_csv_treinamentos.php">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-down w-4 h-4 mr-1" data-fg-bzec77="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:185:27:6610:37:e:FileDown::::::EGMW" data-fgid-bzec77=":rpf:"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M12 18v-6"></path><path d="m9 15 3 3 3-3"></path></svg>
                            Excel
                        </a>
                    </div>
                </div>
                <div class="card">
                    <div class="svg svg-vermelho">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text w-6 h-6" data-fg-bzec70="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:174:25:6041:28:e:Icon" data-fgid-bzec70=":rpo:"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path></svg>
                    </div>
                    <h3>Relatório de Acidentes</h3>
                    <p>Histórico de acidentes e análise de causas</p>
                    <div class="buttons">
                        <a href="../Controller/gerar_relatorio_acidentes.php">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-down w-4 h-4 mr-1" data-fg-bzec77="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:185:27:6610:37:e:FileDown::::::EGMW" data-fgid-bzec77=":rpf:"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M12 18v-6"></path><path d="m9 15 3 3 3-3"></path></svg>
                            PDF
                        </a>
                        <a href="../Controller/gerar_csv_acidentes.php">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-down w-4 h-4 mr-1" data-fg-bzec77="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:185:27:6610:37:e:FileDown::::::EGMW" data-fgid-bzec77=":rpf:"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M12 18v-6"></path><path d="m9 15 3 3 3-3"></path></svg>
                            Excel
                        </a>
                    </div>
                </div>
                <div class="card">
                    <div class="svg svg-azul">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield w-6 h-6" data-fg-bzec70="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:174:25:6041:28:e:Icon" data-fgid-bzec70=":rq7:"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path></svg>
                    </div>
                    <h3>Controle de EPIs</h3>
                    <p>Estoque, distribuição e trocas de EPIs</p>
                    <div class="buttons">
                        <a href="../Controller/gerar_controle_epis.php">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-down w-4 h-4 mr-1" data-fg-bzec77="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:185:27:6610:37:e:FileDown::::::EGMW" data-fgid-bzec77=":rpf:"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M12 18v-6"></path><path d="m9 15 3 3 3-3"></path></svg>
                            PDF
                        </a>
                        <a href="../Controller/gerar_csv_epis.php">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-down w-4 h-4 mr-1" data-fg-bzec77="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:185:27:6610:37:e:FileDown::::::EGMW" data-fgid-bzec77=":rpf:"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M12 18v-6"></path><path d="m9 15 3 3 3-3"></path></svg>
                            Excel
                        </a>
                    </div>
                </div>
                <div class="card">
                    <div class="svg svg-laranja">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text w-6 h-6" data-fg-bzec70="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:174:25:6041:28:e:Icon" data-fgid-bzec70=":rpo:"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path></svg>
                    </div>
                    <h3>PGR Completo</h3>
                    <p>Programa de Gerenciamento de Riscos atualizado</p>
                    <div class="buttons">
                        <a href="../Controller/gerar_pgr_completo.php">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-down w-4 h-4 mr-1" data-fg-bzec77="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:185:27:6610:37:e:FileDown::::::EGMW" data-fgid-bzec77=":rpf:"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M12 18v-6"></path><path d="m9 15 3 3 3-3"></path></svg>
                            PDF
                        </a>
                        <a href="../Controller/gerar_csv_pgr.php">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-down w-4 h-4 mr-1" data-fg-bzec77="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:185:27:6610:37:e:FileDown::::::EGMW" data-fgid-bzec77=":rpf:"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M12 18v-6"></path><path d="m9 15 3 3 3-3"></path></svg>
                            Excel
                        </a>
                    </div>
                </div>
                <div class="card">
                    <div class="svg svg-roxo">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text w-6 h-6" data-fg-bzec70="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:174:25:6041:28:e:Icon" data-fgid-bzec70=":rpo:"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path></svg>
                    </div>
                    <h3>Indicadores de Performance</h3>
                    <p>Taxas de frequência, gravidade e outros KPIs</p>
                    <div class="buttons">
                        <a href="../Controller/gerar_indicadores_kpis.php">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-down w-4 h-4 mr-1" data-fg-bzec77="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:185:27:6610:37:e:FileDown::::::EGMW" data-fgid-bzec77=":rpf:"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M12 18v-6"></path><path d="m9 15 3 3 3-3"></path></svg>
                            PDF
                        </a>
                        <a href="../Controller/gerar_csv_kpis.php">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-down w-4 h-4 mr-1" data-fg-bzec77="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:185:27:6610:37:e:FileDown::::::EGMW" data-fgid-bzec77=":rpf:"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M12 18v-6"></path><path d="m9 15 3 3 3-3"></path></svg>
                            Excel
                        </a>
                    </div>
                </div>
                <div class="card">
                    <div class="svg svg-cinza">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-database w-6 h-6" data-fg-bzec70="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:174:25:6041:28:e:Icon" data-fgid-bzec70=":rrk:"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M3 5V19A9 3 0 0 0 21 19V5"></path><path d="M3 12A9 3 0 0 0 21 12"></path></svg>
                    </div>
                    <h3>Compliance e Auditoria</h3>
                    <p>Evidências para auditorias e fiscalizações</p>
                    <div class="buttons">
                        <a href="../Controller/gerar_compliance_auditoria.php">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-down w-4 h-4 mr-1" data-fg-bzec77="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:185:27:6610:37:e:FileDown::::::EGMW" data-fgid-bzec77=":rpf:"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M12 18v-6"></path><path d="m9 15 3 3 3-3"></path></svg>
                            PDF
                        </a>
                        <a href="../Controller/gerar_csv_auditoria.php">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-down w-4 h-4 mr-1" data-fg-bzec77="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:185:27:6610:37:e:FileDown::::::EGMW" data-fgid-bzec77=":rpf:"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M12 18v-6"></path><path d="m9 15 3 3 3-3"></path></svg>
                            Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="proximas_auditorias">
            <div class="section-header">
                <div class="title_auditorias">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar w-6 h-6 text-blue-600" data-fg-bzec86="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:210:15:7460:46:e:Calendar::::::Bbz4" data-fgid-bzec86=":rs4:"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M3 10h18"></path></svg>
                    <h3>Próximas auditorias</h3>
                </div>
                <button class="btn-action" onclick="criarModalAuditoria()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="M12 5v14"></path></svg>
                    Criar Auditoria
                </button>
            </div>
            <p class="auditorias_p">Agenda de auditorias internas e externas</p>
            <?php foreach($auditorias as $auditoria):?>
                <?php if($auditoria['status_auditoria'] == 'Agendada'):?>
                    <div class="auditoria svg-verde auditoria_verde">
                        <div class="left">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar w-6 h-6 text-blue-600" data-fg-bzec86="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:210:15:7460:46:e:Calendar::::::Bbz4" data-fgid-bzec86=":rs4:"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M3 10h18"></path></svg>
                            <div class="text">
                                <h3><?= htmlspecialchars($auditoria['nome_auditoria'])?></h3>
                                <p><?= htmlspecialchars($auditoria['auditor_auditoria'])?></p>
                            </div>
                        </div>
                        <div class="status">
                            <p class="auditoria_p"><?= htmlspecialchars($auditoria['status_auditoria'])?></p>
                            <div class="botoes_back">
                                <button class="edit" id="<?= htmlspecialchars($auditoria['id_auditoria'])?>" data-nome="<?= htmlspecialchars($auditoria['nome_auditoria'])?>" data-auditor="<?= htmlspecialchars($auditoria['auditor_auditoria'])?>" data-date="<?= htmlspecialchars($auditoria['data_auditoria'])?>" data-status="<?= htmlspecialchars($auditoria['status_auditoria'])?>" onclick="editarModalAuditoria(this)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 20h9"></path>
                                        <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                    </svg>
                                </button>
                                <form method="POST">
                                    <button class="erase" type="submit" onclick="return confirm('Deseja mesmo deletar essa auditoria?')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 6h18"></path>
                                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                            <line x1="10" y1="11" x2="10" y2="17"></line>
                                            <line x1="14" y1="11" x2="14" y2="17"></line>
                                        </svg>
                                    </button>
                                    <input type="hidden" name="delete_aud" value="<?= htmlspecialchars($auditoria['id_auditoria'])?>">
                                </form>
                            </div>
                        </div>
                    </div>
                <?php elseif($auditoria['status_auditoria'] == 'Aguardando'):?>
                    <div class="auditoria svg-azul auditoria_azul">
                        <div class="left">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar w-6 h-6 text-blue-600" data-fg-bzec86="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:210:15:7460:46:e:Calendar::::::Bbz4" data-fgid-bzec86=":rs4:"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M3 10h18"></path></svg>
                            <div class="text">
                                <h3><?= htmlspecialchars($auditoria['nome_auditoria'])?></h3>
                                <p><?= htmlspecialchars($auditoria['auditor_auditoria'])?></p>
                            </div>
                        </div>
                        <div class="status">
                            <p class="auditoria_p"><?= htmlspecialchars($auditoria['status_auditoria'])?></p>
                            <div class="botoes_back">
                                <button class="edit" id="<?= htmlspecialchars($auditoria['id_auditoria'])?>" data-nome="<?= htmlspecialchars($auditoria['nome_auditoria'])?>" data-auditor="<?= htmlspecialchars($auditoria['auditor_auditoria'])?>" data-date="<?= htmlspecialchars($auditoria['data_auditoria'])?>" data-status="<?= htmlspecialchars($auditoria['status_auditoria'])?>" onclick="editarModalAuditoria(this)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 20h9"></path>
                                        <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                    </svg>
                                </button>
                                <form method="POST">
                                    <button type="submit" class="erase" onclick="return confirm('Deseja mesmo deletar essa auditoria?')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 6h18"></path>
                                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                            <line x1="10" y1="11" x2="10" y2="17"></line>
                                            <line x1="14" y1="11" x2="14" y2="17"></line>
                                        </svg>
                                    </button>
                                    <input type="hidden" name="delete_aud" value="<?= htmlspecialchars($auditoria['id_auditoria'])?>">
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endif;?>
            <?php endforeach;?>
            <?php if(empty($auditorias)):?>
                <h2>Não há nenhuma auditoria agendada</h2>
            <?php endif;?>
        </div>
        <div class="documentos_compliance">
            <div class="section-header">
                <h3>Documentos de compliance</h3>
                <button class="btn-action" onclick="criarModalDocumento()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="M12 5v14"></path></svg>
                    Adicionar Documento
                </button>
            </div>
            <p class="documentos_p">Documentação obrigatória para auditorias</p>
            <?php foreach($documentos as $documento):?>
                <div class="documento">
                    <div class="left">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text w-5 h-5 text-blue-600" data-fg-bzec117="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:276:21:10608:46:e:FileText::::::B1i5" data-fgid-bzec117=":rdu:"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path></svg>
                        <div class="text">
                            <a href="?download_id=<?= $documento['id_documento']?>"><?= htmlspecialchars($documento['nome_documento'])?></a>
                            <?php
                                $data = new DateTime($documento['data_documento']);
                            ?>
                            <p>Última atualização: <?= $data->format('d/m/Y')?></p>
                        </div>
                    </div>
                    <div class="status">
                        <p class="<?= strtolower($documento['status_documento'])?>"><?= ucfirst($documento['status_documento'])?></p>
                        <div class="botoes_back">
                            <button class="edit" id="<?= htmlspecialchars($documento['id_documento'])?>" data-nome="<?= htmlspecialchars($documento['nome_documento'])?>" data-date="<?= htmlspecialchars($documento['data_documento'])?>" data-status="<?= htmlspecialchars(ucfirst($documento['status_documento']))?>" onclick="editarModalDocumento(this)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 20h9"></path>
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                </svg>
                            </button>
                            <form method="POST">
                                <button type="submit" class="erase" onclick="return confirm('Deseja mesmo deletar esse documento?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 6h18"></path>
                                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                        <line x1="10" y1="11" x2="10" y2="17"></line>
                                        <line x1="14" y1="11" x2="14" y2="17"></line>
                                    </svg>
                                    <input type="hidden" name="delete_doc" value="<?= htmlspecialchars($documento['id_documento'])?>">
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach;?>
            <?php if(empty($documentos)):?>
                <h2>Não há documentos cadastrados</h2>
            <?php endif;?>
        </div>
        <!-- MODAL: Criar Auditoria -->
        <div class="modal-overlay" id="modalAuditoria">
            <div class="modal-card">
                <h3>Agendar Nova Auditoria</h3>
                <form id="formAuditoria" method="POST">
                    <div class="form-group">
                        <label for="auditoria_titulo">Tipo / Título da Auditoria</label>
                        <input type="text" id="auditoria_titulo" name="titulo" placeholder="Ex: Interna, Externa MTE, ISO 45001" required>
                    </div>
                    <div class="form-group">
                        <label for="auditoria_responsavel">Auditor / Responsável</label>
                        <input type="text" id="auditoria_responsavel" name="responsavel" placeholder="Ex: Eng. Carlos Mendes" required>
                    </div>
                    <div class="form-group">
                        <label for="auditoria_data">Data Prevista</label>
                        <input type="date" id="auditoria_data" name="data" required>
                    </div>
                    <div class="form-group">
                        <label for="auditoria_status">Status</label>
                        <select id="auditoria_status" name="status">
                            <option class="auditoria_placeholder" value="" selected disabled>Selecione o status da auditoria</option>
                            <option class="auditoria_options" value="Agendada">Agendada</option>
                            <option class="auditoria_options" value="Aguardando">Aguardando</option>
                        </select>
                    </div>
                    <div class="modal-actions">
                        <button type="button" class="btn-secondary" onclick="fecharModal('modalAuditoria')">Cancelar</button>
                        <button type="submit" class="btn-action">Salvar Auditoria</button>
                    </div>
                    <input type="hidden" name="id_auditoria" id="id_auditoria" value="">
                </form>
            </div>
        </div>

        <!-- MODAL: Adicionar Documento -->
        <div class="modal-overlay" id="modalDocumento">
            <div class="modal-card">
                <h3>Adicionar Documento de Compliance</h3>
                <form id="formDocumento" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="doc_nome">Nome do Documento</label>
                        <input type="text" id="doc_nome" name="nome" placeholder="Ex: PGR, PCMSO, Ficha de EPIs" required>
                    </div>
                    <div class="form-group">
                        <label for="doc_data">Data de Atualização</label>
                        <input type="date" id="doc_data" name="data_atualizacao" required>
                    </div>
                    <div class="form-group">
                        <label for="doc_status">Status</label>
                        <select id="doc_status" name="status_doc">
                            <option value="" selected disabled>Selecione o status do documento</option>
                            <option class="doc_options" value="Atualizado">Atualizado</option>
                            <option class="doc_options" value="Vencendo">Vencendo</option>
                            <option class="doc_options" value="Vencido">Vencido</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="doc_arquivo">Arquivo (PDF/DOC)</label>
                        <input type="file" id="doc_arquivo" name="arquivo">
                    </div>
                    <div class="modal-actions">
                        <button type="button" class="btn-secondary" onclick="fecharModal('modalDocumento')">Cancelar</button>
                        <button type="submit" class="btn-action">Salvar Documento</button>
                    </div>
                    <input type="hidden" name="id_documento" id="id_documento" value="">
                </form>
            </div>
        </div>
        <script src="../templates/assets/js/admin_compliance.js"></script>
    </body>
</html>


<!-- 
<div class="documentos_compliance">
            <div class="section-header">
                <h3>Documentos de compliance</h3>
                <button class="btn-action" onclick="abrirModal('modalDocumento')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="M12 5v14"></path></svg>
                    Adicionar Documento
                </button>
            </div>
            <p class="documentos_p">Documentação obrigatória para auditorias</p>
                        <div class="documento">
                <div class="left">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text w-5 h-5 text-blue-600" data-fg-bzec117="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:276:21:10608:46:e:FileText::::::B1i5" data-fgid-bzec117=":rdu:"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path></svg>
                    <div class="text">
                        <h3>PGR - Programa de gerenciamento de riscos</h3>
                        <p>Última atualização: 31/10/2025</p>
                    </div>
                </div>
                <p class="atualizado">Atualizado</p>
            </div>
            <div class="documento">
                <div class="left">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text w-5 h-5 text-blue-600" data-fg-bzec117="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:276:21:10608:46:e:FileText::::::B1i5" data-fgid-bzec117=":rdu:"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path></svg>
                    <div class="text">
                        <h3>PCMAT - Programa de condições e meio ambiente</h3>
                        <p>Última atualização: 14/10/2025</p>
                    </div>
                </div>
                <p class="atualizado">Atualizado</p>
            </div>
            <div class="documento">
                <div class="left">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text w-5 h-5 text-blue-600" data-fg-bzec117="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:276:21:10608:46:e:FileText::::::B1i5" data-fgid-bzec117=":rdu:"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path></svg>
                    <div class="text">
                        <h3>Ordem de serviço (OS) - NR-01</h3>
                        <p>Última atualização: 30/11/2025</p>
                    </div>
                </div>
                <p class="atualizado">Atualizado</p>
            </div>
            <div class="documento">
                <div class="left">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text w-5 h-5 text-blue-600" data-fg-bzec117="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:276:21:10608:46:e:FileText::::::B1i5" data-fgid-bzec117=":rdu:"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path></svg>
                    <div class="text">
                        <h3>APR - Análise preliminar de risco</h3>
                        <p>Última atualização: 10/12/2025</p>
                    </div>
                </div>
                <p class="atualizado">Atualizado</p>
            </div>
            <div class="documento">
                <div class="left">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text w-5 h-5 text-blue-600" data-fg-bzec117="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:276:21:10608:46:e:FileText::::::B1i5" data-fgid-bzec117=":rdu:"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path></svg>
                    <div class="text">
                        <h3>Registro de treinamentos</h3>
                        <p>Última atualização: 09/12/2025</p>
                    </div>
                </div>
                <p class="atualizado">Atualizado</p>
            </div>
            <div class="documento">
                <div class="left">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text w-5 h-5 text-blue-600" data-fg-bzec117="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:276:21:10608:46:e:FileText::::::B1i5" data-fgid-bzec117=":rdu:"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path></svg>
                    <div class="text">
                        <h3>Fichas de entrega de EPIs</h3>
                        <p>Última atualização: 10/12/2025</p>
                    </div>
                </div>
                <p class="atualizado">Atualizado</p>
            </div>
            <div class="documento">
                <div class="left">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text w-5 h-5 text-blue-600" data-fg-bzec117="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:276:21:10608:46:e:FileText::::::B1i5" data-fgid-bzec117=":rdu:"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path></svg>
                    <div class="text">
                        <h3>Laudos de insalubridade/periculosidade</h3>
                        <p>Última atualização: 29/12/2025</p>
                    </div>
                </div>
                <p class="vencendo">Vencendo</p>
            </div>
        </div> -->