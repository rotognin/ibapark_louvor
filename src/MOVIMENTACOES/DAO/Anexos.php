<?php

namespace App\MOVIMENTACOES\DAO;

use Funcoes\Lib\DAO;

class Anexos extends DAO
{
    private array $colunas = array(
        'anx_id',
        'anx_entidade',
        'anx_entidade_id',
        'anx_tipo',
        'anx_nome_arquivo',
        'anx_fisico_arquivo',
        'anx_caminho',
        'anx_descricao',
        'anx_data_hora',
        'anx_usuario'
    );

    private array $tipo = array(
        1 => 'Imagem',
        2 => 'PDF',
        3 => 'Texto',
        4 => 'Documento',
        5 => 'Planilha',
        6 => 'Outro'
    );

    private array $tipo_extensao = array(
        'jpg;png;bmp;jpeg' => 'Imagem',
        'pdf' => 'PDF',
        'txt' => 'Texto',
        'doc;docx' => 'Documento',
        'xls;xlsx' => 'Planilha',
        'ppt;pptx' => 'Outro'
    );

    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
    }

    public function getTipo(int $tipo = 0): array|string
    {
        return ($tipo == 0) ? $this->tipo : $this->tipo[$tipo];
    }

    public function encontrarTipo(string $extensao): int
    {
        $extensao = strtolower($extensao);
        $num_ext = 0;

        foreach ($this->tipo_extensao as $chv => $tipo) {
            if (str_contains($chv, $extensao)) {
                $num_ext = array_search($tipo, $this->tipo);
                if (!$num_ext) {
                    $num_ext = array_search('Outro', $this->tipo);
                }
            }
        }

        return $num_ext;
    }

    public function get($anx_id): array
    {
        $anexos = $this->getArray([" AND anx_id = ?", [$anx_id]]);
        return $anexos[0] ?? [];
    }

    public function baseQuery($where)
    {
        $campos = implode(', ', $this->colunas);

        $sql = "SELECT 
                {$campos} 
            FROM {$this->table('anexos')}
            WHERE 1=1
        ";

        if ($where) {
            $sql .= "$where[0]";
        }
        return $sql;
    }

    public function getArray($where = [], $order = ' anx_id ASC ', $limit = null, $offset = '0'): array
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
        [$sql, $args] = $this->preparedInsert($this->table('anexos'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public function update(string $anx_id, array $record): int
    {
        [$sql, $args] = $this->preparedUpdate($this->table('anexos'), $record);
        $sql .= " WHERE anx_id = ?";
        $args[] = $anx_id;

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }

    public function delete(int $anx_id): int
    {
        if ($anx_id == 0) {
            return 0;
        }

        $sql = "DELETE FROM {$this->table('anexos')} WHERE anx_id = ?";
        $stmt = $this->default->prepare($sql);
        $stmt->execute([$anx_id]);
        return $stmt->rowCount();
    }
}
