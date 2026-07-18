<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagina de Checklist - Dados da Inspeção</title>
    <link rel="stylesheet" href="../templates/assets/css/checklistpart1.css">

</head>

<body>
    <button class="setavoltarpreta" type="button" onclick="window.location.href='paginainicial.php'">
        <figure>
            <img src="../templates/assets/img/seta_esquerda.png" alt="Seta Voltar">
        </figure>
        <span>Voltar</span>
    </button>

    <main>
<div class="container_diario">
    <figure>
        <img src="../templates/assets/img/checklist.png" alt="Checklist">
    </figure>

    <h1 class="titulo_checklist">Checklist Diário - NR-18</h1>
    <p class="subtitulo">
        Inspeção diária das condições de segurança no canteiro de obras
    </p>

    <button
        class="btn_ver_checklists"
        type="button"
        onclick="window.location.href='checklistsrealizados.php'">
        Ver Checklists Realizados
    </button>
</div>


        <div class="container_dados">
            <h2>Dados da Inspeção</h2>

            <form>
                <div class="input_nome">
                    <label for="nome">Responsável pela Inspeção</label>
                    <input type="text" id="nome" placeholder="Nome do Técnico/Mestre de Obras">

                    <p id="erroNome" class="mensagem_erro">
                        Todos os campos são obrigatórios.
                    </p>
                </div>

                <div class="input_turno">
                    <label for="turno">Turno</label>
                    <figure class="selectcinzaft">
                        <img src="../templates/assets/img/selectcinza.png" alt="Select Turno">
                    </figure>

                    <select id="turno">
                        <option value="" disabled selected>Selecione o turno</option>
                        <option value="matutino">Matutino (06h - 14h)</option>
                        <option value="vespertino">Vespertino (14h - 22h)</option>
                        <option value="noturno">Noturno (22h - 06h)</option>
                    </select>

                    <p id="erroTurno" class="mensagem_erro">
                        Todos os campos são obrigatórios.
                    </p>
                </div>

            </form>

            <div class="caixinha_azul">
                <p class="checklist_inclui">O checklist inclui:</p>

                <ul>
                    <li>Organização do Canteiro</li>
                    <li>Áreas de Vivência</li>
                    <li>Condições de Trabalho</li>
                    <li>Máquinas e Equipamentos</li>
                    <li>Instalações Elétricas - NR-10</li>
                    <li>Movimentação de Materiais - NR-11</li>
                    <li>Trabalho em Altura - NR-35</li>
                    <li>Produtos Químicos</li>
                    <li>Prevenção Contra Incêndio - NR-23</li>
                    <li>Documentação</li>
                </ul>

                <p class="total_itens">Total: 40 itens de verificação</p>
            </div>

            <div class="container_importante">
                <figure>
                    <img src="../templates/assets/img/alerta.png" alt="Alerta">
                </figure>
                <span class="titulo_importante">Importante:</span>
                <p class="subtitulo_importante">Itens não conformes devem ser registrados e corrigidos
                    imediatamente.
                    Atividades com risco grave e iminente devem ser paralisadas.</p>
            </div>

            <div class="btn_iniciar" >
                <button type="button"  id="btnIniciarChecklist">
                    Iniciar Checklist
                </button>
            </div>
        </div>
    </main>

    <script src="../templates/assets/js/checklistpart1.js"></script>
</body>

</html>