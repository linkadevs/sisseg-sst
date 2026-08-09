<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Controller/FuncionarioController.php';
require_once __DIR__ . '/../Controller/IncidenteController.php';
require_once __DIR__ . '/../Controller/FuncionarioTreinamentoController.php';

use Controller\FuncionarioController;
use Controller\IncidenteController;
use Controller\FuncionarioTreinamentoController;

$funcionario_controller = new FuncionarioController();
$incidente_controller = new IncidenteController();
$funcionario_treinamento_controller = new FuncionarioTreinamentoController();

$funcionarios = $funcionario_controller->selecionarTodosOsFuncionarios();
$incidentes = $incidente_controller->selecionarTodosOsIncidentes();
$funcionarios_treinados = $funcionario_treinamento_controller->selecionarFuncionariosTreinados();

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
        <button class="voltar">
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
                <h2>842</h2>
            </div>
            <div class="mini_card">
                <div class="mini_card_title">
                    <svg class="svg3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users w-5 h-5 text-blue-600" data-fg-bzec17="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:112:17:3257:43:e:Users::::::DV8M" data-fgid-bzec17=":rno:"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <p>Incidentes (mês)</p>
                </div>
                <h2><?= $incidentes_mes;?></h2>
                <p class="green_p">Baixo</p>
            </div>
            <div class="mini_card">
                <div class="mini_card_title">
                    <svg class="svg4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users w-5 h-5 text-blue-600" data-fg-bzec17="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:112:17:3257:43:e:Users::::::DV8M" data-fgid-bzec17=":rno:"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <p>Conformidade</p>
                </div>
                <h2>96%</h2>
                <p class="green_p">Excelente</p>
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
                        <button>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-down w-4 h-4 mr-1" data-fg-bzec77="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:185:27:6610:37:e:FileDown::::::EGMW" data-fgid-bzec77=":rpf:"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M12 18v-6"></path><path d="m9 15 3 3 3-3"></path></svg>
                            PDF
                        </button>
                        <button>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-down w-4 h-4 mr-1" data-fg-bzec77="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:185:27:6610:37:e:FileDown::::::EGMW" data-fgid-bzec77=":rpf:"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M12 18v-6"></path><path d="m9 15 3 3 3-3"></path></svg>
                            Excel
                        </button>
                    </div>
                </div>
                <div class="card">
                    <div class="svg svg-vermelho">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text w-6 h-6" data-fg-bzec70="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:174:25:6041:28:e:Icon" data-fgid-bzec70=":rpo:"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path></svg>
                    </div>
                    <h3>Relatório de Acidentes</h3>
                    <p>Histórico de acidentes e análise de causas</p>
                    <div class="buttons">
                        <button>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-down w-4 h-4 mr-1" data-fg-bzec77="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:185:27:6610:37:e:FileDown::::::EGMW" data-fgid-bzec77=":rpf:"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M12 18v-6"></path><path d="m9 15 3 3 3-3"></path></svg>
                            PDF
                        </button>
                        <button>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-down w-4 h-4 mr-1" data-fg-bzec77="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:185:27:6610:37:e:FileDown::::::EGMW" data-fgid-bzec77=":rpf:"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M12 18v-6"></path><path d="m9 15 3 3 3-3"></path></svg>
                            Excel
                        </button>
                    </div>
                </div>
                <div class="card">
                    <div class="svg svg-azul">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield w-6 h-6" data-fg-bzec70="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:174:25:6041:28:e:Icon" data-fgid-bzec70=":rq7:"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path></svg>
                    </div>
                    <h3>Controle de EPIs</h3>
                    <p>Estoque, distribuição e trocas de EPIs</p>
                    <div class="buttons">
                        <button>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-down w-4 h-4 mr-1" data-fg-bzec77="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:185:27:6610:37:e:FileDown::::::EGMW" data-fgid-bzec77=":rpf:"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M12 18v-6"></path><path d="m9 15 3 3 3-3"></path></svg>
                            PDF
                        </button>
                        <button>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-down w-4 h-4 mr-1" data-fg-bzec77="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:185:27:6610:37:e:FileDown::::::EGMW" data-fgid-bzec77=":rpf:"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M12 18v-6"></path><path d="m9 15 3 3 3-3"></path></svg>
                            Excel
                        </button>
                    </div>
                </div>
                <div class="card">
                    <div class="svg svg-laranja">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text w-6 h-6" data-fg-bzec70="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:174:25:6041:28:e:Icon" data-fgid-bzec70=":rpo:"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path></svg>
                    </div>
                    <h3>PGR Completo</h3>
                    <p>Programa de Gerenciamento de Riscos atualizado</p>
                    <div class="buttons">
                        <button>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-down w-4 h-4 mr-1" data-fg-bzec77="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:185:27:6610:37:e:FileDown::::::EGMW" data-fgid-bzec77=":rpf:"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M12 18v-6"></path><path d="m9 15 3 3 3-3"></path></svg>
                            PDF
                        </button>
                        <button>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-down w-4 h-4 mr-1" data-fg-bzec77="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:185:27:6610:37:e:FileDown::::::EGMW" data-fgid-bzec77=":rpf:"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M12 18v-6"></path><path d="m9 15 3 3 3-3"></path></svg>
                            Excel
                        </button>
                    </div>
                </div>
                <div class="card">
                    <div class="svg svg-roxo">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text w-6 h-6" data-fg-bzec70="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:174:25:6041:28:e:Icon" data-fgid-bzec70=":rpo:"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path></svg>
                    </div>
                    <h3>Indicadores de Performance</h3>
                    <p>Taxas de frequência, gravidade e outros KPIs</p>
                    <div class="buttons">
                        <button>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-down w-4 h-4 mr-1" data-fg-bzec77="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:185:27:6610:37:e:FileDown::::::EGMW" data-fgid-bzec77=":rpf:"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M12 18v-6"></path><path d="m9 15 3 3 3-3"></path></svg>
                            PDF
                        </button>
                        <button>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-down w-4 h-4 mr-1" data-fg-bzec77="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:185:27:6610:37:e:FileDown::::::EGMW" data-fgid-bzec77=":rpf:"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M12 18v-6"></path><path d="m9 15 3 3 3-3"></path></svg>
                            Excel
                        </button>
                    </div>
                </div>
                <div class="card">
                    <div class="svg svg-cinza">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-database w-6 h-6" data-fg-bzec70="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:174:25:6041:28:e:Icon" data-fgid-bzec70=":rrk:"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M3 5V19A9 3 0 0 0 21 19V5"></path><path d="M3 12A9 3 0 0 0 21 12"></path></svg>
                    </div>
                    <h3>Compliance e Auditoria</h3>
                    <p>Evidências para auditorias e fiscalizações</p>
                    <div class="buttons">
                        <button>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-down w-4 h-4 mr-1" data-fg-bzec77="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:185:27:6610:37:e:FileDown::::::EGMW" data-fgid-bzec77=":rpf:"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M12 18v-6"></path><path d="m9 15 3 3 3-3"></path></svg>
                            PDF
                        </button>
                        <button>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-down w-4 h-4 mr-1" data-fg-bzec77="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:185:27:6610:37:e:FileDown::::::EGMW" data-fgid-bzec77=":rpf:"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M12 18v-6"></path><path d="m9 15 3 3 3-3"></path></svg>
                            Excel
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="proximas_auditorias">
            <div class="title_auditorias">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar w-6 h-6 text-blue-600" data-fg-bzec86="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:210:15:7460:46:e:Calendar::::::Bbz4" data-fgid-bzec86=":rs4:"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M3 10h18"></path></svg>
                <h3>Próximas auditorias</h3>
            </div>
            <p class="auditorias_p">Agenda de auditorias internas e externas</p>
            <div class="auditoria svg-verde auditoria_verde">
                <div class="left">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar w-6 h-6 text-blue-600" data-fg-bzec86="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:210:15:7460:46:e:Calendar::::::Bbz4" data-fgid-bzec86=":rs4:"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M3 10h18"></path></svg>
                    <div class="text">
                        <h3>Interna</h3>
                        <p>19/12/2025 - Eng. Carlos Mendes</p>
                    </div>
                </div>
                <p class="auditoria_p">Agendada</p>
            </div>
            <div class="auditoria svg-azul auditoria_azul">
                <div class="left">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar w-6 h-6 text-blue-600" data-fg-bzec86="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:210:15:7460:46:e:Calendar::::::Bbz4" data-fgid-bzec86=":rs4:"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M3 10h18"></path></svg>
                    <div class="text">
                        <h3>Externa - Ministério do Trabalho</h3>
                        <p>14/01/2026 - Fiscal do MTE</p>
                    </div>
                </div>
                <p class="auditoria_p">Aguardando</p>
            </div>
            <div class="auditoria svg-azul auditoria_azul">
                <div class="left">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar w-6 h-6 text-blue-600" data-fg-bzec86="13.43:13.11473:/src/app/components/screens/AdminScreen.tsx:210:15:7460:46:e:Calendar::::::Bbz4" data-fgid-bzec86=":rs4:"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M3 10h18"></path></svg>
                    <div class="text">
                        <h3>Certificação ISO 45001</h3>
                        <p>09/02/2026 - Bureau Veritas</p>
                    </div>
                </div>
                <p class="auditoria_p">Aguardando</p>
            </div>
        </div>
        <div class="documentos_compliance">
            <h3>Documentos de compliance</h3>
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
        </div>
    </body>
</html>