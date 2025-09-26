<?php

namespace App\CADASTRO\DAO;

use Funcoes\Lib\DAO;

class Livros extends DAO
{
    private array $colunas = array(
        'id',
        'nome',
        'abbrev',
        'testamento',
        'qtd_caps'
    );

    private array $testamento = array(
        1 => 'Antigo Testamento',
        2 => 'Novo Testamento'
    );

    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
    }

    public function getTipo(int $testamento = 0): array|string
    {
        return ($testamento == 0) ? $this->testamento : $this->testamento[$testamento];
    }

    public function get($liv_id): array
    {
        $livros = $this->getArray([" AND id = ?", [$liv_id]]);
        return $livros[0] ?? [];
    }

    public function getAbrev($abbrev)
    {
        $livros = $this->getArray([" AND abbrev = ?", [$abbrev]]);
        return $livros[0] ?? [];
    }

    public function baseQuery($where)
    {
        $campos = implode(', ', $this->colunas);

        $sql = "SELECT 
                {$campos} 
            FROM {$this->table('livros')}
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

    public function montarArray()
    {
        $livros = $this->getArray([], 'id ASC');
        $array = [];

        if (!empty($livros)) {
            foreach ($livros as $liv) {
                $array[$liv['id']] = $liv['nome'];
            }
        }

        return $array;
    }
}
