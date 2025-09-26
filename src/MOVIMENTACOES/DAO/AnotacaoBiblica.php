<?php

namespace App\MOVIMENTACOES\DAO;

use Funcoes\Lib\DAO;

class AnotacaoBiblica extends DAO
{
    private array $colunas = array(
        'abi_id',
        'abi_usuario',
        'abi_livro',
        'abi_capitulo',
        'abi_gravado_em',
        'abi_anotacao'
    );

    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
    }

    public function get($abi_id): array
    {
        $registros = $this->getArray([" AND abi_id = ?", [$abi_id]]);
        return $registros[0] ?? [];
    }

    public function getAnotacao($livro, $capitulo)
    {
        global $session;
        $usuario = $session->get('credentials.default');

        $where = array('');
        $where[0] = ' AND abi_usuario = ? AND abi_livro = ? AND abi_capitulo = ?';
        $where[1][] = $usuario;
        $where[1][] = $livro;
        $where[1][] = $capitulo;

        $registro = $this->getArray($where);

        $anotacao = (empty($registro)) ? '' : $registro[0]['abi_anotacao'];
        return $anotacao;
    }

    public function baseQuery($where)
    {
        $campos = implode(', ', $this->colunas);

        $sql = "SELECT 
                {$campos} 
            FROM {$this->table('anotacao_biblica')}
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
        [$sql, $args] = $this->preparedInsert($this->table('anotacao_biblica'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public function update(string $abi_id, array $record): int
    {
        [$sql, $args] = $this->preparedUpdate($this->table('anotacao_biblica'), $record);
        $sql .= " WHERE abi_id = ?";
        $args[] = $abi_id;

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }

    public function delete(int $abi_id): int
    {
        if ($abi_id == 0) {
            return 0;
        }

        $sql = "DELETE FROM {$this->table('anotacao_biblica')} WHERE abi_id = ?";
        $stmt = $this->default->prepare($sql);
        $stmt->execute([$abi_id]);
        return $stmt->rowCount();
    }
}
