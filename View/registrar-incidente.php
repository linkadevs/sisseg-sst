<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Controller/AtividadeController.php';
// 1. Inclua o FuncionarioController (ou o nome equivalente da sua classe de controller)
require_once __DIR__ . '/../Controller/FuncionarioController.php';

use Controller\AtividadeController;
use Controller\FuncionarioController;

$atividadeController = new AtividadeController();
$atividades = $atividadeController->getAllAtvs();

// 2. Instancie e busque os funcionários
$funcionarioController = new FuncionarioController();
$funcionarios = $funcionarioController->selecionarTodosOsFuncionarios(); // Ajuste o nome do método se necessário

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Registrar Incidente – SISSEG SST</title>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link rel="stylesheet" href="../templates/assets/css/registrar-incidente.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
</head>
<body>

  <!-- ── TOPO: Cancelar ── -->
  <div class="top-bar">
    <button class="cancel-link" onclick="history.back()" aria-label="Cancelar e voltar">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
           stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M19 12H5"/><path d="M12 5l-7 7 7 7"/>
      </svg>
      Cancelar
    </button>
  </div>

  <!-- ── FORMULÁRIO ── -->
  <div class="form-wrapper">
    <div class="form-card">
      <h1 class="form-title">Registrar Novo Incidente</h1>
      <p class="form-subtitle">Preencha todos os campos obrigatórios</p>

      <form id="incidentForm" novalidate>

        <!-- Tipo + Data -->
        <div class="field-row">
          <div class="field-group">
            <label class="field-label" for="fieldTipo">
              Tipo <span class="required">*</span>
            </label>
            <div class="select-wrapper">
              <select id="fieldTipo" name="tipo" class="field-select" required>
                <option value="">Selecione o tipo</option>
                <option value="Quase Acidente">Quase Acidente</option>
                <option value="Condição Insegura">Condição Insegura</option>
                <option value="Acidente com Lesão">Acidente com Lesão</option>
                <option value="Risco Elétrico">Risco Elétrico</option>
                <option value="Incêndio / Explosão">Incêndio / Explosão</option>
                <option value="Outro">Outro</option>
              </select>
              <svg class="select-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none"
                   stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 9l6 6 6-6"/>
              </svg>
            </div>
            <span class="field-error-msg" id="errTipo">Campo obrigatório</span>
          </div>

          <!-- Data (tinha id="fieldLocal" duplicado no original — corrigido para fieldData) -->
          <div class="field-group">
            <label class="field-label" for="fieldData">
              Data <span class="required">*</span>
            </label>
            <input id="fieldData" name="data" type="date" class="field-input" required />
            <span class="field-error-msg" id="errData">Campo obrigatório</span>
          </div>
        </div>

        <!-- Local -->
        <div class="field-group">
          <label class="field-label" for="fieldLocal">
            Local <span class="required">*</span>
          </label>
          <input id="fieldLocal" name="local" type="text" class="field-input"
                 placeholder="Ex: Pavimento 3 - Torre A" required />
          <span class="field-error-msg" id="errLocal">Campo obrigatório</span>
        </div>

        <div class="field-group">
          <label class="field-label" for="fieldGravidade">
            Gravidade <span class="required">*</span>
          </label>
          <div class="select-wrapper">
            <select id="fieldGravidade" name="gravidade" class="field-select" required>
              <option value="">Selecione</option>
              <option value="baixa">Baixa</option>
              <option value="media">Média</option>
              <option value="alta">Alta</option>
            </select>
            <svg class="select-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M6 9l6 6 6-6"/>
            </svg>
          </div>
          <span class="field-error-msg" id="errGravidade">Campo obrigatório</span>
        </div>

        <!-- Atividade -->
        <div class="field-group">
          <label class="field-label" for="fieldAtividade">
            Atividade <span class="required">*</span>
          </label>
          <select class="field-select" name="atividade" id="fieldAtividade" required>
            <option value="placeholder" selected disabled>Selecione uma atividade</option>
            <?php foreach($atividades as $atividade):?>
              <option value="<?= htmlspecialchars($atividade['nome_atividade'])?>"><?= htmlspecialchars($atividade['nome_atividade'])?></option>
            <?php endforeach;?>
          </select>
          <span class="field-error-msg" id="errAtividade">Campo obrigatório</span>
        </div>

        <!-- Descrição -->
        <div class="field-group">
          <label class="field-label" for="fieldDescricao">
            Descrição do Incidente <span class="required">*</span>
          </label>
          <textarea id="fieldDescricao" name="descricao" class="field-textarea"
                    placeholder="Descreva detalhadamente o que ocorreu..." required></textarea>
          <span class="field-error-msg" id="errDescricao">Campo obrigatório</span>
        </div>

        <!-- Testemunhas (opcional) -->
        <div class="field-group">
          <label class="field-label" for="fieldTestemunhas">Testemunhas</label>
          <input id="fieldTestemunhas" name="testemunhas" type="text" class="field-input"
                 placeholder="Nomes das testemunhas" />
        </div>

        <!-- Vítimas / Funcionários Envolvidos -->
        <div class="field-group">
          <label class="field-label">Vítimas / Funcionários Envolvidos</label>
          
          <div style="display: flex; gap: 8px; margin-bottom: 8px;">
            <div class="select-wrapper" style="flex: 1;">
              <select id="selectVitima" class="field-select">
                <option value="">Selecione um funcionário...</option>
                <?php foreach($funcionarios as $func): ?>
                  <option value="<?= htmlspecialchars($func['id_funcionario']) ?>">
                    <?= htmlspecialchars($func['nome_funcionario']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <svg class="select-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 9l6 6 6-6"/>
              </svg>
            </div>
            
            <button type="button" id="btnAddVitima" style="padding: 0 18px; background: #2563eb; color: #fff; border: none; border-radius: 8px; font-weight: bold; font-size: 18px; cursor: pointer;">+</button>
          </div>

          <!-- Container onde as tags com 'x' serão adicionadas dinamicamente -->
          <div id="vitimasContainer" style="display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px;"></div>
          
          <!-- Container invisível onde os <input type="hidden" name="vitimas[]"> serão injetados para enviar via FormData -->
          <div id="vitimasInputsHidden"></div>
        </div>

        <!-- Ação Imediata -->
        <div class="field-group">
          <label class="field-label" for="fieldAcao">
            Ação Imediata Tomada <span class="required">*</span>
          </label>
          <textarea id="fieldAcao" name="acaoImediata" class="field-textarea"
                    placeholder="Descreva as ações imediatas realizadas..." required></textarea>
          <span class="field-error-msg" id="errAcao">Campo obrigatório</span>
        </div>

        <!-- Treinamento de Reciclagem -->
        <div class="field-group">
          <label class="field-label" for="fieldTreinamento">Treinamento de Reciclagem Necessário</label>
          <input id="fieldTreinamento" name="treinamento" type="text" class="field-input"
                 placeholder="Ex: NR-35, NR-10, NR-06 (se aplicável)" />
          <p class="field-hint">Indique se o incidente requer reciclagem de treinamento específico</p>
        </div>

        <!-- Upload de fotos -->
        <div class="field-group">
          <label class="field-label">Fotos do Local</label>
          <div class="upload-area" id="uploadArea">
            <svg class="upload-icon" width="38" height="38" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
              <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
              <circle cx="12" cy="13" r="4"/>
            </svg>
            <p class="upload-label-text">Adicionar fotos do local (opcional)</p>
            <button type="button" class="btn-upload" onclick="document.getElementById('fieldFotos').click()">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                   stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="16 16 12 12 8 16"/>
                <line x1="12" y1="12" x2="12" y2="21"/>
                <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/>
              </svg>
              Fazer Upload
            </button>
            <!-- name="fotos[]" (array): o backend hoje guarda só a 1ª imagem (fotos_incidente é 1 mediumblob) -->
            <input type="file" id="fieldFotos" name="fotos[]" accept="image/*" multiple style="display:none" />
          </div>
          <p class="field-hint">Apenas a primeira imagem selecionada é salva no momento.</p>
          <div class="upload-preview" id="uploadPreview"></div>
        </div>

        <!-- Ações – desktop -->
        <div class="form-actions">
          <button type="submit" class="btn-submit">Registrar Incidente</button>
          <button type="button" class="btn-cancel-form" onclick="history.back()">Cancelar</button>
        </div>

      </form>
    </div>
  </div>

  <!-- Rodapé fixo – mobile -->
  <div class="form-actions-fixed" id="fixedActions">
    <button type="submit" form="incidentForm" class="btn-submit">Registrar Incidente</button>
    <button type="button" class="btn-cancel-form" onclick="history.back()">Cancelar</button>
  </div>

  <!-- Toast -->
  <div class="toast" id="toast">✓ Incidente registrado com sucesso!</div>

<script src="../templates/assets/js/registrar-incidente.js"></script>

</body>
</html>