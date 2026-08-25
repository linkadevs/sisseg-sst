<?php
session_start();

// ============================================
// ARRAY COM OS ÍCONES
// ============================================
$icons = [
    'chave_inglesa' => '🔧️',
    'guindaste' => '🏗️',
    'ferramentas' => '🛠️',
    'alta_tensao' => '⚡️',
    'engrenagem' => '⚙️',
    'fogo' => '🔥',
    'escada' => '🪜',
    'trator' => '🚜',
    'caixa_pacote' => '📦',
    'caminhao' => '🚛',
    'deposito_galpao' => '🏬',
    'etiqueta' => '🏷️',
    'colete_seguranca' => '🦺',
    'bota_protecao' => '🥾',
    'oculos_protecao' => '🥽',
    'protetor_auricular' => '🎧',
    'luvas' => '🧤',
    'mascara_protecao' => '😷',
    'corda_no' => '🪢',
    'capacete_obras' => '👷‍♀️',
    'capacete_obras_sol' => '👷‍♂️'
];

if (!isset($_SESSION['id_atividade_modulo']) || $_SESSION['id_atividade_modulo'] <= 0) {
    header('Location: selecao_atividade.php');
    exit;
}

$id_atividade = $_SESSION['id_atividade_modulo'];
$nome_atividade = $_SESSION['nome_atividade_modulo'] ?? 'Atividade não encontrada';
$nome_nr = $_SESSION['nr_atividade_modulo'] ?? 'NR não atribuído';
$id_nr_fk = $_SESSION['idnr_atividade_modulo'] ?? 0;
$quantidade_epis = $_SESSION['qtdepis_atividade_modulo'] ?? 0;

require_once __DIR__ . '/../Controller/ModuloVerificacaoeEpiController.php';

$controller = new Controller\ModuloVerificacaoeEpiController();
$epis = $controller->obterepis($id_atividade);
$norma = $controller->exibirNorma($id_nr_fk);


$icone_atividade = isset($_SESSION['icone_atividade_modulo']) ? $_SESSION['icone_atividade_modulo'] : '';
$icone_exibicao = isset($icons[$icone_atividade]) ? $icons[$icone_atividade] : '📌';
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmação de EPIs</title>
    <link rel="stylesheet" href="../templates/assets/css/confirmacao_epis.css">
</head>

<body>
    <div class="container">
        <header class="conteudo-atividade">
            <figure>
            
                <span style="font-size: 40px;"><?php echo $icone_exibicao; ?></span>
            </figure>
            <div class="texto-atividade">
                <h1><?php echo htmlspecialchars($nome_atividade); ?></h1>
                <p><?php echo htmlspecialchars($nome_nr); ?></p>
            </div>
        </header>

        <main>
            <div class="progresso-container">
                <div class="progresso-texto">
                    <p>EPIs Confirmados</p>
                    <p class="contador">0 / <?php echo $quantidade_epis; ?></p>
                </div>
                <div class="barra-fundo">
                    <div class="barra-progresso"></div>
                </div>
            </div>

            <div class="lista-epis">
                <?php foreach ($epis as $key => $epi) : ?>
                <div class="card-epi">
                    <div class="bloco-esquerda">
                        <figure class="icone-epi">
                            <img src="../templates/assets/img/verificacao-de-escudo.png" alt="Escudo">
                        </figure>
                        <div class="info-epi">
                            <h2><?php echo htmlspecialchars($epi['nome_epi'] ?? 'Não encontrado'); ?></h2>
                            <p class="label">Função:</p>
                            <p class="detalhe"><?php echo htmlspecialchars($epi['funcao_epi'] ?? 'Não encontrado'); ?></p>
                            <p class="label">Descrição:</p>
                            <p class="detalhe"><?php echo htmlspecialchars($epi['descricao_epi'] ?? 'Não encontrado'); ?></p>
                            <div class="tags">
                                <span class="tag-ca"><?php echo htmlspecialchars($epi['ca_epi'] ?? 'Não encontrado'); ?></span>
                                <span class="tag-nr"><?php echo htmlspecialchars($nome_nr); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="bloco-direita">
                        <label class="checkbox-container">
                            <input type="checkbox" class="epi-checkbox">
                            <span class="checkmark"></span>
                        </label>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="msg-alerta">
                <figure class="icone-alerta">
                    <img src="../templates/assets/img/alerta-escudo.png" alt="Escudo de Atenção">
                </figure>
                <div class="texto-alerta">
                    <p><span>Atenção!</span></p>
                    <p>Você deve confirmar o uso de TODOS os EPIs obrigatórios antes de prosseguir. Sua segurança depende disso.</p>
                </div>
            </div>

            <footer class="botoes-acao">
                <button type="button" class="btn-voltar" onclick="window.history.back()">
                    <img src="../templates/assets/img/seta-cinza-esquerda.png" alt="Seta para esquerda"> Voltar
                </button>
                <button type="submit" class="btn-confirmar" disabled>
                    Confirmar e Continuar
                    <div class="setacinza">
                        <img src="../templates/assets/img/seta-cinza-direita.png" alt="Seta cinza para direita">
                    </div>
                    <div class="setabranca">
                        <img src="../templates/assets/img/seta-direita-branca.png" alt="seta branca para a direita">
                    </div>
                </button>
            </footer>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const cards = document.querySelectorAll('.card-epi');
            const checkboxes = document.querySelectorAll('.epi-checkbox');
            const botaoconfirmar = document.querySelector('.btn-confirmar');
            const contador = document.querySelector('.contador');
            const barraProgresso = document.querySelector('.barra-progresso');
            const setacinza = document.querySelector('.setacinza');
            const setabranca = document.querySelector('.setabranca');
            const totalEpis = cards.length;

            function atualizarprogresso() {
                const cardsmarcados = document.querySelectorAll('.epi-checkbox:checked').length;
                const percentual = (cardsmarcados / totalEpis) * 100;

                contador.textContent = cardsmarcados + ' / ' + totalEpis;
                barraProgresso.style.width = percentual + '%';

                if (cardsmarcados === totalEpis) {
                    botaoconfirmar.disabled = false;
                    setacinza.style.display = 'none';
                    setabranca.style.display = 'block';
                } else {
                    botaoconfirmar.disabled = true;
                    setacinza.style.display = 'block';
                    setabranca.style.display = 'none';
                }

                cards.forEach(card => {
                    const checkbox = card.querySelector('.epi-checkbox');
                    if (checkbox.checked) {
                        card.classList.add('confirmado');
                    } else {
                        card.classList.remove('confirmado');
                    }
                });
            }

            cards.forEach(card => {
                card.addEventListener('click', function (e) {
                    if (e.target.closest('.checkbox-container')) {
                        return;
                    }
                    const checkbox = this.querySelector('.epi-checkbox');
                    checkbox.checked = !checkbox.checked;
                    const event = new Event('change', { bubbles: true });
                    checkbox.dispatchEvent(event);
                });
            });

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function () {
                    atualizarprogresso();
                });
            });

            botaoconfirmar.addEventListener('click', function () {
                if (!this.disabled) {
                    window.location.href = 'liberacao_adm.php';
                }
            });
        });
    </script>
</body>

</html>