<?php

namespace App\PESSOAL\DAO;

use Funcoes\Lib\DAO;

class Anotacoes extends DAO
{
    private array $colunas = array(
        'ano_id',
        'ano_usuario',
        'ano_titulo',
        'ano_texto',
        'ano_criada_em',
        'ano_alterada_em',
        'ano_excluida_em'
    );

    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
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
            WHERE ano_excluida_em IS NULL
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

    // soft-delete
    public function delete(int $ano_id): int
    {
        $record = array(
            'ano_excluida_em' => date('Y-m-d H:i:s')
        );

        return $this->update($ano_id, $record);
    }
}
