<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avaliação física e mental</title>
    <link rel="stylesheet" href="../templates/assets/css/Avaliacao_fisica_e_mental.css">
</head>

<body>
    <header>
        <button onclick="window.history.back()">
            <figure>
                <img src="../templates/assets/img/seta-cinza-esquerda.png" alt="">
            </figure>
        </button>
    </header>
    <main>
        <div class="container">

            <div class="icone">
                <figure>
                    <img src="../templates/assets/img/risco-marrom.png" alt="Risco marrom">
                </figure>
            </div>

            <div class="sobreosite">
                <div class="titulo">SISSEG SST</div>
                <div class="subtitulo">Sistema direcionado para a segurança do Trabalho</div>
            </div>

            <div class="caixa_central">
                <p class="pergunta">Você está apto para trabalhar hoje?</p>
                <p class="descricao">Avalie sua condição física e mental antes de iniciar as atividades</p>
            </div>

            <div class="botoes">
                <button class="afirmativo">
                    <figure>
                        <img src="../templates/assets/img/verificacao_ branco.png" alt="">
                    </figure>
                    Sim, estou apto
                </button>


                <button class="negativo">
                    <figure>
                        <img src="../templates/assets/img/x_branco.png" alt="">
                    </figure>
                    Não, não estou apto
                </button>
            </div>

            <p class="mensagem">
                Sua segurança é nossa prioridade
            </p>
        </div>
    </main>

    <script>
        const afirmativo = document.querySelector('.afirmativo');
        const negativo = document.querySelector('.negativo');


        if (afirmativo) {
            afirmativo.addEventListener('click', () => {
                window.location.href = 'selecao_da_atividade.html';
            });
        }

        if (negativo) {
            negativo.addEventListener('click', () => {
                window.location.href = 'direito_recusa.html'
            });
        }




    </script>
</body>

</html>