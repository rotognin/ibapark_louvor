<?php

namespace App\CADASTRO\DAO;

use Funcoes\Lib\DAO;

class Anotacoes extends DAO
{
    private array $colunas = array(
        'ano_id',
        'ano_titulo',
        'ano_texto',
        'ano_data_hora',
        'ano_usuario',
        'ano_status'
    );

    private array $status = array(
        'A' => 'Ativa',
        'I' => 'Inativa'
    );

    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
    }

    public function getStatus(string $status = ''): array|string
    {
        return ($status == '') ? $this->status : $this->status[$status];
    }

    public function get($ano_id): array
    {
        $anotacoes = $this->getArray([" AND ano_id = ?", [$ano_id]]);
        return $anotacoes[0] ?? [];
    }

    public function baseQuery($where)
    {
        $campos = implode(', ', $this->colunas);

        $sql = "SELECT 
                {$campos} 
            FROM {$this->table('anotacoes')}
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
        [$sql, $args] = $this->preparedInsert($this->table('anotacoes'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public function update(string $ano_id, array $record): int
    {
        [$sql, $args] = $this->preparedUpdate($this->table('anotacoes'), $record);
        $sql .= " WHERE ano_id = ?";
        $args[] = $ano_id;

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }

    public function delete(int $ano_id): int
    {
        if ($ano_id == 0) {
            return 0;
        }

        $sql = "DELETE FROM {$this->table('anotacoes')} WHERE ano_id = ?";
        $stmt = $this->default->prepare($sql);
        $stmt->execute([$ano_id]);
        return $stmt->rowCount();
    }
}
