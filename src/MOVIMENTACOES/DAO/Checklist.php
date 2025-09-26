<?php

namespace App\MOVIMENTACOES\DAO;

use Funcoes\Lib\DAO;

class Checklist extends DAO
{
    private array $colunas = array(
        'chk_id',
        'chk_descricao',
        'chk_criado_em',
        'chk_marcado_em',
        'chk_usuario',
        'chk_marcado'
    );

    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
    }

    public function get($chk_id): array
    {
        $registros = $this->getArray([" AND chk_id = ?", [$chk_id]]);
        return $registros[0] ?? [];
    }

    private function buscarItens($marcados, $usuario)
    {
        $where = array('');
        $where[0] = ' AND chk_usuario = ? AND chk_marcado = ?';
        $where[1][] = $usuario;
        $where[1][] = $marcados;

        $aRegistros = $this->getArray($where, 'chk_id DESC');

        return $aRegistros;
    }

    public function getMarcados(string $usuario)
    {
        return $this->buscarItens('S', $usuario);
    }

    public function getDesmarcados(string $usuario)
    {
        return $this->buscarItens('N', $usuario);
    }

    public function baseQuery($where)
    {
        $campos = implode(', ', $this->colunas);

        $sql = "SELECT 
                {$campos} 
            FROM {$this->table('checklist')}
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
        [$sql, $args] = $this->preparedInsert($this->table('checklist'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public function update(string $chk_id, array $record): int
    {
        [$sql, $args] = $this->preparedUpdate($this->table('checklist'), $record);
        $sql .= " WHERE chk_id = ?";
        $args[] = $chk_id;

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }

    public function delete(int $chk_id): int
    {
        if ($chk_id == 0) {
            return 0;
        }

        $sql = "DELETE FROM {$this->table('checklist')} WHERE chk_id = ?";
        $stmt = $this->default->prepare($sql);
        $stmt->execute([$chk_id]);
        return $stmt->rowCount();
    }
}
