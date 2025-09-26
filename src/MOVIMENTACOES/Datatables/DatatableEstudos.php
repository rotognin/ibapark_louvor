<?php

namespace App\MOVIMENTACOES\Datatables;

use App\MOVIMENTACOES\DAO\Estudos;
use App\CADASTRO\DAO\Grupos;
use Funcoes\Layout\Datatable;
use Funcoes\Lib\Datatables\Definitions;
use Funcoes\Layout\Layout as L;
use Funcoes\Helpers\Format;

class DatatableEstudos extends Definitions
{
    public function __construct($tableID = "")
    {
        parent::__construct($tableID);

        //Definição de filtros e valores padrão
        $this->filters = [
            'bib_titulo_filtro' => '',
            'bib_referencia_filtro' => '',
            'bib_grupo_id_filtro' => ''
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
                ['name' => 'bib_id'],
                ['name' => 'bib_titulo'],
                ['name' => 'bib_referencia'],
                ['name' => 'bib_grupo_id'],
                ['name' => 'acoes'],
            ],
            'order' => [[0, 'asc']],
            'columnDefs' => [
                ['targets' => [0, 4], 'className' => 'text-center'],
                ['targets' => [4], 'orderable' => false],
            ],
            'fixedHeader' => true,
            'lengthMenu' => [[-1], ['Todos']],
            'infoCallback' => $script
        ]);

        //carregar os filtros a partir da requisição
        $this->loadFilters();
    }

    public function tableConfig(Datatable $table)
    {
        $table->setAttrs(['id' => 'tabela-estudos']);
        $table->setSize('sm');
        $table->setFooter(false);

        $table->addHeader([
            'cols' => [
                ['value' => '#', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Título', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Referência', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Grupo', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Ações', 'attrs' => ['class' => 'text-center']]
            ],
        ]);
    }

    public function getData($limit, $offset, $orderBy)
    {
        $estudosDAO = new Estudos();
        $gruposDAO = new Grupos();

        $where = ['', []];

        global $session;
        $usuario = $session->get('credentials.default');

        $where[0] .= ' AND bib_usuario = ?';
        $where[1][] = $usuario;

        if (!empty($this->filters['bib_titulo_filtro'])) {
            $where[0] .= ' AND bib_titulo LIKE ?';
            $where[1][] = '%' . $this->filters['bib_titulo_filtro'] . '%';
        }

        if (!empty($this->filters['bib_referencia_filtro'])) {
            $where[0] .= ' AND bib_referencia LIKE ?';
            $where[1][] = '%' . $this->filters['bib_referencia_filtro'] . '%';
        }

        if (!empty($this->filters['bib_grupo_id_filtro']) && $this->filters['bib_grupo_id_filtro'] > 0) {
            $where[0] .= ' AND bib_grupo_id = ?';
            $where[1][] = $this->filters['bib_grupo_id_filtro'];
        }

        if ($limit == -1) {
            $limit = 0;
            $offset = 0;
        }

        $registros = $estudosDAO->getArray($where, $orderBy ?? 'bib_id ASC', $limit, $offset);

        $data = [];
        $total = 0;

        if (!empty($registros)) {
            $total = $registros[0]['total'] ?? count($registros);

            foreach ($registros as $reg) {
                $buttons = L::buttonGroup([
                    L::button('', "editar({$reg['bib_id']})", 'Editar Estudo', 'fas fa-edit', 'outline-secondary', 'sm')
                ]);

                $data[] = array(
                    $reg['bib_id'],
                    $reg['bib_titulo'],
                    $reg['bib_referencia'],
                    $reg['gru_nome'],
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
