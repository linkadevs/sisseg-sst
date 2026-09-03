<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Controller/IndicadoresController.php';
require_once __DIR__ . '/../Controller/IncidenteController.php';
require_once __DIR__ . '/../Controller/FuncionarioTreinamentoController.php';
require_once __DIR__ . '/../Controller/InspecaoController.php';
require_once __DIR__ . '/../Controller/FuncionarioController.php';
require_once __DIR__ . '/../Controller/TreinamentoController.php';

use Controller\IndicadoresController;
use Controller\IncidenteController;
use Controller\FuncionarioTreinamentoController;
use Controller\InspecaoController;
use Controller\FuncionarioController;
use Controller\TreinamentoController;

$indicadoresController = new IndicadoresController();
$incidenteController = new IncidenteController();
$funcionarioTreinamentoController = new FuncionarioTreinamentoController();
$inspecaoController = new InspecaoController();
$funcionarioController = new FuncionarioController();
$treinamentoController = new TreinamentoController();

$indicadores = $indicadoresController->selecionarTodosIndicadores();
$incidentes = $incidenteController->selecionarTodosOsIncidentes();
$treinamentos = $funcionarioTreinamentoController->selecionarTreinamentosRealizados();
$conformidade = $inspecaoController->selecionarDadosConformidade();
$funcionarios = $funcionarioController->selecionarTodosOsFuncionarios();

$treinamentos_realizados = count($treinamentos);
$total_incidentes = count($incidentes);


// Define o fuso horário padrão para São Paulo
$fusoSaoPaulo = new DateTimeZone('America/Sao_Paulo');

// Captura a data de hoje zerando as horas (00:00:00) para fazer a comparação correta
$hoje = new DateTime('now', $fusoSaoPaulo);
$hoje->setTime(0, 0, 0);

$treinamentos2 = $treinamentoController->listAll();
$treinamentosFuturosOuHoje = [];

foreach ($treinamentos2 as $treinamento) {
    if (!empty($treinamento['data_limite_treinamento'])) {
        // Converte a data do treinamento para DateTime no fuso de SP
        $dataTreinamento = new DateTime($treinamento['data_limite_treinamento'], $fusoSaoPaulo);
        $dataTreinamento->setTime(0, 0, 0);

        // Se a data do treinamento for maior ou igual a hoje
        if ($dataTreinamento >= $hoje) {
            $treinamentosFuturosOuHoje[] = $treinamento;
        }
    }
}


