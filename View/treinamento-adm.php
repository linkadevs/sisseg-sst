<?php
session_start();

// TODO: substituir por sessão real quando o login de administrador for implementado.
if (!isset($_SESSION['id_adm'])) {
    $_SESSION['id_adm'] = 1;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Treinamentos</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../templates/assets/css/treinamento_funcionario-adm.css">
<link rel="stylesheet" href="../templates/assets/css/provas-adm.css">
<style>
  /* Painel de filtro - classes novas que ainda não existem no CSS original */
  .filter-panel{display:none;gap:12px;align-items:center;flex-wrap:wrap;background:#fff;border:1px solid #e5e5e5;border-radius:10px;padding:12px 16px;margin:-4px 0 16px 0;}
  .filter-panel.open{display:flex;}
  .filter-panel label{font-size:13px;font-weight:600;color:#444;margin-right:6px;}
  .filter-panel select{padding:6px 10px;border-radius:8px;border:1px solid #ddd;}
  .filter-panel .btn-limpar-filtro{background:none;border:none;color:#b23b3b;font-size:13px;cursor:pointer;text-decoration:underline;}
</style>
</head>
<body>

<div class="page">

  <a href="#" class="back-btn" onclick="return false;">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M19 12H5M5 12l7 7M5 12l7-7"/>
    </svg>
    Voltar
  </a>

  <div class="header-card">
    <h1>Treinamentos</h1>
    <p>Dê vida aos seus treinamentos: crie, atualize e gerencie tudo em um só lugar.</p>
    <div class="kpi-row">
      <div class="kpi-card blue">
        <div class="kpi-label">Total de Cursos</div>
        <div class="kpi-value" id="kpiTotal">0</div>
      </div>
      <div class="kpi-card green">
        <div class="kpi-label">Válidos</div>
        <div class="kpi-value" id="kpiValidos">0</div>
      </div>
      <div class="kpi-card red">
        <div class="kpi-label">Inválidos</div>
        <div class="kpi-value" id="kpiInvalidos">0</div>
      </div>
    </div>
  </div>

  <div class="search-bar">
    <div class="search-wrap">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
      </svg>
      <input type="text" class="search-input" id="searchInput" placeholder="Busque treinamentos, cursos ou conteúdos...">
    </div>
    <button class="btn-primary" id="btnCriarTreinamento">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
      <span class="btn-text">Criar treinamento</span>
    </button>
    
    <button class="btn-filter" id="btnFiltrar">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="11" y1="18" x2="13" y2="18"/>
      </svg>
      Filtrar
    </button>
  </div>

  <!-- Painel de filtro: NR + status -->
  <div class="filter-panel" id="filterPanel">
    <div>
      <label for="filtroNR">NR</label>
      <select id="filtroNR">
        <option value="">Todas</option>
        <option value="NR-06">NR-06 - EPI</option>
        <option value="NR-10">NR-10 - Eletricidade</option>
        <option value="NR-12">NR-12 - Máquinas e Equipamentos</option>
        <option value="NR-35">NR-35 - Trabalho em Altura</option>
      </select>
    </div>
    <div>
      <label for="filtroStatus">Status</label>
      <select id="filtroStatus">
        <option value="">Todos</option>
        <option value="valido">Válido</option>
        <option value="invalido">Inválido</option>
      </select>
    </div>
    <button type="button" class="btn-limpar-filtro" id="btnLimparFiltro">Limpar filtros</button>
  </div>

  <div class="courses-grid" id="coursesGrid"></div>

</div>

<!-- Modal: Criar/Editar treinamento -->
<div class="modal-overlay" id="modalOverlay">
  <div class="modal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <button class="modal-back" id="modalBackBtn">
      <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M19 12H5M5 12l7 7M5 12l7-7"/>
      </svg>
      Voltar
    </button>

    <h2 class="modal-title" id="modalTitle">Criar novo treinamento</h2>

    <div class="img-upload" id="imgUpload" title="Adicionar imagem">
      <span class="img-upload-icon">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M12 5v14M5 12h14"/>
        </svg>
      </span>
      <img class="img-upload-preview" id="imgUploadPreview" alt="Imagem do treinamento" hidden>
      <button type="button" class="img-upload-remove" id="imgUploadRemove" title="Remover imagem">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M18 6 6 18M6 6l12 12"/>
        </svg>
      </button>
      <input type="file" id="inputImagem" accept="image/png, image/jpeg, image/webp">
    </div>

    <div class="form-group">
      <label class="form-label" for="inputTitulo">Título</label>
      <input id="inputTitulo" class="form-input" type="text" placeholder="Digite o título do treinamento">
    </div>

    <div class="form-group">
      <label class="form-label" for="inputSubtitulo">Subtítulo</label>
      <input id="inputSubtitulo" class="form-input" type="text" placeholder="Digite o subtítulo do treinamento">
    </div>

    <div class="form-group">
      <label class="form-label" for="inputLinkAulas">Link das videoaulas</label>
      <input id="inputLinkAulas" class="form-input" type="url" placeholder="https://...">
    </div>

    <div class="form-row">
      <div class="form-group">
        <label class="form-label" for="inputCarga">Carga horária (horas)</label>
        <input id="inputCarga" class="form-input" type="number" min="1" placeholder="Ex: 8">
      </div>
      <div class="form-group">
        <label class="form-label" for="inputValidade">Validade</label>
        <input id="inputValidade" class="form-input" type="date">
      </div>
      <div class="toggle-group">
        <span class="toggle-label">Sem validade</span>
        <input type="checkbox" class="toggle-checkbox" id="toggleSemValidade" checked>
      </div>
    </div>

    <div class="form-group">
      <label class="form-label" for="selectNR">NRs relacionada</label>
      <div class="form-select-wrap">
        <select id="selectNR" class="form-select">
          <option value="" disabled selected>Selecione a NR do treinamento</option>
          <option value="NR-06">NR-06 - EPI</option>
          <option value="NR-10">NR-10 - Eletricidade</option>
          <option value="NR-12">NR-12 - Máquinas e Equipamentos</option>
          <option value="NR-35">NR-35 - Trabalho em Altura</option>
        </select>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="6 9 12 15 18 9"/>
        </svg>
      </div>
    </div>

    <div class="modal-actions">
      <button class="btn-delete" id="btnExcluir">Excluir</button>
      <button class="btn-save" id="btnSalvar">Salvar</button>
    </div>
  </div>
</div>

<!-- Modal: Criar/Editar prova -->
<div class="modal-overlay" id="modalProvaOverlay">
  <div class="modal modal-prova" role="dialog" aria-modal="true" aria-labelledby="modalProvaTitle">
    <button class="modal-back" id="modalProvaBackBtn">
      <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M19 12H5M5 12l7 7M5 12l7-7"/>
      </svg>
      Voltar
    </button>

    <h2 class="modal-title" id="modalProvaTitle">Criar nova prova</h2>

    <div class="form-group">
      <label class="form-label" for="provaTreinamento">Treinamento vinculado</label>
      <div class="form-select-wrap">
        <select id="provaTreinamento" class="form-select">
          <option value="" disabled selected>Selecione o treinamento</option>
        </select>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="6 9 12 15 18 9"/>
        </svg>
      </div>
    </div>

    <div class="form-group">
      <label class="form-label" for="provaTitulo">Título da prova</label>
      <input id="provaTitulo" class="form-input" type="text" placeholder="Digite o título da prova">
    </div>

    <div id="questoesContainer"></div>

    <button type="button" class="btn-add-questao" id="btnAdicionarQuestao">+ Adicionar questão</button>

    <div class="modal-actions">
      <button class="btn-delete" id="btnExcluirProva">Excluir</button>
      <button class="btn-save" id="btnSalvarProva">Salvar prova</button>
    </div>
  </div>
</div>

<script src="../templates/assets/js/treinamento-adm.js"></script>
<script src="../templates/assets/js/provas-adm.js"></script>

</body>
</html>