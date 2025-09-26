<?php

namespace App\MOVIMENTACOES\DAO;

use Funcoes\Lib\DAO;

class Diario extends DAO
{
    private array $colunas = array(
        'dia_id',
        'dia_data',
        'dia_titulo',
        'dia_texto',
        'dia_usuario',
        'dia_data_hora'
    );

    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
    }

    public function get($dia_id): array
    {
        $diarios = $this->getArray([" AND dia_id = ?", [$dia_id]]);
        return $diarios[0] ?? [];
    }

    public function getDia($data): array
    {
        global $session;
        $usuario = $session->get('credentials.default');

        $aData = $this->getArray([" AND dia_data = ? AND dia_usuario = ?", [$data, $usuario]]);
        return $aData[0] ?? [];
    }

    public function baseQuery($where)
    {
        $campos = implode(', ', $this->colunas);

        $sql = "SELECT 
                {$campos} 
            FROM {$this->table('diario')}
            WHERE 1=1
        ";

        if ($where) {
            $sql .= "$where[0]";
        }
        return $sql;
    }

    public function getArray($where = [], $order = null, $limit = null, $offset = '0'): array
    {
        $query = $this->baseQuery($where);
        if ($limit) {
            $query = $this->paginate($query, $limit, $offset, $order);
        } else {
            if ($order) {
                $query .= " ORDER BY $order";
            }
        }

        $stmt = $this->default->prepare($query);
        $stmt->execute($where[1] ?? []);
        return $stmt->fetchAll();
    }

    public function insert(array $record): int
    {
        [$sql, $args] = $this->preparedInsert($this->table('diario'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public function update(string $dia_id, array $record): int
    {
        [$sql, $args] = $this->preparedUpdate($this->table('diario'), $record);
        $sql .= " WHERE dia_id = ?";
        $args[] = $dia_id;

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }

    public function delete(int $dia_id): int
    {
        if ($dia_id == 0) {
            return 0;
        }

        $sql = "DELETE FROM {$this->table('diario')} WHERE dia_id = ?";
        $stmt = $this->default->prepare($sql);
        $stmt->execute([$dia_id]);
        return $stmt->rowCount();
    }
}
