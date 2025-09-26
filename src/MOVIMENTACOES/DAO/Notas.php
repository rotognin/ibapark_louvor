<?php

namespace App\MOVIMENTACOES\DAO;

use Funcoes\Lib\DAO;

class Notas extends DAO
{
    private array $colunas = array(
        'not_id',
        'not_usuario',
        'not_id_pai',
        'not_titulo',
        'not_status',
        'not_texto'
    );

    private array $status = array(
        'A' => 'Ativo',
        'I' => 'Inativo'
    );

    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
    }

    public function getStatus(string $status = ''): string|array
    {
        return ($status == '') ? $this->status : $this->status[$status];
    }

    public function get($not_id): array
    {
        $registros = $this->getArray([" AND not_id = ?", [$not_id]]);
        return $registros[0] ?? [];
    }

    public function baseQuery($where)
    {
        $campos = implode(', ', $this->colunas);

        $sql = "SELECT 
                {$campos} 
            FROM {$this->table('notas')}
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

    /**
     * Buscar apenas os itens do topo da lista
     */
    public function buscarPais()
    {
        global $session;
        $usuario = $session->get('credentials.default');

        $where = array('');
        $where[0] = ' AND not_usuario = ? AND not_id_pai = ?';
        $where[1][] = $usuario;
        $where[1][] = 0;

        $aRegistros = $this->getArray($where, 'not_id');

        return $this->montarArray($aRegistros);
    }

    private function montarArray(array $aRegistros)
    {
        if (empty($aRegistros)) {
            return [];
        }

        $array = [];

        foreach ($aRegistros as $reg) {
            $array[$reg['not_id']] = $reg['not_titulo'];
        }

        return $array;
    }

    public function estrutura($nota_pai, &$aNotas, &$nivel, &$aFamilia)
    {
        global $session;
        $usuario = $session->get('credentials.default');

        $aRegistros = array();

        if ($nota_pai == '')
            $where = "not_id_pai = 0 ";
        else
            $where = "not_id_pai = $nota_pai ";

        $where .= " AND not_usuario = '$usuario'";

        $sql = "
            SELECT not_id, not_usuario, not_id_pai, not_titulo, not_status, not_texto
              FROM " . $this->table('notas') . "
              WHERE $where
              ORDER BY not_id";

        $stmt = $this->default->prepare($sql);
        $stmt->execute([]);
        $aRegistros = $stmt->fetchAll();
        $rows = count($aRegistros);

        $cont = 0;
        foreach ($aRegistros as $indice => $aDataSet) {
            $cont++;

            $id = $aDataSet["not_id"];
            $descricao = $aDataSet["not_titulo"];
            $id_pai = $aDataSet["not_id_pai"];

            $nivel++;

            $aNotas[] = array(
                'id'        => $id,
                'descricao' => $descricao,
                'id_pai'    => $id_pai,
                'texto'     => $aDataSet['not_texto'],
                'status'    => $aDataSet['not_status'],
                'nivel'     => $nivel,
                'ultimo'    => $cont == $rows ? true : false,
                'familia'   => $aFamilia
            );
            $aFamilia[$nivel] = $cont < $rows ? true : false;

            $this->estrutura($id, $aNotas, $nivel, $aFamilia);
        }

        unset($aFamilia[$nivel]);
        $nivel--;
    }

    public function insert(array $record): int
    {
        [$sql, $args] = $this->preparedInsert($this->table('notas'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public function update(string $not_id, array $record): int
    {
        [$sql, $args] = $this->preparedUpdate($this->table('notas'), $record);
        $sql .= " WHERE not_id = ?";
        $args[] = $not_id;

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }

    public function delete(int $not_id): int
    {
        if ($not_id == 0) {
            return 0;
        }

        $sql = "DELETE FROM {$this->table('notas')} WHERE not_id = ?";
        $stmt = $this->default->prepare($sql);
        $stmt->execute([$not_id]);
        return $stmt->rowCount();
    }
}
