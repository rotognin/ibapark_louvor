<?php

namespace App\MOVIMENTACOES\DAO;

use Funcoes\Lib\DAO;

class Financeiro extends DAO
{
    private array $colunas = array(
        'fin_id',
        'fin_usuario',
        'fin_data',
        'fin_data_hora_cadastro',
        'fin_tipo',
        'fin_valor',
        'fin_descricao',
        'fin_fonte'
    );

    private array $fin_tipo = array(
        'E' => 'Entrada',
        'S' => 'Saída'
    );

    private array $fin_fonte = array(
        1 => 'Recargapay',
        2 => 'Poupança Caixa Tati',
        3 => 'Poupança Caixa Rodrigo',
        4 => 'Cta Conjunta Caixa',
        5 => 'Bradesco Tati',
        6 => 'Cartão Crédito Porto',
        7 => 'Cartão Crédito Nubank',
        8 => 'Em mãos',
        9 => 'Vale Alimentação Tati',
        10 => 'Banco Inter'
    );

    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
    }

    public function getTipo(string $tipo = ''): string|array
    {
        return ($tipo == '') ? $this->fin_tipo : $this->fin_tipo[$tipo];
    }

    public function getFonte(string $fonte = ''): string|array
    {
        return ($fonte == '') ? $this->fin_fonte : $this->fin_fonte[$fonte];
    }

    public function get($fin_id): array
    {
        $registros = $this->getArray([" AND fin_id = ?", [$fin_id]]);
        return $registros[0] ?? [];
    }

    public function baseQuery($where)
    {
        $campos = implode(', ', $this->colunas);

        $sql = "SELECT 
                {$campos} 
            FROM {$this->table('financeiro')}
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
        [$sql, $args] = $this->preparedInsert($this->table('financeiro'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public function update(string $fin_id, array $record): int
    {
        [$sql, $args] = $this->preparedUpdate($this->table('financeiro'), $record);
        $sql .= " WHERE fin_id = ?";
        $args[] = $fin_id;

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }

    public function delete(int $fin_id): int
    {
        if ($fin_id == 0) {
            return 0;
        }

        $sql = "DELETE FROM {$this->table('financeiro')} WHERE fin_id = ?";
        $stmt = $this->default->prepare($sql);
        $stmt->execute([$fin_id]);
        return $stmt->rowCount();
    }
}