if(!empty($_SESSION['message'])) {
  echo '<script>alert("'. $_SESSION['message'] .'")</script>';
  unset($_SESSION['message']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!empty($_POST['nome_equipe_indicadores']) && isset($_POST['funcionarios'])) {
    
    // Se existir id_indicador preenchido, trata como edição
    if (!empty($_POST['id_indicador'])) {
      $indicadoresController->editarIndicador(
        $_POST['id_indicador'], 
        $_POST['nome_equipe_indicadores'], 
        $_POST['funcionarios']
      );
    } else {
      // Caso contrário, cria um novo indicador
      $indicadoresController->criarIndicador(
        $_POST['nome_equipe_indicadores'], 
        $_POST['funcionarios']
      );
    }
    
    header('Location: modulo-indicadores.php');
    exit;
  }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Indicadores de Segurança</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <link rel="stylesheet" href="../templates/assets/css/modulo-indicadores.css">
</head>
<body>

<main class="page" role="main">

  <!-- ── Back link ── -->
  <a href="principal_adm.php" class="back-link" aria-label="Voltar ao Dashboard">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
      <path d="M19 12H5M12 5l-7 7 7 7"/>
    </svg>
    Voltar ao Dashboard
  </a>

  <!-- ── Page header ── -->
  <header class="page-header">
    <div class="page-title">
      <span class="page-title-icon" aria-hidden="true">
        <!-- Stylised bar-chart icon -->
        <svg width="36" height="36" viewBox="0 0 36 36" fill="none">
          <rect x="4"  y="20" width="6" height="12" rx="2" fill="#7c3aed" opacity=".4"/>
          <rect x="14" y="12" width="6" height="20" rx="2" fill="#7c3aed" opacity=".7"/>
          <rect x="24" y="6"  width="6" height="26" rx="2" fill="#7c3aed"/>
          <polyline points="4,18 14,10 24,4" stroke="#7c3aed" stroke-width="2" stroke-linecap="round" fill="none"/>
          <circle cx="4"  cy="18" r="2" fill="#7c3aed"/>
          <circle cx="14" cy="10" r="2" fill="#7c3aed"/>
          <circle cx="24" cy="4"  r="2" fill="#7c3aed"/>
        </svg>
      </span>
      <h1>Indicadores de Segurança</h1>
    </div>
    <p class="page-subtitle">Métricas, análises e ranking de equipes</p>
  </header>

  <!-- Modal Criar Equipe -->
  <div id="modalCriarEquipe" class="modal-overlay" aria-hidden="true">
    <div class="modal-container" role="dialog" aria-labelledby="modalTitulo">
      <div class="modal-header">
        <h2 id="modalTitulo" class="modal-title">Criar equipe</h2>
        
        <div class="modal-actions">
          <!-- Input hidden para saber qual equipe editar/excluir via formulário -->
          <input type="hidden" id="id_indicador" name="id_indicador" form="formCriarEquipe" value="">

          <!-- Botão de Excluir (Submit direcionado ao script de exclusão ou via JS) -->
          <button 
            type="submit" 
            form="formCriarEquipe" 
            formaction="deletar_indicador.php" 
            class="btn-delete" 
            id="btnDeletarEquipe" 
            title="Excluir equipe" 
            onclick="return confirm('Tem certeza que deseja excluir esta equipe?');"
            style="display: none;"
          >
            🗑️
          </button>

          <button type="button" class="modal-close" onclick="fecharModal()" aria-label="Fechar modal">&times;</button>
        </div>
      </div>

      <form method="POST" class="modal-form" id="formCriarEquipe">
        <!-- Nome da Equipe -->
        <div class="form-group">
          <label for="nome_equipe" class="form-label">Nome da Equipe</label>
          <input type="text" id="nome_equipe" name="nome_equipe_indicadores" class="form-input" placeholder="Ex: Manutenção Alfa" required>
        </div>

        <!-- Seleção de Funcionários -->
        <div class="form-group">
          <label for="select_funcionario" class="form-label">Funcionários</label>
          <div class="input-with-button">
            <select id="select_funcionario" class="form-select">
              <option value="" disabled selected>Selecione um funcionário...</option>
              <?php foreach($funcionarios as $funcionario):?>
                <option value="<?= htmlspecialchars($funcionario['id_funcionario'])?>"><?= htmlspecialchars($funcionario['nome_funcionario'])?></option>
              <?php endforeach;?>
            </select>
            <button type="button" class="btn-add" id="btnAdicionarFuncionario" aria-label="Adicionar funcionário">+</button>
          </div>
        </div>

        <!-- Lista de Funcionários Selecionados -->
        <div class="selected-container">
          <span class="selected-title">Integrantes selecionados:</span>
          <ul id="listaFuncionarios" class="employee-list">
            <!-- Itens adicionados via JS aparecerão aqui -->
          </ul>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn--secondary" onclick="fecharModal()">Cancelar</button>
          <button type="submit" class="btn btn--primary">Salvar Equipe</button>
        </div>
      </form>
    </div>
  </div>

  <!-- ── KPI Cards ── -->
  <section aria-label="Indicadores principais">
    <div class="kpi-grid">

      <!-- 2. Treinamentos Concluídos -->
      <article class="kpi-card kpi-card--green">
        <p class="kpi-label">Treinamentos Concluídos</p>
        <div class="kpi-value-row">
          <span class="kpi-value"><?= htmlspecialchars($treinamentos_realizados)?></span>
        </div>
      </article>

      <!-- 3. Total de Acidentes -->
      <article class="kpi-card kpi-card--orange">
        <p class="kpi-label">Total de Incidentes</p>
        <div class="kpi-value-row">
          <span class="kpi-value"><?= htmlspecialchars($total_incidentes)?></span>
          <span class="kpi-badge">Acima da Meta</span>
        </div>
      </article>

      <!-- 4. Conformidade EPIs -->
      <article class="kpi-card kpi-card--purple">
        <p class="kpi-label">Conformidade EPIs</p>
        <div class="kpi-value-row">
          <span class="kpi-value"><?= htmlspecialchars($conformidade[0]['porcentagem_conclusao'])?>%</span>
        </div>
      </article>

    </div>
  </section>

  <!-- ── Chart 1: Taxa de Frequência ── -->
  <section aria-label="Gráfico Taxa de Frequência de Acidentes">
    <div class="chart-card">
      <p class="chart-title">Taxa de Frequência de Acidentes</p>
      <p class="chart-subtitle">Número de acidentes por milhão de horas trabalhadas</p>
      <div class="chart-container">
        <canvas id="chartFrequencia" aria-label="Gráfico de linha da taxa de frequência de acidentes"></canvas>
      </div>
    </div>
  </section>

  <!-- ── Chart 2: Treinamentos por Mês ── -->
  <section aria-label="Gráfico Treinamentos Concluídos por Mês">
    <div class="chart-card">
      <p class="chart-title">Treinamentos Concluídos por Mês</p>
      <p class="chart-subtitle">Evolução das capacitações realizadas</p>
      <div class="chart-container">
        <canvas id="chartTreinamentos" aria-label="Gráfico de barras dos treinamentos concluídos por mês"></canvas>
      </div>
    </div>
  </section>

  <!-- ── Ranking ── -->
  <section aria-label="Ranking de equipes">

    <div class="ranking-header">
      <span class="ranking-trophy" aria-hidden="true">🏆</span>
      <div class="ranking-title-block">
        <h2>Ranking de Equipes - Gamificação</h2>
        <p>Pontuação baseada em treinamentos, conformidade e dias sem acidentes</p>
      </div>
      <button class="criar_equipe">Criar equipe</button>
    </div>

    <ol class="ranking-list" style="list-style:none;">
      
      <?php 
      $maior_pontuacao = 1;
      foreach($indicadores as $indicador){
        if($indicador['pontos_indicadores'] > $maior_pontuacao) {
          $maior_pontuacao = $indicador['pontos_indicadores'];
        }
      }?>
      <?php foreach($indicadores as $index => $indicador):?>
        <li>
          <article class="rank-card" 
            data-id="<?= htmlspecialchars($indicador['id_indicadores'])?>" 
            data-nome="<?= htmlspecialchars($indicador['nome_equipe_indicadores'])?>"
            data-funcionarios="<?php if(!empty($indicador['funcionarios'])){ echo htmlspecialchars($indicador['funcionarios']); }?>"
            data-id_funcionarios="<?php if(!empty($indicador['id_funcionarios'])){ echo htmlspecialchars($indicador['id_funcionarios']); }?>">
            <div class="rank-card-top">
              <?php if($index+1 == 1):?>
                <div class="pos-badge pos-badge--gold" aria-label="1º lugar">🏆</div>
              <?php elseif($index+1 == 2):?>
                <div class="pos-badge pos-badge--silver" aria-label="2º lugar">2º</div>
              <?php elseif($index+1 == 3):?>
                <div class="pos-badge pos-badge--bronze" aria-label="3º lugar">3º</div>
              <?php else:?>
                <div class="pos-badge pos-badge--plain" aria-label="<?= $index+1?>º lugar"><?= $index+1?>º</div>
              <?php endif;?>
              <div class="rank-info">
                <p class="rank-name"><?= htmlspecialchars($indicador['nome_equipe_indicadores'])?></p>
                <div class="rank-stats">
                  <span class="rank-stat">@ <strong><?= htmlspecialchars($indicador['pontos_indicadores'])?> pts</strong></span>
                  <span class="rank-stat">Treinamentos: <strong><?= htmlspecialchars($indicador['treinamento_percentual_indicadores'])?>%</strong></span>
                  <span class="rank-stat">EPIs: <strong><?= htmlspecialchars($indicador['epi_percentual_indicadores'])?>%</strong></span>
                  <span class="rank-stat"><strong><?= htmlspecialchars($indicador['dias_sem_acidentes_indicadores'])?> dias</strong> sem acidentes</span>
                </div>
              </div>
              <?php if($index+1 == 1):?>
                <span class="rank-badge rank-badge--gold" aria-label="Campeão">🏅 Campeão</span>
              <?php elseif($index+1 == 2):?>
                <span class="rank-badge rank-badge--blue" aria-label="Vice-campeão">🥈 Vice</span>
              <?php elseif($index+1 == 3):?>
                <span class="rank-badge rank-badge--orange" aria-label="3º Lugar">🥉 3º Lugar</span>
              <?php endif;?>
            </div>
            <div class="rank-progress">
              <div class="progress-track" role="progressbar" aria-valuenow="<?= round((htmlspecialchars($indicador['pontos_indicadores'])/$maior_pontuacao)*100);?>" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-fill" style="width: <?= round((htmlspecialchars($indicador['pontos_indicadores'])/$maior_pontuacao)*100);?>%;"></div>
              </div>
            </div>
          </article>
        </li>
      <?php endforeach;?>

    </ol>

    <!-- Info card -->
    <aside class="info-card" aria-label="Regras de pontuação">
      <h3>Como funciona a pontuação?</h3>
      <ul>
        <li>Treinamentos concluídos: até 300 pontos</li>
        <li>Conformidade de EPIs: até 300 pontos</li>
        <li>Dias consecutivos sem acidentes: até 400 pontos</li>
        <li>Bônus por metas alcançadas: até 100 pontos</li>
      </ul>
    </aside>

  </section>

</main>
<?php
$sixMonthsAgo = (new DateTime('today', new DateTimeZone('America/Sao_Paulo')))->modify('-6 months');
$fiveMonthsAgo = (new DateTime('today', new DateTimeZone('America/Sao_Paulo')))->modify('-5 months');
$fourMonthsAgo = (new DateTime('today', new DateTimeZone('America/Sao_Paulo')))->modify('-4 months');
$threeMonthsAgo = (new DateTime('today', new DateTimeZone('America/Sao_Paulo')))->modify('-3 months');
$twoMonthsAgo = (new DateTime('today', new DateTimeZone('America/Sao_Paulo')))->modify('-2 months');
$oneMonthAgo = (new DateTime('today', new DateTimeZone('America/Sao_Paulo')))->modify('-1 months');
$today = (new DateTime('today', new DateTimeZone('America/Sao_Paulo')));
// $meses = [
//   $sixMonthsAgo->format('m'),
//   $fiveMonthsAgo->format('m'),
//   $fourMonthsAgo->format('m'),
//   $threeMonthsAgo->format('m'),
//   $twoMonthsAgo->format('m'),
//   $oneMonthAgo->format('m'),
//   $today->format('m')
// ];

$seisTreinamentos = 0;
$cincoTreinamentos = 0;
$quatroTreinamentos = 0;
$tresTreinamentos = 0;
$doisTreinamentos = 0;
$umTreinamento = 0;
$hojeTreinamento = 0;

$seisIncidentes = 0;
$cincoIncidentes = 0;
$quatroIncidentes = 0;
$tresIncidentes = 0;
$doisIncidentes = 0;
$umIncidente = 0;
$hojeIncidente = 0;

foreach($treinamentos as $treinamento) {
    if((new DateTime($treinamento['data_funcionario_treinamento'], new DateTimeZone('America/Sao_Paulo')))->format('m') == $sixMonthsAgo->format('m')) {
      $seisTreinamentos++;
    }
    if((new DateTime($treinamento['data_funcionario_treinamento'], new DateTimeZone('America/Sao_Paulo')))->format('m') == $fiveMonthsAgo->format('m')) {
      $cincoTreinamentos++;
    }
    if((new DateTime($treinamento['data_funcionario_treinamento'], new DateTimeZone('America/Sao_Paulo')))->format('m') == $fourMonthsAgo->format('m')) {
      $quatroTreinamentos++;
    }
    if((new DateTime($treinamento['data_funcionario_treinamento'], new DateTimeZone('America/Sao_Paulo')))->format('m') == $threeMonthsAgo->format('m')) {
      $tresTreinamentos++;
    }
    if((new DateTime($treinamento['data_funcionario_treinamento'], new DateTimeZone('America/Sao_Paulo')))->format('m') == $twoMonthsAgo->format('m')) {
      $doisTreinamentos++;
    }
    if((new DateTime($treinamento['data_funcionario_treinamento'], new DateTimeZone('America/Sao_Paulo')))->format('m') == $oneMonthAgo->format('m')) {
      $umTreinamento++;
    }
    if((new DateTime($treinamento['data_funcionario_treinamento'], new DateTimeZone('America/Sao_Paulo')))->format('m') == $today->format('m')) {
      $hojeTreinamento++;
    }
}

foreach($incidentes as $incidente) {
    if((new DateTime($incidente['data_incidente'], new DateTimeZone('America/Sao_Paulo')))->format('m') == $sixMonthsAgo->format('m')) {
      $seisIncidentes++;
    }
    if((new DateTime($incidente['data_incidente'], new DateTimeZone('America/Sao_Paulo')))->format('m') == $fiveMonthsAgo->format('m')) {
      $cincoIncidentes++;
    }
    if((new DateTime($incidente['data_incidente'], new DateTimeZone('America/Sao_Paulo')))->format('m') == $fourMonthsAgo->format('m')) {
      $quatroIncidentes++;
    }
    if((new DateTime($incidente['data_incidente'], new DateTimeZone('America/Sao_Paulo')))->format('m') == $threeMonthsAgo->format('m')) {
      $tresIncidentes++;
    }
    if((new DateTime($incidente['data_incidente'], new DateTimeZone('America/Sao_Paulo')))->format('m') == $twoMonthsAgo->format('m')) {
      $doisIncidentes++;
    }
    if((new DateTime($incidente['data_incidente'], new DateTimeZone('America/Sao_Paulo')))->format('m') == $oneMonthAgo->format('m')) {
      $umIncidente++;
    }
    if((new DateTime($incidente['data_incidente'], new DateTimeZone('America/Sao_Paulo')))->format('m') == $today->format('m')) {
      $hojeIncidente++;
    }
}

$array = [$seisTreinamentos, $cincoTreinamentos, $quatroTreinamentos, $tresTreinamentos, $doisTreinamentos, $umTreinamento, $hojeTreinamento];
$array2 = [$seisIncidentes, $cincoIncidentes, $quatroIncidentes, $tresIncidentes, $doisIncidentes, $umIncidente, $hojeIncidente];

$max_qtd = 0;
foreach($array as $qtd) {
  if($max_qtd<$qtd) {
    $max_qtd = $qtd;
  }
}

$max_qtd2 = 0;
foreach($array2 as $qtd) {
  if($max_qtd2<$qtd) {
    $max_qtd2 = $qtd;
  }
}

$formatter = new IntlDateFormatter(
    'pt_BR', 
    IntlDateFormatter::NONE, 
    IntlDateFormatter::NONE, 
    null, 
    null, 
    "MMM/yyyy"
);
?>
<script>
  const months = ['<?= ucwords(str_replace('.','',$formatter->format($sixMonthsAgo)))?>','<?= ucwords(str_replace('.','',$formatter->format($fiveMonthsAgo)))?>','<?= ucwords(str_replace('.','',$formatter->format($fourMonthsAgo)))?>','<?= ucwords(str_replace('.','',$formatter->format($threeMonthsAgo)))?>','<?= ucwords(str_replace('.','',$formatter->format($twoMonthsAgo)))?>','<?= ucwords(str_replace('.','',$formatter->format($oneMonthAgo)))?>','<?= ucwords(str_replace('.','',$formatter->format($today)))?>'];
  let dadosBarra = [<?= $seisTreinamentos?>, <?= $cincoTreinamentos?>, <?= $quatroTreinamentos?>, <?= $tresTreinamentos?>, <?= $doisTreinamentos?>, <?= $umTreinamento?>, <?= $hojeTreinamento?>];
  let dadosLinha = [<?= $seisIncidentes?>, <?= $cincoIncidentes?>, <?= $quatroIncidentes?>, <?= $tresIncidentes?>, <?= $doisIncidentes?>, <?= $umIncidente?>, <?= $hojeIncidente?>];
  let max_qtd = <?= $max_qtd?>;
  let max_qtd_linha = <?= $max_qtd2?>;
</script>
<script src="../templates/assets/js/modulo-indicadores.js"></script>

</body>
</html>