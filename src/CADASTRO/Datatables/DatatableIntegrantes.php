<?php

namespace App\CADASTRO\Datatables;

use App\CADASTRO\DAO\Integrantes;
use Funcoes\Layout\Datatable;
use Funcoes\Lib\Datatables\Definitions;
use Funcoes\Layout\Layout as L;
use Funcoes\Helpers\Format;

class DatatableIntegrantes extends Definitions
{
    public function __construct($tableID = "")
    {
        parent::__construct($tableID);

        //Definição de filtros e valores padrão
        $this->filters = [];

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
                ['name' => 'int_id'],
                ['name' => 'int_nome'],
                ['name' => 'int_usuario'],
                ['name' => 'int_ativo'],
                ['name' => 'acoes'],
            ],
            'order' => [[2, 'asc']],
            'columnDefs' => [
                ['targets' => [0, 2, 3, 4], 'className' => 'text-center'],
                ['targets' => [4], 'orderable' => false],
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
        $table->setSize('sm');
        $table->setFooter(false);

        $table->addHeader([
            'cols' => [
                ['value' => '#', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Nome', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Usuário', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Situação', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Ações', 'attrs' => ['class' => 'text-center']]
            ],
        ]);
    }

    public function getData($limit, $offset, $orderBy)
    {
        $integrantesDAO = new Integrantes();

        $where = ['', []];

        if (!empty($this->filters['int_nome'])){
            $where[0] .= ' AND int_nome = ?';
            $where[1][] = $this->filters['int_nome'];
        }
        
        if ($limit == -1) {
            $limit = 0;
            $offset = 0;
        }

        $registros = $integrantesDAO->getArray($where, $orderBy ?? 'int_id ASC', $limit, $offset);

        $data = [];
        $total = 0;

        if (!empty($registros)) {
            $total = $registros[0]['total'] ?? count($registros);

            foreach ($registros as $reg) {
                $link = '?posicao=excluir&int_id=' . $reg['int_id'];

                $buttons = L::buttonGroup([
                    L::button('', 'visualizar(' . $reg['int_id'] . ')', 'Visualizar Informações', 'fas fa-file-alt', 'outline-primary', 'sm'),
                    L::linkButton('', "?posicao=form&int_id={$reg['int_id']}", 'Editar Integrante', 'fas fa-edit', 'outline-primary', 'sm'),
                    L::button('', "excluir('{$link}')", 'Excluir Integrante', 'fas fa-trash', 'outline-danger', 'sm')
                ]);

                $data[] = array(
                    $reg['int_id'],
                    $reg['int_nome'],
                    $reg['int_usuario'],
                    ($reg['int_ativo'] == 'S') ? 'Ativo' : 'Inativo',
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
