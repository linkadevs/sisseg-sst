<?php
session_start();

$erros = $_SESSION['erro_cadastro'] ?? [];
$dados = $_SESSION['dados_form'] ?? [];

unset($_SESSION['erro_cadastro'], $_SESSION['dados_form']);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Cadastro</title>

    <link rel="stylesheet" href="../templates/assets/css/cadastro.css">
</head>

<body>

    <main>

        <div class="circuloazul1">
            <figure>
                <img src="../templates/assets/img/circuloazulcadastro1.png" alt="Circulo 1">
            </figure>
        </div>

        <button class="setavoltarbranca" type="button" onclick="window.location.href='paginainicial.php'">
            <figure>
                <img src="../templates/assets/img/setabranca_voltar.png" alt="Seta Voltar">
            </figure>
            <span>Voltar</span>
        </button>

        <div class="container">

            <div class="apresentacao">

                <h1>Bem-Vindo ao<br>SISSEG SST</h1>

                <p>
                    Um site voltado para Segurança do Trabalho,
                    possibilitando o registro, acompanhamento e
                    organização de dados, documentos e processos.
                </p>

            </div>

            <div class="card-cadastro">

                <h2>Cadastre-se</h2>

                <p class="subtitulo">
                    Realize o cadastro para ser identificado!
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

                <form action="../Controller/ControllerCad.php" id="formCadastro" method="post">

                    <div class="input_nome">

                        <label for="nome">Nome</label>

                        <input type="text" id="nome" name="nome" placeholder="Digite o seu nome completo"
                            autocomplete="name" required
                            value="<?= htmlspecialchars($dados['nome'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

                    </div>

                    <div class="input_cpf">

                        <label for="cpf">CPF</label>

                        <input type="text" id="cpf" name="cpf" placeholder="Digite o seu CPF" maxlength="14"
                            autocomplete="username" required
                            value="<?= htmlspecialchars($dados['cpf'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

                    </div>

                    <div class="input_senha">

                        <label for="senha">Senha</label>

                        <input type="password" id="senha" name="senha" placeholder="Digite a senha"
                            autocomplete="new-password" required>

                    </div>

                    <div class="input_confirmar_senha">

                        <label for="confirmar_senha">Confirmar Senha</label>

                        <input type="password" id="confirmar_senha" name="confirmar_senha"
                            placeholder="Digite novamente a senha" autocomplete="new-password" required>

                    </div>

                    <div class="input_turno">

                        <label for="turno">Turno</label>

                        <select id="turno" name="turno" required>

                            <option value="">Selecione o turno</option>

                            <option value="Matutino" <?= (($dados['turno'] ?? '') == 'Matutino') ? 'selected' : ''; ?>>
                                Matutino
                            </option>

                            <option value="Vespertino" <?= (($dados['turno'] ?? '') == 'Vespertino') ? 'selected' : ''; ?>>
                                Vespertino
                            </option>

                            <option value="Noturno" <?= (($dados['turno'] ?? '') == 'Noturno') ? 'selected' : ''; ?>>
                                Noturno
                            </option>

                            <option value="Integral" <?= (($dados['turno'] ?? '') == 'Integral') ? 'selected' : ''; ?>>
                                Integral
                            </option>

                        </select>

                    </div>

                    <div class="input_cargo">

                        <label for="cargo">Cargo</label>

                        <input type="text" id="cargo" name="cargo" placeholder="Digite o seu cargo"
                            autocomplete="organization-title" required
                            value="<?= htmlspecialchars($dados['cargo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

                    </div>

                    <div class="input_setor">

                        <label for="setor">Setor</label>

                        <input type="text" id="setor" name="setor" placeholder="Digite o setor correspondente"
                            autocomplete="organization" required
                            value="<?= htmlspecialchars($dados['setor'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

                    </div>

                    <div class="btn_cadastro">

                        <button type="submit">
                            Cadastrar
                        </button>

                    </div>

                </form>

                <p class="login">

                    Já tem uma conta?

                    <a href="login.php" class="link-login">
                        Realize o Login
                    </a>

                </p>

            </div>

        </div>

        <div class="circuloazul2">
            <figure>
                <img src="../templates/assets/img/circuloazulcadastro2.png" alt="Circulo 2">
            </figure>
        </div>

    </main>

    <script src="../templates/assets/js/cadastro.js"></script>

</body>

</html>