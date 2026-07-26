<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Checklists NR-18</title>
        <link rel="stylesheet" href="../templates/assets/css/visualizacao_checklists.css">
    </head>
    <body>
        <div class="sombra"></div>
        <form>
            <figure><img src="../templates/assets/img/seta_esquerda.png" alt=""></figure>
            <h2>Criar nova atividade</h2>
            <div class="inputs">
                <div class="input">
                    <label for="nome_atividade">Nome</label>
                    <input type="text" name="nome_atividade" id="nome_atividade" placeholder="Digite o nome da atividade">
                </div>
                <div class="input">
                    <label for="nr">NR</label>
                    <input type="text" name="nr" id="nr" placeholder="Selecione a NR da atividade">
                </div>
                <div class="input">
                    <label for="epi-1">EPI - 1</label>
                    <input type="text" name="epi-1" id="epi-1" placeholder="Insira o nome do EPI 1">
                </div>
                <div class="input">
                    <label for="funcao-1">Função - 1</label>
                    <input type="text" name="funcao-1" id="funcao-1" placeholder="Insira a função do EPI 1">
                </div>
                <div class="input">
                    <label for="descricao-1">Descrição - 1</label>
                    <input type="text" name="descricao-1" id="descricao-1" placeholder="Insira a descrição do EPI 1">
                </div>
                <div class="input">
                    <label for="ca-1">CA - 1</label>
                    <input type="text" name="ca-1" id="ca-1" placeholder="Certificado de aprovação do EPI 1">
                </div>
            </div>
            <button type="button" class="adicionar_epi">Adicionar EPI</button>
            <button type="submit" class="salvar">Salvar</button>
        </form>
        <header>
            <button class="voltar">
                <figure><img src="../templates/assets/img/seta_esquerda.png" alt=""></figure>
                Voltar
            </button>
        </header>
        <main>
            <div class="upper">
                <h1>Checklists NR-18</h1>
                <p class="gerencie">Confira todos os checklists realizados pelos dministradores.</p>
                <div class="atvs">
                    <p>Total de checklists</p>
                    <h2>11</h2>
                </div>
            </div>
            <div class="search_div">
                <div class="search_input">
                    <figure><img src="../templates/assets/img/lupa_azul.png" alt=""></figure>
                    <input type="text" name="search" id="search" placeholder="Busque por atividades">
                </div>
                <div class="buttons">
                    <button>Filtrar</button>
                </div>
            </div>
            <div class="grid">
                <div class="card">
                    <div class="card_header nao_conforme">
                        <h3 class="status_header">NÃO CONFORME</h3>
                        <p class="progress_header">Progresso: 0%</p>
                    </div>
                    <ul>
                        <li><span>Responsável: </span>João</li>
                        <li><span>Turno: </span>Matutino</li>
                        <li><span>Data: </span>05/07/2026</li>
                        <li><span>Status: </span>Não conforme</li>
                    </ul>
                    <div class="progress_bar">
                        <div class="progress_background nao_conforme" style="width: 0%;"><p class="progress_number">0%</p></div>
                    </div>
                </div>
                <div class="card">
                    <div class="card_header parcialmente_conforme">
                        <h3 class="status_header">PARCIALMENTE CONFORME</h3>
                        <p class="progress_header">Progresso: 70%</p>
                    </div>
                    <ul>
                        <li><span>Responsável: </span>João</li>
                        <li><span>Turno: </span>Matutino</li>
                        <li><span>Data: </span>05/07/2026</li>
                        <li><span>Status: </span>Parcialmente conforme</li>
                    </ul>
                    <div class="progress_bar">
                        <div class="progress_background parcialmente_conforme" style="width: 70%;"><p class="progress_number">70%</p></div>
                    </div>
                </div>
                <div class="card">
                    <div class="card_header conforme">
                        <h3 class="status_header">CONFORME</h3>
                        <p class="progress_header">Progresso: 100%</p>
                    </div>
                    <ul>
                        <li><span>Responsável: </span>João</li>
                        <li><span>Turno: </span>Matutino</li>
                        <li><span>Data: </span>05/07/2026</li>
                        <li><span>Status: </span>Conforme</li>
                    </ul>
                    <div class="progress_bar">
                        <div class="progress_background conforme" style="width: 100%;"><p class="progress_number">100%</p></div>
                    </div>
                </div>

                <!-- CARD 1: Não Conforme (0%) -->
                <div class="card">
                    <div class="card_header nao_conforme">
                        <h3 class="status_header">NÃO CONFORME</h3>
                        <p class="progress_header">Progresso: 0%</p>
                    </div>
                    <ul>
                        <li><span>Responsável: </span>João</li>
                        <li><span>Turno: </span>Matutino</li>
                        <li><span>Data: </span>05/07/2026</li>
                        <li><span>Status: </span>Não conforme</li>
                    </ul>
                    <div class="progress_bar">
                        <div class="progress_background nao_conforme" style="width: 0%;"><p class="progress_number">0%</p></div>
                    </div>
                </div>

                <!-- CARD 2: Parcialmente Conforme (70%) -->
                <div class="card">
                    <div class="card_header parcialmente_conforme">
                        <h3 class="status_header">PARCIALMENTE CONFORME</h3>
                        <p class="progress_header">Progresso: 70%</p>
                    </div>
                    <ul>
                        <li><span>Responsável: </span>Ana Silva</li>
                        <li><span>Turno: </span>Vespertino</li>
                        <li><span>Data: </span>04/07/2026</li>
                        <li><span>Status: </span>Parcialmente conforme</li>
                    </ul>
                    <div class="progress_bar">
                        <div class="progress_background parcialmente_conforme" style="width: 70%;"><p class="progress_number">70%</p></div>
                    </div>
                </div>

                <!-- CARD 3: Conforme (100%) -->
                <div class="card">
                    <div class="card_header conforme">
                        <h3 class="status_header">CONFORME</h3>
                        <p class="progress_header">Progresso: 100%</p>
                    </div>
                    <ul>
                        <li><span>Responsável: </span>Carlos Souza</li>
                        <li><span>Turno: </span>Noturno</li>
                        <li><span>Data: </span>05/07/2026</li>
                        <li><span>Status: </span>Conforme</li>
                    </ul>
                    <div class="progress_bar">
                        <div class="progress_background conforme" style="width: 100%;"><p class="progress_number">100%</p></div>
                    </div>
                </div>

                <!-- CARD 4: Não Conforme (35%) -->
                <div class="card">
                    <div class="card_header nao_conforme">
                        <h3 class="status_header">NÃO CONFORME</h3>
                        <p class="progress_header">Progresso: 35%</p>
                    </div>
                    <ul>
                        <li><span>Responsável: </span>Mariana Costa</li>
                        <li><span>Turno: </span>Matutino</li>
                        <li><span>Data: </span>03/07/2026</li>
                        <li><span>Status: </span>Não conforme</li>
                    </ul>
                    <div class="progress_bar">
                        <div class="progress_background nao_conforme" style="width: 35%;"><p class="progress_number">35%</p></div>
                    </div>
                </div>

                <!-- CARD 5: Parcialmente Conforme (85%) -->
                <div class="card">
                    <div class="card_header parcialmente_conforme">
                        <h3 class="status_header">PARCIALMENTE CONFORME</h3>
                        <p class="progress_header">Progresso: 85%</p>
                    </div>
                    <ul>
                        <li><span>Responsável: </span>Lucas Oliveira</li>
                        <li><span>Turno: </span>Vespertino</li>
                        <li><span>Data: </span>05/07/2026</li>
                        <li><span>Status: </span>Parcialmente conforme</li>
                    </ul>
                    <div class="progress_bar">
                        <div class="progress_background parcialmente_conforme" style="width: 85%;"><p class="progress_number">85%</p></div>
                    </div>
                </div>

                <!-- CARD 6: Não Conforme (50%) -->
                <div class="card">
                    <div class="card_header nao_conforme">
                        <h3 class="status_header">NÃO CONFORME</h3>
                        <p class="progress_header">Progresso: 50%</p>
                    </div>
                    <ul>
                        <li><span>Responsável: </span>Beatriz Santos</li>
                        <li><span>Turno: </span>Noturno</li>
                        <li><span>Data: </span>02/07/2026</li>
                        <li><span>Status: </span>Não conforme</li>
                    </ul>
                    <div class="progress_bar">
                        <div class="progress_background nao_conforme" style="width: 50%;"><p class="progress_number">50%</p></div>
                    </div>
                </div>

                <!-- CARD 7: Parcialmente Conforme (92%) -->
                <div class="card">
                    <div class="card_header parcialmente_conforme">
                        <h3 class="status_header">PARCIALMENTE CONFORME</h3>
                        <p class="progress_header">Progresso: 92%</p>
                    </div>
                    <ul>
                        <li><span>Responsável: </span>Fernando Lima</li>
                        <li><span>Turno: </span>Matutino</li>
                        <li><span>Data: </span>05/07/2026</li>
                        <li><span>Status: </span>Parcialmente conforme</li>
                    </ul>
                    <div class="progress_bar">
                        <div class="progress_background parcialmente_conforme" style="width: 92%;"><p class="progress_number">92%</p></div>
                    </div>
                </div>

                <!-- CARD 8: Conforme (100%) -->
                <div class="card">
                    <div class="card_header conforme">
                        <h3 class="status_header">CONFORME</h3>
                        <p class="progress_header">Progresso: 100%</p>
                    </div>
                    <ul>
                        <li><span>Responsável: </span>Juliana Ribeiro</li>
                        <li><span>Turno: </span>Vespertino</li>
                        <li><span>Data: </span>01/07/2026</li>
                        <li><span>Status: </span>Conforme</li>
                    </ul>
                    <div class="progress_bar">
                        <div class="progress_background conforme" style="width: 100%;"><p class="progress_number">100%</p></div>
                    </div>
                </div>

                <!-- CARD 9: Não Conforme (15%) -->
                <div class="card">
                    <div class="card_header nao_conforme">
                        <h3 class="status_header">NÃO CONFORME</h3>
                        <p class="progress_header">Progresso: 15%</p>
                    </div>
                    <ul>
                        <li><span>Responsável: </span>Ricardo Alves</li>
                        <li><span>Turno: </span>Noturno</li>
                        <li><span>Data: </span>04/07/2026</li>
                        <li><span>Status: </span>Não conforme</li>
                    </ul>
                    <div class="progress_bar">
                        <div class="progress_background nao_conforme" style="width: 15%;"><p class="progress_number">15%</p></div>
                    </div>
                </div>

                <!-- CARD 10: Parcialmente Conforme (75%) -->
                <div class="card">
                    <div class="card_header parcialmente_conforme">
                        <h3 class="status_header">PARCIALMENTE CONFORME</h3>
                        <p class="progress_header">Progresso: 75%</p>
                    </div>
                    <ul>
                        <li><span>Responsável: </span>Camila Rocha</li>
                        <li><span>Turno: </span>Matutino</li>
                        <li><span>Data: </span>03/07/2026</li>
                        <li><span>Status: </span>Parcialmente conforme</li>
                    </ul>
                    <div class="progress_bar">
                        <div class="progress_background parcialmente_conforme" style="width: 75%;"><p class="progress_number">75%</p></div>
                    </div>
                </div>

                <!-- CARD 11: Não Conforme (62%) -->
                <div class="card">
                    <div class="card_header nao_conforme">
                        <h3 class="status_header">NÃO CONFORME</h3>
                        <p class="progress_header">Progresso: 62%</p>
                    </div>
                    <ul>
                        <li><span>Responsável: </span>Gabriel Melo</li>
                        <li><span>Turno: </span>Vespertino</li>
                        <li><span>Data: </span>05/07/2026</li>
                        <li><span>Status: </span>Não conforme</li>
                    </ul>
                    <div class="progress_bar">
                        <div class="progress_background nao_conforme" style="width: 62%;"><p class="progress_number">62%</p></div>
                    </div>
                </div>

                <!-- CARD 12: Conforme (100%) -->
                <div class="card">
                    <div class="card_header conforme">
                        <h3 class="status_header">CONFORME</h3>
                        <p class="progress_header">Progresso: 100%</p>
                    </div>
                    <ul>
                        <li><span>Responsável: </span>Patricia Dias</li>
                        <li><span>Turno: </span>Matutino</li>
                        <li><span>Data: </span>02/07/2026</li>
                        <li><span>Status: </span>Conforme</li>
                    </ul>
                    <div class="progress_bar">
                        <div class="progress_background conforme" style="width: 100%;"><p class="progress_number">100%</p></div>
                    </div>
                </div>

                <!-- CARD 13: Parcialmente Conforme (79%) -->
                <div class="card">
                    <div class="card_header parcialmente_conforme">
                        <h3 class="status_header">PARCIALMENTE CONFORME</h3>
                        <p class="progress_header">Progresso: 79%</p>
                    </div>
                    <ul>
                        <li><span>Responsável: </span>Rodrigo Cruz</li>
                        <li><span>Turno: </span>Noturno</li>
                        <li><span>Data: </span>04/07/2026</li>
                        <li><span>Status: </span>Parcialmente conforme</li>
                    </ul>
                    <div class="progress_bar">
                        <div class="progress_background parcialmente_conforme" style="width: 79%;"><p class="progress_number">79%</p></div>
                    </div>
                </div>

                <!-- CARD 14: Não Conforme (40%) -->
                <div class="card">
                    <div class="card_header nao_conforme">
                        <h3 class="status_header">NÃO CONFORME</h3>
                        <p class="progress_header">Progresso: 40%</p>
                    </div>
                    <ul>
                        <li><span>Responsável: </span>Amanda Borges</li>
                        <li><span>Turno: </span>Vespertino</li>
                        <li><span>Data: </span>03/07/2026</li>
                        <li><span>Status: </span>Não conforme</li>
                    </ul>
                    <div class="progress_bar">
                        <div class="progress_background nao_conforme" style="width: 40%;"><p class="progress_number">40%</p></div>
                    </div>
                </div>

                <!-- CARD 15: Parcialmente Conforme (95%) -->
                <div class="card">
                    <div class="card_header parcialmente_conforme">
                        <h3 class="status_header">PARCIALMENTE CONFORME</h3>
                        <p class="progress_header">Progresso: 95%</p>
                    </div>
                    <ul>
                        <li><span>Responsável: </span>Thiago Martins</li>
                        <li><span>Turno: </span>Matutino</li>
                        <li><span>Data: </span>05/07/2026</li>
                        <li><span>Status: </span>Parcialmente conforme</li>
                    </ul>
                    <div class="progress_bar">
                        <div class="progress_background parcialmente_conforme" style="width: 95%;"><p class="progress_number">95%</p></div>
                    </div>
                </div>

            </div>
        </main>
    </body>
</html>