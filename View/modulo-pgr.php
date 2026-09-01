<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Controller/AtividadeController.php';

use Controller\AtividadeController;

$atividadeController = new AtividadeController();

$atividades = $atividadeController->getAllAtvs();

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PGR - Gerador de Fichas de Risco</title>
<link rel="stylesheet" href="../templates/assets/css/modulo-pgr.css">
</head>
<body>

  <!-- ================= MODAL: FORMULÁRIO (CRIAÇÃO) ================= -->
  <div class="modal-overlay" id="modalOverlay">
    <div class="modal modal-large" id="modalBox" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
      <div class="modal-header">
        <h2 id="modalTitle">Nova Ficha de Risco</h2>
        <button type="button" class="modal-close" id="modalClose" aria-label="Fechar formulário">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <div class="modal-body">
        <form id="riskForm" novalidate>

          <div class="form-section">
            <h2 class="form-section-title">Dados da Atividade</h2>
            <div class="field">
              <select name="activityName" id="activityName" required>
                <option value="placeholder" selected disabled>Selecione uma atividade</option>
                <?php foreach($atividades as $atividade):?>
                  <option value="<?= htmlspecialchars($atividade['nome_atividade'])?>"><?= htmlspecialchars($atividade['nome_atividade'])?></option>
                <?php endforeach;?>
              </select>
            </div>
            <div class="field">
              <label for="activityNr">Norma Regulamentadora (NR)</label>
              <select id="activityNr" name="activityNr" required>
                <option value="">Selecione a NR...</option>
              </select>
            </div>
          </div>

          <div class="form-section">
            <h2 class="form-section-title">Riscos Identificados</h2>
            <div id="createRisksContainer"><!-- Blocos de risco renderizados dinamicamente via JS --></div>
            <button type="button" class="btn-add-risk" id="btnAddCreateRisk">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
              </svg>
              Adicionar Risco
            </button>
          </div>

          <div class="form-section">
            <h2 class="form-section-title">Medidas Coletivas e Procedimentos</h2>

            <div class="field">
              <label>Medidas de Proteção Coletiva</label>
              <div class="add-item-row">
                <input type="text" id="collectiveInput" placeholder="Ex: Guarda-corpo rígido nos perímetros">
                <button type="button" class="btn-add" id="btnAddCollective">+</button>
              </div>
              <ul class="chip-list" id="collectiveList"></ul>
            </div>

            <div class="field">
              <label>Procedimentos Obrigatórios</label>
              <div class="add-item-row">
                <input type="text" id="procedureInput" placeholder="Ex: Análise Preliminar de Risco (APR) diária">
                <button type="button" class="btn-add" id="btnAddProcedure">+</button>
              </div>
              <ul class="chip-list" id="proceduresList"></ul>
            </div>
          </div>

          <button type="submit" class="btn-submit">Gerar e Adicionar Ficha de Risco</button>
        </form>
      </div>
    </div>
  </div>

  <!-- ================= MODAL: EDIÇÃO DE FICHA ================= -->
  <div class="modal-overlay" id="editModalOverlay">
    <div class="modal modal-large" id="editModalBox" role="dialog" aria-modal="true" aria-labelledby="editModalTitle">
      <div class="modal-header">
        <h2 id="editModalTitle">Editar Ficha de Risco</h2>
        <button type="button" class="modal-close" id="editModalClose" aria-label="Fechar edição">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <div class="modal-body" id="editModalBody">
        <!-- Conteúdo renderizado dinamicamente via JS -->
      </div>

      <div class="modal-footer">
        <button type="button" class="btn-cancel btn-delete-activity" id="editModalDelete">Excluir Ficha</button>
        <button type="button" class="btn-cancel" id="editModalCancel">Cancelar</button>
        <button type="button" class="btn-submit" id="editModalSave">Salvar Alterações</button>
      </div>
    </div>
  </div>

  <div class="app-shell">
    <!-- ================= MAIN: DASHBOARD / DETALHAMENTO ================= -->
    <main class="main-content">

      <!-- ---------- VIEW: DASHBOARD ---------- -->
      <section class="view" id="dashboardView">
        <div class="page-header">
          <div class="page-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="3" width="18" height="18" rx="3"></rect>
              <line x1="9" y1="8" x2="15" y2="8"></line>
              <line x1="9" y1="12" x2="15" y2="12"></line>
              <line x1="9" y1="16" x2="13" y2="16"></line>
            </svg>
          </div>
          <div class="page-header-text">
            <h1 class="page-title">PGR - Programa de Gerenciamento de Riscos</h1>
            <p class="page-subtitle">Fichas de análise de risco por atividade conforme NR-01</p>
          </div>
          <button type="button" class="btn-open-modal" id="btnOpenModal">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
              <line x1="12" y1="5" x2="12" y2="19"></line>
              <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Nova Ficha de Risco
          </button>
        </div>

        <div class="about-card">
          <h2>Sobre o PGR</h2>
          <p>O Programa de Gerenciamento de Riscos (PGR) identifica os perigos e avalia os riscos ocupacionais, implementando medidas de prevenção e proteção para cada atividade.</p>
          <div class="risk-type-grid">
            <div class="risk-type-tag tag-physical">
              <span class="tag-label">Físicos</span>
              <span class="tag-desc">Ruído, Vibração</span>
            </div>
            <div class="risk-type-tag tag-chemical">
              <span class="tag-label">Químicos</span>
              <span class="tag-desc">Poeiras, Gases</span>
            </div>
            <div class="risk-type-tag tag-ergonomic">
              <span class="tag-label">Ergonômicos</span>
              <span class="tag-desc">Postura, Esforço</span>
            </div>
            <div class="risk-type-tag tag-accident">
              <span class="tag-label">Acidentes</span>
              <span class="tag-desc">Quedas, Choques</span>
            </div>
          </div>
        </div>

        <h2 class="section-heading">Fichas por Atividade</h2>
        <div class="cards-grid" id="cardsGrid">
          <!-- Cards das atividades são renderizados dinamicamente via JS -->
          <p class="empty-state" id="emptyState">Nenhuma ficha cadastrada ainda. Clique em "Nova Ficha de Risco" para gerar a primeira ficha.</p>
        </div>

        <div class="legend-card">
          <h3>Níveis de Risco</h3>
          <div class="legend-row">
            <div class="legend-item"><span class="legend-dot dot-baixo"></span>Baixo</div>
            <div class="legend-item"><span class="legend-dot dot-medio"></span>Médio</div>
            <div class="legend-item"><span class="legend-dot dot-alto"></span>Alto</div>
            <div class="legend-item"><span class="legend-dot dot-critico"></span>Crítico</div>
          </div>
        </div>
      </section>

      <!-- ---------- VIEW: DETALHAMENTO ---------- -->
      <section class="view hidden" id="detailView">
        <button class="btn-back" id="btnVoltarDashboard" type="button">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
          </svg>
          Voltar ao Dashboard
        </button>

        <h1 class="detail-title" id="detailActivityName">—</h1>
        <p class="detail-subtitle">Programa de Gerenciamento de Riscos</p>

        <div class="detail-card">
          <div class="detail-card-header">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f97316" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
              <line x1="12" y1="9" x2="12" y2="13"></line>
              <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
            <div>
              <h2>Riscos Identificados</h2>
              <p>Análise detalhada dos riscos para esta atividade</p>
            </div>
          </div>
          <div id="risksList"><!-- Blocos de risco renderizados via JS --></div>
        </div>

        <div class="detail-card">
          <div class="detail-card-header">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
              <polyline points="9 12 11 14 15 10"></polyline>
            </svg>
            <h2>Medidas de Proteção Coletiva</h2>
          </div>
          <div class="green-list" id="collectiveMeasuresOutput"></div>
        </div>

        <div class="detail-card">
          <div class="detail-card-header">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="8" y="2" width="8" height="4" rx="1"></rect>
              <path d="M9 4H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-4"></path>
              <polyline points="9 14 11 16 15 12"></polyline>
            </svg>
            <h2>Procedimentos Obrigatórios</h2>
          </div>
          <div class="blue-list" id="proceduresOutput"></div>
        </div>
      </section>

    </main>
  </div>

<script>
  let atividades = ''
  <?php foreach($atividades as $atividade):?>
    atividades += '<option value="<?= htmlspecialchars($atividade['nome_atividade'])?>"><?= htmlspecialchars($atividade['nome_atividade'])?></option>'
  <?php endforeach;?>
</script>

<script src="../templates/assets/js/modulo-pgr.js"></script>
</body>
</html>