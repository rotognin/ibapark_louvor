<?php

namespace App\CADASTRO\DAO;

use Funcoes\Lib\DAO;

class Bancos extends DAO
{
    private array $colunas = array(
        'ban_id',
        'ban_descricao',
        'ban_sigla',
        'ban_tipo',
        'ban_especificacao',
        'ban_usuario',
        'ban_data_hora',
        'ban_status'
    );

    private array $status = array(
        'A' => 'Ativo',
        'I' => 'Inativo'
    );

    private array $tipo = array(
        1 => 'Banco',
        2 => 'Vale',
        3 => 'Crédito',
        4 => 'Em mãos'
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

    public function getTipo(string $tipo = ''): array|string
    {
        return ($tipo == '') ? $this->tipo : $this->tipo[$tipo];
    }

    public function get($ban_id): array
    {
        $bancos = $this->getArray([" AND ban_id = ?", [$ban_id]]);
        return $bancos[0] ?? [];
    }

    public function baseQuery($where)
    {
        $campos = implode(', ', $this->colunas);

        $sql = "SELECT 
                {$campos} 
            FROM {$this->table('bancos')}
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
        [$sql, $args] = $this->preparedInsert($this->table('bancos'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public function update(string $ban_id, array $record): int
    {
        [$sql, $args] = $this->preparedUpdate($this->table('bancos'), $record);
        $sql .= " WHERE ban_id = ?";
        $args[] = $ban_id;

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }

    public function delete(int $ban_id): int
    {
        if ($ban_id == 0) {
            return 0;
        }

        $sql = "DELETE FROM {$this->table('bancos')} WHERE ban_id = ?";
        $stmt = $this->default->prepare($sql);
        $stmt->execute([$ban_id]);
        return $stmt->rowCount();
    }
}
