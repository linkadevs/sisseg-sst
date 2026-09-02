<?php
session_start();

require_once __DIR__ . '/../Controller/ModuloInspecaoController.php';

$controller = new Controller\ModuloInspecaoController();

$funcoes = $controller->listarFuncoes();
$total_funcoes = count($funcoes);
$todos_epis = $controller->listarTodosEpis();

$mensagem = $_SESSION['mensagem'] ?? '';
$mensagem_tipo = $_SESSION['mensagem_tipo'] ?? '';
if(!empty($mensagem)) {
    echo '<script>alert("'. $mensagem .'")</script>';
    unset($_SESSION['mensagem'], $_SESSION['mensagem_tipo']);
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo de Inspeção de EPIs - Funções (Admin)</title>
    <link rel="stylesheet" href="../templates/assets/css/modulo_funcoes.css">
</head>

<body>
    <div class="container">
        <div class="nav-interna">
            <a href="principal_adm.php" class="btn-voltar">
                <img src="../templates/assets/img/seta-cinza-esquerda.png" alt="Voltar"> Voltar
            </a>
        </div>

        <header class="header-dashboard">
            <div class="titulo-sessao">
                <div class="configuracao">
                    <figure>
                        <img src="../templates/assets/img/configuracao-enfeite.png" alt="Engrenagem">
                    </figure>
                </div>
                <div class="textos">
                    <h1>Gerenciamento de Funções</h1>
                    <p>Configure e gerencie os EPIs obrigatórios de cada cargo</p>
                </div>
            </div>
            <div class="card-contador">
                <p class="contador-label">Total de funções</p>
                <p class="contador-numero"><?php echo $total_funcoes; ?></p>
            </div>
        </header>

        <main>
            <div class="barra-ferramentas">
                <div class="campo-busca">
                    <img src="../templates/assets/img/lupa_azul.png" alt="Buscar" class="lupa-busca">
                    <input type="text" placeholder="Busque por funções" id="busca-funcao">
                </div>
                <div class="botoes-grupo">
                    <button type="button" class="btn-secundario btn-registro" onclick="window.location.href='registro_inspecoes.php'">
                        <img src="../templates/assets/img/pasta.png" alt="Histórico"> Inspeções
                    </button>
                    <button type="button" class="btn-principal btn-azul-claro" id="btn-abrir-modal-criar">
                        <img src="../templates/assets/img/mais.png" alt="Simbolo de mais"> Criar função
                    </button>
                </div>
            </div>

            <div class="grid-funcoes" id="grid-funcoes">
                <?php if (!empty($funcoes)): ?>
                    <?php foreach ($funcoes as $funcao): ?>
                        <div class="card-funcao" data-id="<?php echo $funcao['id_funcao']; ?>">
                            <div class="card-conteudo">
                                <h3><?php echo htmlspecialchars($funcao['nome_funcao']); ?></h3>
                                <p class="qtd-epis"><?php echo $funcao['total_epis']; ?> EPIs obrigatórios</p>
                                <ul class="lista-prev-epis" id="lista-epis-<?php echo $funcao['id_funcao']; ?>">
                                    <?php 
                                        $epis_funcao = $controller->buscarEpisPorFuncao($funcao['id_funcao']);
                                        if (!empty($epis_funcao)): 
                                            $count = 0;
                                            foreach ($epis_funcao as $epi): 
                                                if ($count < 3): 
                                    ?>
                                        <li><?php echo htmlspecialchars($epi['nome_epi']); ?></li>
                                    <?php 
                                                $count++;
                                                endif;
                                            endforeach;
                                            if (count($epis_funcao) > 3): 
                                    ?>
                                        <li class="mais-itens">+<?php echo (count($epis_funcao) - 3); ?> mais...</li>
                                    <?php 
                                            endif;
                                        else: 
                                    ?>
                                        <li>Nenhum EPI cadastrado</li>
                                    <?php endif; ?>
                                </ul>
                                <div class="acoes-card">
                                    <button type="button" class="btn-acao-card btn-editar" data-id="<?php echo $funcao['id_funcao']; ?>">
                                        Editar função
                                    </button>
                                    <button type="button" class="btn-excluir-card" data-id="<?php echo $funcao['id_funcao']; ?>" data-nome="<?php echo htmlspecialchars($funcao['nome_funcao']); ?>">
                                        Excluir
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="sem-funcoes">Nenhuma função cadastrada.</p>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- ===== MODAL CRIAR ===== -->
    <div class="modal-overlay" id="modal-criar">
        <div class="modal-card">
            <header class="modal-header">
                <h2>Criar nova função</h2>
                <button type="button" class="btn-fechar-modal-editar" id="btn-fechar-criar">
                    <img src="../templates/assets/img/x_preto.png" alt="x preto">
                </button>
            </header>

            <form class="modal-corpo" id="form-criar" method="POST" action="../processa_funcao.php">
                <input type="hidden" name="action" value="criar">

                <div class="modal-campo">
                    <label for="nome-funcao">Nome da Função</label>
                    <input type="text" id="nome-funcao" name="nome_funcao" placeholder="Ex: Gesseiro / Drywall" required>
                </div>

                <div id="campos-epis-criar">
                    <div class="modal-campo epi-campo">
                        <label for="epi-1">EPI - 1</label>
                        <select id="epi-1" name="epis[]" class="select-epi">
                            <option value="">Selecione um EPI</option>
                            <?php foreach ($todos_epis as $epi): ?>
                                <option value="<?php echo $epi['id_epi']; ?>"><?php echo htmlspecialchars($epi['nome_epi']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="modal-botoes" id="botoes-container-criar">
                    <button type="button" class="btn-modal-adicionar" id="btn-adicionar-epi-criar">Adicionar EPI</button>
                    <button type="submit" class="btn-modal-salvar">Criar função</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ===== MODAL EDITAR ===== -->
    <div class="modal-overlay" id="modal-editar">
        <div class="modal-card">
            <header class="modal-header">
                <h2>Editar função</h2>
                <button type="button" class="btn-fechar-modal-editar" id="btn-fechar-editar">
                    <img src="../templates/assets/img/x_preto.png" alt="x preto">
                </button>
            </header>

            <form class="modal-corpo" id="form-editar" method="POST" action="../processa_funcao.php">
                <input type="hidden" name="action" value="atualizar">
                <input type="hidden" name="id_funcao" id="editar-id-funcao" value="">

                <div class="modal-campo">
                    <label for="editar-nome-funcao">Nome da Função</label>
                    <input type="text" id="editar-nome-funcao" name="nome_funcao" placeholder="Ex: Gesseiro / Drywall" required>
                </div>

                <div id="editar-campos-epis"></div>

                <div class="modal-botoes" id="botoes-container-editar">
                    <button type="button" class="btn-modal-adicionar-editar" id="btn-adicionar-epi-editar">Adicionar EPI</button>
                    <button type="submit" class="btn-modal-salvar">Salvar alterações</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let contadorEPI = 1;
        let contadorEPIEditar = 1;

        const todosEpis = <?php echo json_encode($todos_epis); ?>;

        function criarSelectEPI(name, selectedValue) {
            const select = document.createElement('select');
            select.name = name;
            select.className = 'select-epi';
            
            const optionDefault = document.createElement('option');
            optionDefault.value = '';
            optionDefault.textContent = 'Selecione um EPI';
            select.appendChild(optionDefault);
            
            todosEpis.forEach(function(epi) {
                const option = document.createElement('option');
                option.value = epi.id_epi;
                option.textContent = epi.nome_epi;
                if (selectedValue && option.value == selectedValue) {
                    option.selected = true;
                }
                select.appendChild(option);
            });
            
            return select;
        }

        // ===== MODAL CRIAR =====
        const modalCriar = document.getElementById('modal-criar');
        const btnAbrirCriar = document.getElementById('btn-abrir-modal-criar');
        const btnFecharCriar = document.getElementById('btn-fechar-criar');
        const btnAdicionarEPI = document.getElementById('btn-adicionar-epi-criar');
        const formCriar = document.getElementById('form-criar');
        const camposEpisCriar = document.getElementById('campos-epis-criar');
        const botoesContainerCriar = document.getElementById('botoes-container-criar');

        // ===== MODAL EDITAR =====
        const modalEditar = document.getElementById('modal-editar');
        const btnFecharEditar = document.getElementById('btn-fechar-editar');
        const btnAdicionarEPIEditar = document.getElementById('btn-adicionar-epi-editar');
        const formEditar = document.getElementById('form-editar');
        const containerCamposEPIs = document.getElementById('editar-campos-epis');
        const botoesContainerEditar = document.getElementById('botoes-container-editar');

        // ABRIR MODAL CRIAR
        if (btnAbrirCriar) {
            btnAbrirCriar.addEventListener('click', function(e) {
                e.preventDefault();
                modalCriar.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            });
        }

        // FECHAR MODAL CRIAR
        if (btnFecharCriar) {
            btnFecharCriar.addEventListener('click', function(e) {
                e.preventDefault();
                modalCriar.style.display = 'none';
                document.body.style.overflow = 'auto';
                resetarFormCriar();
            });
        }

        // ADICIONAR EPI NO MODAL CRIAR
        if (btnAdicionarEPI && camposEpisCriar) {
            btnAdicionarEPI.addEventListener('click', function(e) {
                e.preventDefault();
                contadorEPI++;

                const novoCampo = document.createElement('div');
                novoCampo.className = 'modal-campo epi-campo';
                novoCampo.style.marginTop = '1rem';
                
                const label = document.createElement('label');
                label.setAttribute('for', 'epi-' + contadorEPI);
                label.textContent = 'EPI - ' + contadorEPI;
                novoCampo.appendChild(label);
                
                const select = criarSelectEPI('epis[]', null);
                select.id = 'epi-' + contadorEPI;
                novoCampo.appendChild(select);
                
                camposEpisCriar.appendChild(novoCampo);
            });
        }

        // ABRIR MODAL EDITAR
        const botoesEditar = document.querySelectorAll('.btn-editar');

        botoesEditar.forEach((botao) => {
            botao.addEventListener('click', function(e) {
                e.preventDefault();
                const card = this.closest('.card-funcao');
                const id = this.dataset.id;
                const nomeFuncao = card.querySelector('h3').textContent;
                const listaEPIs = card.querySelectorAll('.lista-prev-epis li:not(.mais-itens)');

                document.getElementById('editar-id-funcao').value = id;
                document.getElementById('editar-nome-funcao').value = nomeFuncao;

                containerCamposEPIs.innerHTML = '';

                if (listaEPIs.length > 0) {
                    listaEPIs.forEach(function(epi, i) {
                        const num = i + 1;
                        const campo = document.createElement('div');
                        campo.className = 'modal-campo epi-campo';
                        if (i > 0) {
                            campo.style.marginTop = '1rem';
                        }
                        
                        const label = document.createElement('label');
                        label.setAttribute('for', 'editar-epi-' + num);
                        label.textContent = 'EPI - ' + num;
                        campo.appendChild(label);
                        
                        const select = criarSelectEPI('epis[]', null);
                        select.id = 'editar-epi-' + num;
                        campo.appendChild(select);
                        
                        const nomeEpi = epi.textContent.trim();
                        todosEpis.forEach(function(e) {
                            if (e.nome_epi === nomeEpi) {
                                select.value = e.id_epi;
                            }
                        });
                        
                        containerCamposEPIs.appendChild(campo);
                    });
                    contadorEPIEditar = listaEPIs.length;
                } else {
                    const campo = document.createElement('div');
                    campo.className = 'modal-campo epi-campo';
                    
                    const label = document.createElement('label');
                    label.setAttribute('for', 'editar-epi-1');
                    label.textContent = 'EPI - 1';
                    campo.appendChild(label);
                    
                    const select = criarSelectEPI('epis[]', null);
                    select.id = 'editar-epi-1';
                    campo.appendChild(select);
                    
                    containerCamposEPIs.appendChild(campo);
                    contadorEPIEditar = 1;
                }

                modalEditar.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            });
        });

        // FECHAR MODAL EDITAR
        if (btnFecharEditar) {
            btnFecharEditar.addEventListener('click', function(e) {
                e.preventDefault();
                modalEditar.style.display = 'none';
                document.body.style.overflow = 'auto';
            });
        }

        // ADICIONAR EPI NO MODAL EDITAR
        if (btnAdicionarEPIEditar && containerCamposEPIs) {
            btnAdicionarEPIEditar.addEventListener('click', function(e) {
                e.preventDefault();
                contadorEPIEditar++;

                const novoCampo = document.createElement('div');
                novoCampo.className = 'modal-campo epi-campo';
                novoCampo.style.marginTop = '1rem';
                
                const label = document.createElement('label');
                label.setAttribute('for', 'editar-epi-' + contadorEPIEditar);
                label.textContent = 'EPI - ' + contadorEPIEditar;
                novoCampo.appendChild(label);
                
                const select = criarSelectEPI('epis[]', null);
                select.id = 'editar-epi-' + contadorEPIEditar;
                novoCampo.appendChild(select);
                
                containerCamposEPIs.appendChild(novoCampo);
            });
        }

        // ============================================
        // EXCLUIR FUNÇÃO
        // ============================================
        const botoesExcluir = document.querySelectorAll('.btn-excluir-card');

        botoesExcluir.forEach((botao) => {
            botao.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const id = this.dataset.id;
                const nome = this.dataset.nome;
                
                if (confirm('Tem certeza que deseja excluir a função "' + nome + '"? Esta ação não pode ser desfeita.')) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '../processa_funcao.php';
                    
                    const inputAction = document.createElement('input');
                    inputAction.type = 'hidden';
                    inputAction.name = 'action';
                    inputAction.value = 'deletar';
                    form.appendChild(inputAction);
                    
                    const inputId = document.createElement('input');
                    inputId.type = 'hidden';
                    inputId.name = 'id_funcao';
                    inputId.value = id;
                    form.appendChild(inputId);
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });

        function resetarFormCriar() {
            formCriar.reset();
            const camposExtras = camposEpisCriar.querySelectorAll('.epi-campo:not(:first-child)');
            camposExtras.forEach(function(campo) {
                campo.remove();
            });
            contadorEPI = 1;
            const primeiroSelect = document.querySelector('#campos-epis-criar .select-epi');
            if (primeiroSelect) primeiroSelect.value = '';
        }

        // FECHAR MODAL CLICANDO FORA
        document.querySelectorAll('.modal-overlay').forEach(function(overlay) {
            overlay.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.style.display = 'none';
                    document.body.style.overflow = 'auto';
                    if (this === modalCriar) {
                        resetarFormCriar();
                    }
                }
            });
        });

        // FECHAR MODAL COM TECLA ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (modalEditar.style.display === 'flex') {
                    modalEditar.style.display = 'none';
                    document.body.style.overflow = 'auto';
                } else if (modalCriar.style.display === 'flex') {
                    modalCriar.style.display = 'none';
                    document.body.style.overflow = 'auto';
                    resetarFormCriar();
                }
            }
        });

        // FILTRO DE BUSCA
        const buscaInput = document.getElementById('busca-funcao');
        if (buscaInput) {
            buscaInput.addEventListener('keyup', function() {
                const termo = this.value.toLowerCase().trim();
                const cards = document.querySelectorAll('.card-funcao');
                const containerCards = document.querySelector('.container-cards') || cards[0]?.parentElement;
                
                let visiveis = 0;

                cards.forEach(function(card) {
                    const nome = card.querySelector('h3').textContent.toLowerCase();
                    if (nome.includes(termo)) {
                        card.style.display = 'block';
                        visiveis++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                // Gerencia a mensagem de "Nenhuma função encontrada"
                let mensagemVazia = document.getElementById('mensagem-busca-vazia');

                if (visiveis === 0) {
                    if (!mensagemVazia && containerCards) {
                        mensagemVazia = document.createElement('h3');
                        mensagemVazia.id = 'mensagem-busca-vazia';
                        mensagemVazia.className = 'mensagem-vazia';
                        mensagemVazia.textContent = 'Nenhuma função encontrada.';
                        containerCards.appendChild(mensagemVazia);
                    } else if (mensagemVazia) {
                        mensagemVazia.style.display = 'block';
                    }
                } else if (mensagemVazia) {
                    mensagemVazia.style.display = 'none';
                }
            });
        }
    });
    </script>
</body>

</html>