<?php

namespace App\CADASTRO\DAO;

use Funcoes\Lib\DAO;

class Integrantes extends DAO
{
    private array $colunas = array(
        'int_id',
        'int_nome',
        'int_observacoes',
        'int_usuario',
        'int_ativo',
        'int_criado_em',
        'int_criado_por',
        'int_alterado_em',
        'int_alterado_por',
        'int_excluido_em',
        'int_excluido_por'
    );

    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
    }

    public function get($int_id): array
    {
        $grupos = $this->getArray([" AND int_id = ?", [$int_id]]);
        return $grupos[0] ?? [];
    }

    public function baseQuery($where)
    {
        $campos = implode(', ', $this->colunas);

        $sql = "SELECT 
                {$campos} 
            FROM {$this->table('grupos_gerais')}
            WHERE int_excluido_em IS NOT NULL
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
        [$sql, $args] = $this->preparedInsert($this->table('grupos_gerais'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public function update(string $int_id, array $record): int
    {
        [$sql, $args] = $this->preparedUpdate($this->table('grupos_gerais'), $record);
        $sql .= " WHERE int_id = ?";
        $args[] = $int_id;

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }

    public function delete(int $int_id): int
    {
        global $session;
        $usuario = $session->get('credentials.default');

        $record = array(
            'int_excluido_em' => date('Y-m-d H:i:s'),
            'int_excluido_por' => $usuario
        );

        return $this->update($int_id, $record);
    }
}
