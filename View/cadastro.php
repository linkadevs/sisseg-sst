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
            <figure><img src="../templates/assets/img/circuloazulcadastro1.png" alt="circulo 1"></figure>
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
                    Realize o cadastro para ser identificado !
                </p>

                <form  action="login.php" id="formCadastro" method="post">
                    <div class="input_nome">
                        <label>Nome</label>
                        <input type="text" placeholder="Digite o seu nome completo" required>
                    </div>

                    <div class="input_cpf">
                        <label>CPF</label>
                        <input type="text" placeholder="Digite o seu CPF" required>
                    </div>

                    <div class="input_senha">
                        <label>Senha</label>
                        <input type="password" placeholder="Digite a senha" required>
                    </div>

                    <div class="input_confirmar_senha">
                        <label>Confirmar Senha</label>
                        <input type="password" placeholder="Digite novamente a senha" required>
                    </div>

                    <div class="input_cargo">
                        <label>Cargo</label>
                        <input type="text" placeholder="Digite o seu cargo" required>
                    </div>

                    <div class="input_setor">
                        <label>Setor</label>
                        <input type="text" placeholder="Digite o setor correspondente" required>
                    </div>

                    <div class="btn_cadastro">
                        <button type="submit">
                            Cadastrar
                        </button>
                    </div>


                </form>

                <p class="login">
                    Já tem uma conta?
                    <a href="login.php" class="link-login">Realize o Login</a>
                </p>

            </div>

        </div>

        <div class="circuloazul2">
            <figure><img src="../templates/assets/img/circuloazulcadastro2.png" alt="circulo 2"></figure>
        </div>

    </main>

    <script src="../templates/assets/js/cadastro.js"></script>

</body>

</html>