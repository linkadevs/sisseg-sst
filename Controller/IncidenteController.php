<?php
namespace Controller;

require_once __DIR__ . "/../Model/Incidente.php";

use Model\Incidente;
use Exception;

class IncidenteController{
    private $incidenteModel;

    public function __construct(){
        $this->incidenteModel = new Incidente();
    }

    public function create(){
        $dados = $this->lerDadosFormulario();

        if (!$dados['valido']) {
            return ['success' => false, 'message' => 'Preencha todos os campos obrigatórios.'];
        }

        $fotoValidada = $this->validarFotoUpload();
        if ($fotoValidada['erro']) {
            return ['success' => false, 'message' => $fotoValidada['erro']];
        }

        $resultado = $this->incidenteModel->createIncidente(
            $dados['data'], $dados['tipo'], $dados['gravidade'], $dados['local'], $dados['atividade'],
            $dados['descricao'], $dados['testemunhas'], $dados['acao'], $dados['treinamento'],
            $fotoValidada['conteudo'] ?? ''
        );

        if (!$resultado['success']) {
            return ['success' => false, 'message' => 'Falha ao salvar o incidente no banco de dados.'];
        }

        return [
            'success' => true,
            'message' => "Incidente {$resultado['codigo']} registrado com sucesso!",
            'id' => $resultado['id'],
            'codigo' => $resultado['codigo']
        ];
    }

    public function update(){
        $id = filter_input(INPUT_POST, 'id_incidente', FILTER_VALIDATE_INT);
        $dados = $this->lerDadosFormulario();
        $status = trim(filter_input(INPUT_POST, 'status', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        if ($status === '' || !in_array($status, Incidente::STATUS_VALIDOS, true)) {
            $status = 'Aberto';
        }

        if (!$id || !$dados['valido']) {
            return ['success' => false, 'message' => 'Dados inválidos ou faltando. Verifique os campos.'];
        }

        if (!$this->incidenteModel->getById($id)) {
            return ['success' => false, 'message' => 'Incidente não encontrado.'];
        }

        $fotoValidada = $this->validarFotoUpload();
        if ($fotoValidada['erro']) {
            return ['success' => false, 'message' => $fotoValidada['erro']];
        }

        try {
            $ok = $this->incidenteModel->updateIncidente(
                $id, $dados['data'], $dados['tipo'], $status, $dados['local'], $dados['atividade'],
                $dados['descricao'], $dados['testemunhas'], $dados['acao'], $dados['gravidade'],
                $dados['treinamento'], $fotoValidada['conteudo']
            );

            if (!$ok) {
                return ['success' => false, 'message' => 'Erro ao atualizar o incidente.'];
            }
            return ['success' => true, 'message' => 'Incidente atualizado com sucesso!'];

        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erro inesperado no servidor: ' . $e->getMessage()];
        }
    }

    /** Troca rápida de status (usada pelo dropdown de status no card) */
    public function updateStatus(){
        $id = filter_input(INPUT_POST, 'id_incidente', FILTER_VALIDATE_INT);
        $status = trim(filter_input(INPUT_POST, 'status', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');

        if (!$id || !in_array($status, Incidente::STATUS_VALIDOS, true)) {
            return ['success' => false, 'message' => 'Status inválido.'];
        }

        $ok = $this->incidenteModel->updateStatus($id, $status);
        return $ok
            ? ['success' => true, 'message' => 'Status atualizado com sucesso!']
            : ['success' => false, 'message' => 'Erro ao atualizar status.'];
    }

    public function delete(){
        $id = filter_input(INPUT_POST, 'id_incidente', FILTER_VALIDATE_INT);

        if (!$id) {
            return ['success' => false, 'message' => 'ID do incidente inválido ou não fornecido.'];
        }

        try {
            $ok = $this->incidenteModel->deleteIncidente($id);
            return $ok
                ? ['success' => true, 'message' => 'Incidente excluído com sucesso.']
                : ['success' => false, 'message' => 'Não foi possível excluir.'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erro inesperado no servidor: ' . $e->getMessage()];
        }
    }

    /** $status: null|'todos'|'Aberto'|'Investigando'|'Concluído' */
    public function listAll($status = null, $busca = null){
        $statusLimpo = $status ? filter_var($status, FILTER_SANITIZE_SPECIAL_CHARS) : null;
        $buscaLimpa = $busca ? filter_var($busca, FILTER_SANITIZE_SPECIAL_CHARS) : null;
        return $this->incidenteModel->getAllFiltered($statusLimpo, $buscaLimpa);
    }

    public function findById($id){
        $cleanId = filter_var($id, FILTER_VALIDATE_INT);
        return $cleanId ? $this->incidenteModel->getById($cleanId) : null;
    }

    public function getCounts(){
        return $this->incidenteModel->getCounts();
    }

    private function lerDadosFormulario(){
        $data        = trim(filter_input(INPUT_POST, 'data', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $tipo        = trim(filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $gravidade   = trim(filter_input(INPUT_POST, 'gravidade', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $local       = trim(filter_input(INPUT_POST, 'local', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $atividade   = trim(filter_input(INPUT_POST, 'atividade', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $descricao   = trim(filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $testemunhas = trim(filter_input(INPUT_POST, 'testemunhas', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $acao        = trim(filter_input(INPUT_POST, 'acaoImediata', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $treinamento = trim(filter_input(INPUT_POST, 'treinamento', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');

        $valido = $data !== '' && $tipo !== '' && $gravidade !== '' && $local !== ''
                  && $atividade !== '' && $descricao !== '' && $acao !== '';

        return compact('data', 'tipo', 'gravidade', 'local', 'atividade', 'descricao', 'testemunhas', 'acao', 'treinamento', 'valido');
    }

    /**
     * Valida o upload de foto (campo "fotos[]", aceita múltiplos no HTML).
     * Por ora, como `fotos_incidente` no banco é um único mediumblob,
     * guardamos apenas a primeira imagem enviada. Se quiser guardar
     * várias, o caminho é criar uma tabela `incidente_foto` separada.
     * Retorna ['conteudo' => string|null, 'erro' => string|null].
     * conteudo = null quando não veio nenhum arquivo novo.
     */
    private function validarFotoUpload(){
        if (!isset($_FILES['fotos']) || !isset($_FILES['fotos']['error'][0]) || $_FILES['fotos']['error'][0] === UPLOAD_ERR_NO_FILE) {
            return ['conteudo' => null, 'erro' => null];
        }

        $i = 0;
        if ($_FILES['fotos']['error'][$i] !== UPLOAD_ERR_OK) {
            return ['conteudo' => null, 'erro' => 'Falha no upload da foto.'];
        }

        $tmpPath = $_FILES['fotos']['tmp_name'][$i];
        $tiposPermitidos = ['image/jpeg', 'image/png', 'image/webp'];
        $mime = mime_content_type($tmpPath);

        if (!in_array($mime, $tiposPermitidos, true)) {
            return ['conteudo' => null, 'erro' => 'Formato de imagem inválido. Use JPG, PNG ou WEBP.'];
        }

        if ($_FILES['fotos']['size'][$i] > 5 * 1024 * 1024) {
            return ['conteudo' => null, 'erro' => 'Cada imagem deve ter no máximo 5MB.'];
        }

        return ['conteudo' => file_get_contents($tmpPath), 'erro' => null];
    }

    public function selecionarTodosOsIncidentes() :array {
        try {
            return $this->incidenteModel->SelecionarTodosOsIncidentes();
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao selecionar todos os incidentes',
                0,
                $e
            );
        }
    }
}