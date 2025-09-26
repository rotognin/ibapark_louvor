<?php

namespace App\CADASTRO\DAO;

use Funcoes\Lib\DAO;

class Tarefas extends DAO
{
    private array $colunas = array(
        'tar_id',
        'tar_descricao',
        'tar_status',
        'tar_usuario',
        'tar_data',
        'tar_data_limite'
    );

    private array $status = array(
        '1' => 'A fazer',
        '2' => 'Fazendo',
        '3' => 'Aguardando',
        '4' => 'Finalizado',
        '5' => 'Cancelado'
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

    public function get($tar_id): array
    {
        $tarefas = $this->getArray([" AND tar_id = ?", [$tar_id]]);
        return $tarefas[0] ?? [];
    }

    public function baseQuery($where)
    {
        $campos = implode(', ', $this->colunas);

        $sql = "SELECT 
                {$campos} 
            FROM {$this->table('tarefas')}
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
        [$sql, $args] = $this->preparedInsert($this->table('tarefas'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public function update(string $tar_id, array $record): int
    {
        [$sql, $args] = $this->preparedUpdate($this->table('tarefas'), $record);
        $sql .= " WHERE tar_id = ?";
        $args[] = $tar_id;

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }

    public function delete(int $tar_id): int
    {
        if ($tar_id == 0) {
            return 0;
        }

        $sql = "DELETE FROM {$this->table('tarefas')} WHERE tar_id = ?";
        $stmt = $this->default->prepare($sql);
        $stmt->execute([$tar_id]);
        return $stmt->rowCount();
    }
}
