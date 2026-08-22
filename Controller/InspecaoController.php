<?php

namespace Controller;

require_once __DIR__ . '/../Model/Inspecao.php';

use Model\Inspecao;

class InspecaoController
{
    private $inspecaoModel;
    private $tiposPermitidos = ['image/jpeg', 'image/png', 'image/webp'];
    private $tamanhoMaximoBytes = 5242880; // 5 MB

    public function __construct()
    {
        $this->inspecaoModel = new Inspecao();
    }

    public function realizarCheckin($id_funcionario, $epis_verificados)
    {
        if (!$id_funcionario) {
            http_response_code(401);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário não autenticado.']);
            return;
        }

        $fotoBinaria = $this->extrairFoto();
        if ($fotoBinaria === false) {
            http_response_code(400);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Envie uma foto válida (JPEG, PNG ou WEBP, até 5MB) para concluir o check-in.',
            ]);
            return;
        }

        try {
            $id_inspecao = $this->inspecaoModel->registrarCheckin(
                $id_funcionario,
                (int) $epis_verificados,
                'Concluído',
                $fotoBinaria
            );

            echo json_encode(['sucesso' => true, 'id_inspecao' => $id_inspecao]);
        } catch (\PDOException $e) {
            error_log('[Controller\InspecaoController::realizarCheckin] ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro interno ao registrar o check-in.']);
        }
    }

    public function buscarFoto($id_inspecao)
    {
        try {
            $foto = $this->inspecaoModel->buscarFoto($id_inspecao);
            if ($foto === null) {
                http_response_code(404);
                echo json_encode(['sucesso' => false, 'mensagem' => 'Foto não encontrada.']);
                return;
            }

            header('Content-Type: image/jpeg');
            header('Content-Length: ' . strlen($foto));
            echo $foto;
        } catch (\PDOException $e) {
            error_log('[Controller\InspecaoController::buscarFoto] ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro interno ao buscar a foto do check-in.']);
        }
    }

    // Aceita duas origens: upload via <input type="file"> (fallback) ou
    // foto capturada pela câmera do navegador, enviada como base64 no corpo JSON.
    private function extrairFoto()
    {
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            if ($_FILES['foto']['size'] > $this->tamanhoMaximoBytes) {
                return false;
            }
            $tipo = mime_content_type($_FILES['foto']['tmp_name']);
            if (!in_array($tipo, $this->tiposPermitidos, true)) {
                return false;
            }
            return file_get_contents($_FILES['foto']['tmp_name']);
        }

        $corpo = json_decode(file_get_contents('php://input'), true);
        if (isset($corpo['foto_base64']) && preg_match('/^data:image\/(jpeg|png|webp);base64,(.+)$/', $corpo['foto_base64'], $partes)) {
            $binario = base64_decode($partes[2], true);
            if ($binario === false || strlen($binario) > $this->tamanhoMaximoBytes) {
                return false;
            }
            return $binario;
        }

        return false;
    }
}
