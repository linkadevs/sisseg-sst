<?php

session_start();

$_SESSION['id_funcionario'] = 6;

require_once __DIR__ . '/../Controller/FuncionarioController.php';
require_once __DIR__ . '/../vendor/autoload.php';

$FuncionarioController = new \Controller\FuncionarioController();

// IMPORTANTE: usando id_funcionario para ficar consistente com o UPDATE abaixo.
// Se a intenção era realmente carregar pelo id_administrador, me avise.
$funcionario = $FuncionarioController->selecionarFuncionarioByID($_SESSION['id_funcionario']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = $_SESSION['id_funcionario'];
    if (
        !empty($_POST['nome']) ||
        !empty($_POST['cpf']) ||
        !empty($_POST['cargo']) ||
        !empty($_POST['setor'])
    ) {
        $cpf = preg_replace('/\D/', '', $_POST['cpf']);
        $FuncionarioController->updateFuncionario(
            $id,
            $_POST['nome'],
            $cpf,
            $_POST['setor'],
            $_POST['cargo']
        );
    }

    if (
        !empty($_POST['senha_funcionario'])
    ) {
        $_SESSION['error_message'] = '';
        $FuncionarioController->updatePassword(
            $id,
            $_POST['senha_funcionario']
        );
    }

    header('Location: perfil-funcionario.php');
    exit;
}

if(isset($_SESSION['success_message'])) {
    echo '<script>alert("Perfil/senha alterado com sucesso")</script>';
    $_SESSION['success_message'] = null;
}

if(isset($_GET['error_message'])) {
    echo '<script>
        alert("'.$_GET['error_message'].'")
        window.location.href = "perfil-funcionario.php"
    </script>';
    exit;
}

$nome  = htmlspecialchars($funcionario['nome_funcionario'] ?? '', ENT_QUOTES, 'UTF-8');
$cpf   = htmlspecialchars($funcionario['cpf_funcionario'] ?? '', ENT_QUOTES, 'UTF-8');
$cargo = htmlspecialchars($funcionario['cargo_funcionario'] ?? '', ENT_QUOTES, 'UTF-8');
$setor = htmlspecialchars($funcionario['setor_funcionario'] ?? '', ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Perfil</title>
<link rel="stylesheet" href="../templates/assets/css/perfil.css">
</head>
<body>

  <div class="bg-shape bg-shape--top" aria-hidden="true"></div>
  <div class="bg-shape bg-shape--bottom" aria-hidden="true"></div>

  <main class="page">
    <section class="card" aria-labelledby="card-title">

      <a href="#" class="back-link" aria-label="Voltar para a página anterior">
        <svg class="back-link__icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M19 12H5"></path>
          <path d="M12 19l-7-7 7-7"></path>
        </svg>
        <span>Voltar</span>
      </a>

      <header class="card__header">
        <h1 id="card-title" class="card__title">Suas informações Pessoais</h1>
        <p class="card__subtitle">Visualize ou Edite seus dados</p>
      </header>

      <form class="profile-form" id="profileForm" method="POST" novalidate>

        <div class="field">
          <label for="nome" class="field__label">Nome</label>
          <div class="field__control">
            <input type="text" id="nome" name="nome" class="field__input" value="<?php echo $nome; ?>" readonly>
            <button type="button" class="field__icon-btn" data-edit-target="nome" aria-label="Editar nome">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
              </svg>
            </button>
          </div>
        </div>

        <div class="field">
          <label for="cpf" class="field__label">CPF</label>
          <div class="field__control">
            <input type="text" id="cpf" name="cpf" class="field__input" value="<?php echo $cpf; ?>" inputmode="numeric" readonly>
            <button type="button" class="field__icon-btn" data-edit-target="cpf" aria-label="Editar CPF">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
              </svg>
            </button>
          </div>
        </div>

        <div class="field">
          <label for="senha" class="field__label">Senha</label>
          <div class="field__control">
            <!-- Por segurança, NÃO exibimos a senha real (nem hash). O campo fica vazio;
                 o usuário digita uma nova senha só se quiser alterá-la. -->
            <input type="password" id="senha" name="senha_funcionario" class="field__input" value="" placeholder="••••••••" readonly>
            <button type="button" class="field__icon-btn" id="toggleSenha" aria-label="Mostrar senha" aria-pressed="false">
              <svg id="eyeIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a19.42 19.42 0 0 1 5.06-6.06M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a19.5 19.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                <path d="M1 1l22 22"></path>
              </svg>
            </button>
            <button type="button" class="field__icon-btn field__icon-btn--edit" data-edit-target="senha" aria-label="Editar senha">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
              </svg>
            </button>
          </div>
        </div>

        <div class="field">
          <label for="cargo" class="field__label">Cargo</label>
          <div class="field__control">
            <input type="text" id="cargo" name="cargo" class="field__input" value="<?php echo $cargo; ?>" readonly>
            <button type="button" class="field__icon-btn" data-edit-target="cargo" aria-label="Editar cargo">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
              </svg>
            </button>
          </div>
        </div>

        <div class="field">
          <label for="setor" class="field__label">Setor</label>
          <div class="field__control">
            <input type="text" id="setor" name="setor" class="field__input" value="<?php echo $setor; ?>" readonly>
            <button type="button" class="field__icon-btn" data-edit-target="setor" aria-label="Editar setor">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
              </svg>
            </button>
          </div>
        </div>

        <p class="status-msg" id="statusMsg" role="status" aria-live="polite"></p>

        <div class="actions">
          <button type="submit" class="btn btn--save" id="saveBtn">Salvar Alterações</button>
          <button type="button" class="btn btn--logout" id="logoutBtn">Fazer Logout</button>
        </div>

      </form>
    </section>
  </main>

<script src="../templates/assets/js/perfil.js"></script>
</body>
</html>
