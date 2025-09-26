<?php

namespace App\MOVIMENTACOES\DAO;

use Funcoes\Lib\DAO;

class Estudos extends DAO
{
    private array $colunas = array(
        'bib_id',
        'bib_titulo',
        'bib_referencia',
        'bib_texto',
        'bib_grupo_id',
        'bib_usuario',
        'bib_data_hora'
    );

    public string $tipo_anexo = 'estudo_biblico';

    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
    }

    public function get($bib_id): array
    {
        $estudos = $this->getArray([" AND bib_id = ?", [$bib_id]]);
        return $estudos[0] ?? [];
    }

    public function baseQuery($where)
    {
        $campos = implode(', ', $this->colunas);

        $sql = "SELECT 
                {$campos}, g.gru_id, g.gru_nome
            FROM {$this->table('estudo_biblico')}
            LEFT JOIN {$this->table('grupos_gerais')} g
                ON bib_grupo_id = g.gru_id
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
        [$sql, $args] = $this->preparedInsert($this->table('estudo_biblico'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public function update(string $bib_id, array $record): int
    {
        [$sql, $args] = $this->preparedUpdate($this->table('estudo_biblico'), $record);
        $sql .= " WHERE bib_id = ?";
        $args[] = $bib_id;

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }

    public function delete(int $bib_id): int
    {
        if ($bib_id == 0) {
            return 0;
        }

        $sql = "DELETE FROM {$this->table('estudo_biblico')} WHERE bib_id = ?";
        $stmt = $this->default->prepare($sql);
        $stmt->execute([$bib_id]);
        return $stmt->rowCount();
    }
}
