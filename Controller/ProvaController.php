<?php
namespace Controller;

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ ."/../Model/Prova.php";

use Model\Prova;
use InvalidArgumentException;
use PDOException;

/**
 * Orquestra as ações de prova/questão vindas da API.
 * Toda validação de entrada mora aqui; o Model só fala com o banco.
 */
class ProvaController
{
    private Prova $provaModel;

    private const LETRAS_VALIDAS = ['a', 'b', 'c', 'd', 'e'];

    public function __construct()
    {
        $this->provaModel = new Prova();
    }

    /**
     * Ponto único de entrada usado pelo prova-api.php.
     * $acao: criar | editar | excluir | criar_questao | editar_questao | excluir_questao | buscar | registrarResultado
     */
    public function handle(string $acao, array $dados): array
    {
        try {
            return match ($acao) {
                'criar'              => $this->criarProva($dados),
                'editar'             => $this->editarProva($dados),
                'excluir'            => $this->excluirProva($dados),
                'criar_questao'      => $this->criarQuestao($dados),
                'editar_questao'     => $this->editarQuestao($dados),
                'excluir_questao'    => $this->excluirQuestao($dados),
                'buscar'             => $this->buscarPorTreinamento($dados),
                'registrarResultado' => $this->registrarResultado($dados),
                default              => ['success' => false, 'message' => 'Ação inválida.'],
            };
        } catch (InvalidArgumentException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro de banco de dados.', 'error' => $e->getMessage()];
        }
    }

    // ================= CRIAR PROVA =================

    public function criarProva(array $dados): array
    {
        $nome = trim((string) ($dados['nome_prova'] ?? ''));
        $idTreinamento = (int) ($dados['id_treinamento'] ?? 0);
        $questoes = $dados['questoes'] ?? [];

        if ($nome === '') {
            throw new InvalidArgumentException('Informe o título da prova.');
        }
        if ($idTreinamento <= 0) {
            throw new InvalidArgumentException('Treinamento vinculado inválido.');
        }
        if (!is_array($questoes) || count($questoes) === 0) {
            throw new InvalidArgumentException('Adicione ao menos uma questão.');
        }

        $idProva = $this->provaModel->createProva($nome, $idTreinamento);

        foreach ($questoes as $questao) {
            $this->criarQuestaoParaProva($idProva, $questao);
        }

        return ['success' => true, 'id_prova' => $idProva];
    }

    // ================= EDITAR PROVA =================

    public function editarProva(array $dados): array
    {
        $idProva = $this->resolverIdProva($dados);
        $nome = trim((string) ($dados['nome_prova'] ?? ''));
        $questoes = $dados['questoes'] ?? [];

        if ($nome === '') {
            throw new InvalidArgumentException('Informe o título da prova.');
        }

        $this->provaModel->updateProva($idProva, $nome);

        // Sincroniza questões: quem já tem id_questao é atualizado,
        // quem não tem é criado, e quem sumiu do payload é excluído.
        $questoesAtuais = $this->provaModel->getAllQuestion($idProva);
        $idsRecebidos = [];

        foreach ($questoes as $questao) {
            if (!empty($questao['id_questao'])) {
                $id = (int) $questao['id_questao'];
                $this->editarQuestaoDaProva($id, $questao);
                $idsRecebidos[] = $id;
            } else {
                $idsRecebidos[] = $this->criarQuestaoParaProva($idProva, $questao);
            }
        }

        foreach ($questoesAtuais as $questaoAtual) {
            $idAtual = (int) $questaoAtual['id_questao'];
            if (!in_array($idAtual, $idsRecebidos, true)) {
                $this->provaModel->deleteQuestion($idAtual);
            }
        }

        return ['success' => true, 'id_prova' => $idProva];
    }

    // ================= EXCLUIR PROVA =================

    public function excluirProva(array $dados): array
    {
        $idProva = $this->resolverIdProva($dados);
        $this->provaModel->deleteProva($idProva);
        return ['success' => true];
    }

    // ================= QUESTÃO (avulsa) =================

    public function criarQuestao(array $dados): array
    {
        $idProva = (int) ($dados['id_prova'] ?? 0);
        if ($idProva <= 0) {
            throw new InvalidArgumentException('Prova inválida.');
        }

        $idQuestao = $this->criarQuestaoParaProva($idProva, $dados);
        return ['success' => true, 'id_questao' => $idQuestao];
    }

    public function editarQuestao(array $dados): array
    {
        $idQuestao = (int) ($dados['id_questao'] ?? 0);
        if ($idQuestao <= 0) {
            throw new InvalidArgumentException('Questão inválida.');
        }

        $this->editarQuestaoDaProva($idQuestao, $dados);
        return ['success' => true];
    }

