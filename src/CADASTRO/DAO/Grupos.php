<?php

namespace App\CADASTRO\DAO;

use Funcoes\Lib\DAO;

class Grupos extends DAO
{
    private array $colunas = array(
        'gru_id',
        'gru_nome',
        'gru_usuario',
        'gru_tipo'
    );

    private array $tipo = array(
        1 => 'Estudos Bíblicos',
        2 => 'Patrimônio',
        3 => 'Financeiro'
    );

    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
    }

    public function getTipo(string $tipo = ''): array|string
    {
        return ($tipo == '') ? $this->tipo : $this->tipo[$tipo];
    }

    public function get($gru_id): array
    {
        $grupos = $this->getArray([" AND gru_id = ?", [$gru_id]]);
        return $grupos[0] ?? [];
    }

    public function baseQuery($where)
    {
        $campos = implode(', ', $this->colunas);

        $sql = "SELECT 
                {$campos} 
            FROM {$this->table('grupos_gerais')}
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

    public function montarArray(array $where)
    {
        $grupos = $this->getArray($where);
        $array = [];

        if (!empty($grupos)) {
            foreach ($grupos as $gru) {
                $array[$gru['gru_id']] = $gru['gru_nome'];
            }
        }

        return $array;
    }

    public function insert(array $record): int
    {
        [$sql, $args] = $this->preparedInsert($this->table('grupos_gerais'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public function update(string $gru_id, array $record): int
    {
        [$sql, $args] = $this->preparedUpdate($this->table('grupos_gerais'), $record);
        $sql .= " WHERE gru_id = ?";
        $args[] = $gru_id;

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }

    public function delete(int $gru_id): int
    {
        if ($gru_id == 0) {
            return 0;
        }

        $sql = "DELETE FROM {$this->table('grupos_gerais')} WHERE gru_id = ?";
        $stmt = $this->default->prepare($sql);
        $stmt->execute([$gru_id]);
        return $stmt->rowCount();
    }
}
