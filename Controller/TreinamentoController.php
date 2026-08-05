<?php
namespace Controller;

require_once __DIR__ . "/../Model/Treinamento.php";

use Model\Treinamento;
use Exception;

class TreinamentoController{
    private $treinamentoModel;

    public function __construct(){
        $this->treinamentoModel = new Treinamento();
    }

    /**
     * Lê o formulário (nome, subtitulo, nr, carga_horaria, link_aulas, data_limite,
     * toggle "sem validade" e a imagem) e cria o treinamento.
     * Retorna sempre um array ['success' => bool, 'message' => string, 'id' => int?]
     */
    public function create(){
        $nome = trim(filter_input(INPUT_POST, 'nome_treinamento', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $subtitulo = trim(filter_input(INPUT_POST, 'subtitulo_treinamento', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $nr = trim(filter_input(INPUT_POST, 'nr_treinamento', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $carga_horaria = filter_input(INPUT_POST, 'carga_horaria_treinamento', FILTER_VALIDATE_INT);
        $link_aulas = trim(filter_input(INPUT_POST, 'link_aulas_treinamento', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $sem_validade = filter_input(INPUT_POST, 'sem_validade_treinamento', FILTER_VALIDATE_BOOLEAN);
        $data_limite = $sem_validade ? null : (filter_input(INPUT_POST, 'data_limite_treinamento', FILTER_SANITIZE_SPECIAL_CHARS) ?: null);

        if ($nome === '' || $subtitulo === '' || $nr === '' || !$carga_horaria || $link_aulas === '') {
            return ['success' => false, 'message' => 'Preencha todos os campos obrigatórios.'];
        }

        // Impede treinamentos duplicados pelo nome
        foreach ($this->treinamentoModel->getAllTreinamento() as $existente) {
            if (mb_strtolower($existente['nome_treinamento']) === mb_strtolower($nome)) {
                return ['success' => false, 'message' => 'Já existe um treinamento com esse título.'];
            }
        }

        $imagemValidada = $this->validarImagemUpload();
        if ($imagemValidada['erro']) {
            return ['success' => false, 'message' => $imagemValidada['erro']];
        }

        $id = $this->treinamentoModel->createTreinamento(
            $nome, $subtitulo, $nr, $carga_horaria, $link_aulas, $data_limite, $imagemValidada['conteudo']
        );

        if (!$id) {
            return ['success' => false, 'message' => 'Falha ao salvar o treinamento no banco de dados.'];
        }

        return ['success' => true, 'message' => 'Treinamento criado com sucesso!', 'id' => $id];
    }

    public function update(){
        $id = filter_input(INPUT_POST, 'id_treinamento', FILTER_VALIDATE_INT);
        $nome = trim(filter_input(INPUT_POST, 'nome_treinamento', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $subtitulo = trim(filter_input(INPUT_POST, 'subtitulo_treinamento', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $nr = trim(filter_input(INPUT_POST, 'nr_treinamento', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $carga_horaria = filter_input(INPUT_POST, 'carga_horaria_treinamento', FILTER_VALIDATE_INT);
        $link_aulas = trim(filter_input(INPUT_POST, 'link_aulas_treinamento', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $sem_validade = filter_input(INPUT_POST, 'sem_validade_treinamento', FILTER_VALIDATE_BOOLEAN);
        $data_limite = $sem_validade ? null : (filter_input(INPUT_POST, 'data_limite_treinamento', FILTER_SANITIZE_SPECIAL_CHARS) ?: null);

        if (!$id || $nome === '' || $subtitulo === '' || $nr === '' || !$carga_horaria || $link_aulas === '') {
            return ['success' => false, 'message' => 'Dados inválidos ou faltando. Verifique os campos.'];
        }

        if (!$this->treinamentoModel->getTreinamentoById($id)) {
            return ['success' => false, 'message' => 'Treinamento não encontrado.'];
        }

        $imagemValidada = $this->validarImagemUpload();
        if ($imagemValidada['erro']) {
            return ['success' => false, 'message' => $imagemValidada['erro']];
        }
        // null aqui significa "não mandou imagem nova" -> o Model mantém a imagem atual
        $imagem = $imagemValidada['conteudo'];

        try {
            $resultado = $this->treinamentoModel->updateTreinamento(
                $id, $nome, $subtitulo, $nr, $carga_horaria, $link_aulas, $data_limite, $imagem
            );

            if (!$resultado['success']) {
                return ['success' => false, 'message' => 'Erro ao atualizar o treinamento.'];
            }
            return ['success' => true, 'message' => 'Treinamento atualizado com sucesso!'];

        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erro inesperado no servidor: ' . $e->getMessage()];
        }
    }

    public function delete(){
        $id = filter_input(INPUT_POST, 'id_treinamento', FILTER_VALIDATE_INT);

        if (!$id) {
            return ['success' => false, 'message' => 'ID do treinamento inválido ou não fornecido.'];
        }

        try {
            $resultado = $this->treinamentoModel->deleteTreinamento($id);
            if (!$resultado['success']) {
                return ['success' => false, 'message' => $resultado['errors'][0] ?? 'Não foi possível excluir.'];
            }
            return ['success' => true, 'message' => 'Treinamento excluído com sucesso.'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erro inesperado no servidor: ' . $e->getMessage()];
        }
    }

    public function listAll(){
        return $this->treinamentoModel->getAllTreinamento();
    }

    public function findById($id){
        $cleanId = filter_var($id, FILTER_VALIDATE_INT);
        return $cleanId ? $this->treinamentoModel->getTreinamentoById($cleanId) : null;
    }

    public function listAllByNR($nr){
        $sanitizedNR = filter_var($nr, FILTER_SANITIZE_SPECIAL_CHARS);
        return $this->treinamentoModel->getTreinamentoByNR($sanitizedNR);
    }

    /**
     * Valida o arquivo de imagem enviado (se houver).
     * Retorna ['conteudo' => string|null, 'erro' => string|null].
     * conteudo = null quando o usuário simplesmente não anexou nenhum arquivo
     * (isso é normal ao editar sem trocar a foto).
     */
    private function validarImagemUpload(){
        if (!isset($_FILES['imagem_treinamento']) || $_FILES['imagem_treinamento']['error'] === UPLOAD_ERR_NO_FILE) {
            return ['conteudo' => null, 'erro' => null];
        }

        if ($_FILES['imagem_treinamento']['error'] !== UPLOAD_ERR_OK) {
            return ['conteudo' => null, 'erro' => 'Falha no upload da imagem.'];
        }

        $tmpPath = $_FILES['imagem_treinamento']['tmp_name'];
        $tiposPermitidos = ['image/jpeg', 'image/png', 'image/webp'];
        $mime = mime_content_type($tmpPath);

        if (!in_array($mime, $tiposPermitidos, true)) {
            return ['conteudo' => null, 'erro' => 'Formato de imagem inválido. Use JPG, PNG ou WEBP.'];
        }

        if ($_FILES['imagem_treinamento']['size'] > 5 * 1024 * 1024) {
            return ['conteudo' => null, 'erro' => 'A imagem deve ter no máximo 5MB.'];
        }

        return ['conteudo' => file_get_contents($tmpPath), 'erro' => null];
    }
}
