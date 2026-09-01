<?php

namespace Model;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Connection.php';

use PDO;
use PDOException;
use Exception;
use Model\Connection;

class Foto {
    private $db;

    public function __construct() {
        $this->db = Connection::getInstance();
    }

    public function criarFoto(
        string $foto_foto,
        int $id_inspecao_fk
    ) :bool {
        try {
            $sql = 'INSERT INTO foto (
                foto_foto,
                id_inspecao_fk
            ) VALUES (
                :foto_foto,
                :id_inspecao_fk
            )';

            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':foto_foto' => $foto_foto,
                ':id_inspecao_fk' => $id_inspecao_fk
            ]);
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao criar foto',
                0,
                $e
            );
        }
    }
}