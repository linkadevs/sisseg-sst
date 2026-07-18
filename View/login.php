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
        <figure><img src="../templates/assets/img/circuloazullogin1.png" alt="Seta Voltar"></figure>
    </div>

    <button class="setavoltarbranca" type="button" onclick="window.location.href='paginainicial.php'">
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


            <form action="paginainicial.php" method="post">
                <div class="input_cpf">
                    <label for="cpf">CPF</label>
                    <input type="text" id="cpf" placeholder="Digite o seu CPF" required>
                </div>

                <div class="input_senha">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" placeholder="Digite a sua senha" required>
                </div>

                <div class="btn_login">
                    <button submit="button">
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
        <figure><img src="../templates/assets/img/circuloazullogin2.png" alt="Seta Voltar"></figure>
    </div>

    <script src="../templates/assets/js/login.js"></script>
</body>

</html>