    public function excluirQuestao(array $dados): array
    {
        $idQuestao = (int) ($dados['id_questao'] ?? 0);
        if ($idQuestao <= 0) {
            throw new InvalidArgumentException('Questão inválida.');
        }

        $this->provaModel->deleteQuestion($idQuestao);
        return ['success' => true];
    }

    // ================= BUSCAR (carregar prova existente no modal) =================

    public function buscarPorTreinamento(array $dados): array
    {
        $idTreinamento = (int) ($dados['id_treinamento'] ?? 0);
        if ($idTreinamento <= 0) {
            throw new InvalidArgumentException('Treinamento inválido.');
        }

        $prova = $this->provaModel->getProvaByTreinamento($idTreinamento);
        if (!$prova) {
            return ['success' => true, 'data' => null];
        }

        $questoes = $this->provaModel->getAllQuestion((int) $prova['id_prova']);

        return ['success' => true, 'data' => ['prova' => $prova, 'questoes' => $questoes]];
    }

    // ================= REGISTRAR RESULTADO (funcionário terminou a prova) =================

    /**
     * Chamada pelo prova.js do funcionário ao finalizar a prova.
     * Reprovado: só confirma o recebimento, não mexe em progresso/certificado.
     * Aprovado: gera o certificado sempre; só grava a conclusão em
     * funcionario_treinamento na PRIMEIRA aprovação (regra de progressão
     * do enunciado — refazer e passar de novo não conta progresso duas vezes).
     */
    public function registrarResultado(array $dados): array
    {
        $idProva = (int) ($dados['id_prova'] ?? 0);
        $idTreinamento = (int) ($dados['id_treinamento'] ?? 0);
        $idFuncionario = (int) ($dados['id_funcionario'] ?? 0);
        $nota = (float) ($dados['nota'] ?? 0);
        $aprovado = filter_var($dados['aprovado'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if ($idProva <= 0 || $idTreinamento <= 0 || $idFuncionario <= 0) {
            throw new InvalidArgumentException('Dados insuficientes para registrar o resultado da prova.');
        }
        if ($nota < 0 || $nota > 10) {
            throw new InvalidArgumentException('Nota inválida.');
        }

        if (!$aprovado) {
            return ['success' => true, 'aprovado' => false];
        }

        $primeiraAprovacao = !$this->provaModel->funcionarioJaConcluiuTreinamento($idFuncionario, $idTreinamento);

        if ($primeiraAprovacao) {
            $this->provaModel->registrarConclusaoTreinamento($idFuncionario, $idTreinamento);
        }

        $this->provaModel->registrarCertificado($idProva, $idFuncionario, $nota);

        return ['success' => true, 'aprovado' => true, 'primeira_aprovacao' => $primeiraAprovacao];
    }

    // ================= Helpers internos =================

    /**
     * Aceita id_prova direto, ou resolve pelo id_treinamento
     * (útil enquanto o front só guarda o id do treinamento em memória).
     */
    private function resolverIdProva(array $dados): int
    {
        if (!empty($dados['id_prova'])) {
            return (int) $dados['id_prova'];
        }

        $idTreinamento = (int) ($dados['id_treinamento'] ?? 0);
        if ($idTreinamento <= 0) {
            throw new InvalidArgumentException('Informe id_prova ou id_treinamento.');
        }

        $prova = $this->provaModel->getProvaByTreinamento($idTreinamento);
        if (!$prova) {
            throw new InvalidArgumentException('Nenhuma prova encontrada para esse treinamento.');
        }

        return (int) $prova['id_prova'];
    }

    private function validarQuestao(array $questao): void
    {
        $letra = $questao['alternativa'] ?? '';
        if (!in_array($letra, self::LETRAS_VALIDAS, true)) {
            throw new InvalidArgumentException('A alternativa correta deve ser a, b, c, d ou e.');
        }

        foreach (['enunciado', 'alt_a', 'alt_b', 'alt_c', 'alt_d', 'alt_e'] as $campo) {
            if (trim((string) ($questao[$campo] ?? '')) === '') {
                throw new InvalidArgumentException("Campo obrigatório da questão ausente: {$campo}.");
            }
        }
    }

    private function criarQuestaoParaProva(int $idProva, array $questao): int
    {
        $this->validarQuestao($questao);

        return $this->provaModel->createQuestion(
            $idProva,
            trim($questao['enunciado']),
            $questao['alternativa'],
            trim($questao['alt_a']),
            trim($questao['alt_b']),
            trim($questao['alt_c']),
            trim($questao['alt_d']),
            trim($questao['alt_e'])
        );
    }

    private function editarQuestaoDaProva(int $idQuestao, array $questao): void
    {
        $this->validarQuestao($questao);

        $this->provaModel->updateQuestion(
            $idQuestao,
            trim($questao['enunciado']),
            $questao['alternativa'],
            trim($questao['alt_a']),
            trim($questao['alt_b']),
            trim($questao['alt_c']),
            trim($questao['alt_d']),
            trim($questao['alt_e'])
        );
    }
}