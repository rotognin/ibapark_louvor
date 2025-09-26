<?php

namespace App\CADASTRO\Datatables;

use App\CADASTRO\DAO\Grupos;
use Funcoes\Layout\Datatable;
use Funcoes\Lib\Datatables\Definitions;
use Funcoes\Layout\Layout as L;
use Funcoes\Helpers\Format;

class DatatableGrupos extends Definitions
{
    public function __construct($tableID = "")
    {
        parent::__construct($tableID);

        //Definição de filtros e valores padrão
        $this->filters = [
            'gru_nome' => '',
            'gru_tipo' => ''
        ];

        $script = <<<'SCRIPT'
            function(settings, start, end, max, total, pre) {
                if (max == -1) {
                    return "Mostrando todos os registros. Total de " + total + " registros.";
                } else {
                    return pre;
                }
            }
        SCRIPT;

        //Definições das opções do datatable dando merge com as opções padrão
        $this->setOptions([
            'columns' => [
                ['name' => 'gru_id'],
                ['name' => 'gru_nome'],
                ['name' => 'gru_tipo'],
                ['name' => 'acoes'],
            ],
            'order' => [[1, 'asc']],
            'columnDefs' => [
                ['targets' => [0, 3], 'className' => 'text-center'],
                ['targets' => [3], 'orderable' => false],
            ],
            'fixedHeader' => true,
            'lengthMenu' => [[10, 50, 100, -1], [10, 50, 100, 'Todos']],
            'infoCallback' => $script
        ]);

        //carregar os filtros a partir da requisição
        $this->loadFilters();
    }

    public function tableConfig(Datatable $table)
    {
        $table->setAttrs(['id' => 'tabela-bancos']);
        $table->setSize('sm');
        $table->setFooter(false);

        $table->addHeader([
            'cols' => [
                ['value' => '#', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Grupo', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Tipo', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Ações', 'attrs' => ['class' => 'text-center']]
            ],
        ]);
    }

    public function getData($limit, $offset, $orderBy)
    {
        $gruposDAO = new Grupos();

        $where = ['', []];
        global $session;
        $usuario = $session->get('credentials.default');

        $where[0] .= ' AND gru_usuario = ?';
        $where[1][] = $usuario;

        if ($this->filters['gru_nome'] != '') {
            $where[0] .= ' AND gru_nome LIKE ?';
            $where[1][] = '%' . $this->filters['gru_nome'] . '%';
        }

        if ($this->filters['gru_tipo'] > 0) {
            $where[0] .= 'AND gru_tipo = ?';
            $where[1][] = $this->filters['gru_tipo'];
        }

        if ($limit == -1) {
            $limit = 0;
            $offset = 0;
        }

        $registros = $gruposDAO->getArray($where, $orderBy ?? 'gru_id ASC', $limit, $offset);

        $data = [];
        $total = 0;

        if (!empty($registros)) {
            $total = $registros[0]['total'] ?? count($registros);

            foreach ($registros as $reg) {
                $buttons = L::buttonGroup([
                    L::linkButton('', "?posicao=form&gru_id={$reg['gru_id']}", 'Editar Grupo', 'fas fa-edit', 'outline-secondary', 'sm')
                ]);

                $data[] = array(
                    $reg['gru_id'],
                    $reg['gru_nome'],
                    $gruposDAO->getTipo($reg['gru_tipo']),
                    $buttons
                );
            }
        }

        return [
            'data' => $data,
            'total' => $total,
        ];
    }
}
