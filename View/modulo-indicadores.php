<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Controller/IndicadoresController.php';

use Controller\IndicadoresController;

$indicadoresController = new IndicadoresController();

$indicadores = $indicadoresController->selecionarTodosIndicadores();

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

  <!-- ── KPI Cards ── -->
  <section aria-label="Indicadores principais">
    <div class="kpi-grid">

      <!-- 1. Taxa de Frequência Média -->
      <article class="kpi-card kpi-card--blue">
        <p class="kpi-label">Taxa de Frequência Média</p>
        <div class="kpi-value-row">
          <span class="kpi-value">10.1</span>
          <span class="kpi-arrow" aria-label="Acima da meta">📈</span>
        </div>
        <p class="kpi-meta">Meta: 10</p>
        <div class="progress-wrap" role="progressbar" aria-valuenow="101" aria-valuemin="0" aria-valuemax="100">
          <div class="progress-track">
            <div class="progress-fill" style="width: 101%; background: var(--color-blue);"></div>
          </div>
        </div>
      </article>

      <!-- 2. Treinamentos Concluídos -->
      <article class="kpi-card kpi-card--green">
        <p class="kpi-label">Treinamentos Concluídos</p>
        <div class="kpi-value-row">
          <span class="kpi-value">378</span>
        </div>
        <p class="kpi-meta">Meta anual: 600</p>
        <div class="progress-wrap" role="progressbar" aria-valuenow="63" aria-valuemin="0" aria-valuemax="100">
          <div class="progress-track">
            <div class="progress-fill" style="width: 63%; background: var(--color-green);"></div>
          </div>
        </div>
      </article>

      <!-- 3. Total de Acidentes -->
      <article class="kpi-card kpi-card--orange">
        <p class="kpi-label">Total de Acidentes</p>
        <div class="kpi-value-row">
          <span class="kpi-value">9</span>
          <span class="kpi-badge">Acima da Meta</span>
        </div>
        <p class="kpi-meta">Meta: Reduzir 20% ao ano</p>
        <div class="progress-wrap" role="progressbar" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100">
          <div class="progress-track">
            <div class="progress-fill" style="width: 90%; background: var(--color-orange);"></div>
          </div>
        </div>
      </article>

      <!-- 4. Conformidade EPIs -->
      <article class="kpi-card kpi-card--purple">
        <p class="kpi-label">Conformidade EPIs</p>
        <div class="kpi-value-row">
          <span class="kpi-value">96%</span>
        </div>
        <p class="kpi-meta">Meta: 95%</p>
        <div class="progress-wrap" role="progressbar" aria-valuenow="96" aria-valuemin="0" aria-valuemax="100">
          <div class="progress-track">
            <div class="progress-fill" style="width: 96%; background: var(--color-purple);"></div>
          </div>
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
    </div>

    <ol class="ranking-list" style="list-style:none;">
      
      <?php 
      $maior_pontuacao = 0;
      foreach($indicadores as $indicador){
        if($indicador['pontos_indicadores'] > $maior_pontuacao) {
          $maior_pontuacao = $indicador['pontos_indicadores'];
        }
      }?>
      <?php foreach($indicadores as $index => $indicador):?>
        <li>
          <article class="rank-card">
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

<script src="../templates/assets/js/modulo-indicadores.js"></script>

</body>
</html>