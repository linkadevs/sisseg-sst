<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Controller/FuncionarioController.php';

use Controller\FuncionarioController;

$funcionarioController = new FuncionarioController();

if(!empty($_SESSION['erro'])) {
    $erro = $_SESSION['erro'];
    echo "<script>alert('$erro')</script>";
}

$funcionarios = $funcionarioController->selecionarTodosOsFuncionarios();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(!empty($_POST['funcionario'])) {
        if($_POST['funcionario'] == 'criar') {
            $funcionarioController->criarFuncionario(
                $_POST['nome'],
                $_POST['cpf'],
                $_POST['setor'],
                $_POST['cargo'],
                $_POST['turno'],
                $_POST['senha']
            );
            header('Location: gerenciamento_funcionarios.php');
            exit;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Funcionários - Interativo</title>
    <link rel="stylesheet" href="../templates/assets/css/gerenciamento_funcionarios.css">
</head>

<body>
    <div class="container">
        <div id="modal-cadastro" class="modal-overlay">
            <div class="modal-card">
                <div class="modal-header">
                    <h2>Cadastrar Novo Funcionário</h2>
                    <button type="button" class="btn-fechar-modal" id="btn-fechar-modal">&times;</button>
                </div>

                <form method="POST" id="form-cadastrar-funcionario" class="form-modal">
                    <input type="hidden" name="acao" value="cadastrar">

                    <div class="form-linha">
                        <div class="form-grupo">
                            <label for="nome">Nome Completo</label>
                            <input type="text" id="nome" name="nome" placeholder="Ex: Carlos Eduardo Santos" required>
                        </div>
                        <div class="form-grupo">
                            <label for="turno">Turno</label>
                            <select name="turno" id="turno" required>
                                <option value="placeholder" disabled selected>Selecione o seu turno</option>
                                <option value="matutino">Matutino</option>
                                <option value="vespertino">Vespertino</option>
                                <option value="noturno">Noturno</option>
                                <option value="integral">Integral</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-linha">
                        <div class="form-grupo">
                            <label for="cpf">CPF</label>
                            <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" maxlength="14" required>
                        </div>

                        <div class="form-grupo">
                            <label for="senha">Senha do App</label>
                            <input type="password" id="senha" name="senha" placeholder="••••••••" required>
                        </div>
                    </div>

                    <div class="form-linha">
                        <div class="form-grupo">
                            <label for="cargo">Cargo / Função</label>
                            <input type="text" id="cargo" name="cargo" placeholder="Ex: Mestre de Obras" required>
                        </div>

                        <div class="form-grupo">
                            <label for="setor">Setor</label>
                            <select id="setor" name="setor" required>
                                <option value="placeholder" disabled selected>Selecione um setor...</option>
                                <option value="Operacional">Operacional</option>
                                <option value="HSE / Segurança">HSE / Segurança</option>
                                <option value="Administrativo">Administrativo</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-acoes">
                        <button type="button" class="btn-secundario" id="btn-cancelar">Cancelar</button>
                        <button type="submit" class="btn-principal" name="funcionario" value="criar">Salvar Funcionário</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="nav-interna">
            <a href="Modulo_funcoes.html" class="btn-voltar">
                <img src="../templates/assets/img/seta-cinza-esquerda.png" alt="Voltar"> Voltar ao Painel
            </a>
        </div>

        <header class="header-dashboard">
            <div class="titulo-sessao">
                <h1>Gerenciamento de Funcionários</h1>
                <p>Gerencie o acesso, cargos e setores de toda a sua equipe</p>
            </div>
            <div class="card-contador">
                <p class="contador-label">Funcionários Ativos</p>
                <p class="contador-numero"><?= htmlspecialchars(count($funcionarios))?></p>
            </div>
        </header>

        <main>
            <div class="barra-ferramentas">
                <div class="campo-busca">
                    <img src="../templates/assets/img/lupa_azul.png" alt="Buscar" class="lupa-busca">
                    <input type="text" placeholder="Busque por nome, CPF, cargo ou setor...">
                </div>
                <div class="botoes-grupo">
                    <button type="button" class="btn-principal adicionar_funcionario">
                        <span class="sinal-mais"><img src="../templates/assets/img/mais.png" alt=""></span> Cadastrar Funcionário
                    </button>
                </div>
            </div>

            <div class="card-tabela-container">
                <table class="tabela-funcionarios">
                    <thead>
                        <tr>
                            <th>Nome do Colaborador</th>
                            <th>CPF</th>
                            <th>Cargo / Função</th>
                            <th>Setor</th>
                            <th>Senha do App</th>
                            <th class="coluna-acoes">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($funcionarios as $funcionario):?>
                            <tr>
                                <td>
                                    <div class="funcionario-perfil">
                                        <span class="indicador-status ativo"></span>
                                        <p class="nome"><?= htmlspecialchars($funcionario['nome_funcionario'])?></p>
                                    </div>
                                </td>
                                <td class="texto-mutado font-mono"><?= htmlspecialchars($funcionario['cpf_funcionario'])?></td>
                                <td><span class="tag-cargo"><?= htmlspecialchars($funcionario['cargo_funcionario'])?></span></td>
                                <td><span class="badge-setor"><?= htmlspecialchars($funcionario['setor_funcionario'])?></span></td>
                                <td>
                                    <div class="wrapper-senha">
                                        <span class="senha-mascarada">••••••••</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="wrapper-acoes">
                                        <button type="button" class="btn-icone-acao editar" title="Editar dados">
                                            Editar
                                        </button>
                                        <button type="button" class="btn-icone-acao deletar" title="Excluir funcionário">
                                        Excluir
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    <script src="../templates/assets/js/gerenciamento_funcionarios.js"></script>
</body>

</html>