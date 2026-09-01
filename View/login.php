<?php
session_start();

$erros = $_SESSION['erro_login'] ?? [];
$dados = $_SESSION['dados_login'] ?? [];

unset($_SESSION['erro_login'], $_SESSION['dados_login']);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Login</title>

    <link rel="stylesheet" href="../templates/assets/css/login.css">
</head>

<body>

    <div class="circuloazul1">
        <figure>
            <img src="../templates/assets/img/circuloazullogin1.png" alt="Círculo Azul">
        </figure>
    </div>

    <button class="setavoltarbranca" type="button" onclick="window.location.href='../index.html'">
        <figure>
            <img src="../templates/assets/img/setabranca_voltar.png" alt="Seta Voltar">
        </figure>
        <span>Voltar</span>
    </button>

    <main class="container">

        <div class="card-login">

            <h1>Login</h1>

            <p class="subtitulo">
                Realize o login para acessar o site!
            </p>

            <!-- Mensagens de erro -->
            <?php if (!empty($erros)): ?>

                <div class="mensagem-erro">

                    <?php foreach ($erros as $erro): ?>

                        <p class="erro-item">
                            <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?>
                        </p>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

            <form action="../Controller/ControllerLogin.php" method="post">

                <div class="input_cpf">

                    <label for="cpf">CPF</label>

                    <input type="text" id="cpf" name="cpf" placeholder="Digite o seu CPF" maxlength="14"
                        autocomplete="username" required
                        value="<?= htmlspecialchars($dados['cpf'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

                </div>

                <div class="input_senha">

                    <label for="senha">Senha</label>

                    <input type="password" id="senha" name="senha" placeholder="Digite a sua senha"
                        autocomplete="current-password" required>

                </div>

                <div class="btn_login">

                    <button type="submit">
                        Entrar
                    </button>

                </div>

                <p class="cadastro">

                    Não possui uma conta?

                    <a href="cadastro.php" class="link-cadastro">
                        Realize o cadastro
                    </a>

                </p>

            </form>

        </div>

    </main>

    <div class="circuloazul2">
        <figure>
            <img src="../templates/assets/img/circuloazullogin2.png" alt="Círculo Azul">
        </figure>
    </div>

    <script src="../templates/assets/js/login.js"></script>

</body>

</html>