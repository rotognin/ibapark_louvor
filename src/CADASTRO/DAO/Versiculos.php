<?php

namespace App\CADASTRO\DAO;

use Funcoes\Lib\DAO;

class Versiculos extends DAO
{
    private array $colunas = array(
        'ver_id',
        'ver_livro',
        'ver_capitulo',
        'ver_versiculo',
        'ver_texto'
    );

    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
    }

    public function get($ver_id): array
    {
        $versiculos = $this->getArray([" AND ver_id = ?", [$ver_id]]);
        return $versiculos[0] ?? [];
    }

    public function baseQuery($where)
    {
        $campos = implode(', ', $this->colunas);

        $sql = "SELECT 
                {$campos} 
            FROM {$this->table('versiculos')}
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
        $versiculos = $this->getArray($where);
        $array = [];

        if (!empty($versiculos)) {
            foreach ($versiculos as $ver) {
                $array[$ver['ver_id']] = $ver['ver_texto'];
            }
        }

        return $array;
    }

    /*
    public function insert(array $record): int
    {
        [$sql, $args] = $this->preparedInsert($this->table('versiculos'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public function update(string $ver_id, array $record): int
    {
        [$sql, $args] = $this->preparedUpdate($this->table('versiculos'), $record);
        $sql .= " WHERE ver_id = ?";
        $args[] = $ver_id;

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }

    public function delete(int $ver_id): int
    {
        if ($ver_id == 0) {
            return 0;
        }

        $sql = "DELETE FROM {$this->table('versiculos')} WHERE ver_id = ?";
        $stmt = $this->default->prepare($sql);
        $stmt->execute([$ver_id]);
        return $stmt->rowCount();
    }
    */
}
