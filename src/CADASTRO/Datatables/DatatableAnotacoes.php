<?php

namespace App\CADASTRO\Datatables;

use App\CADASTRO\DAO\Anotacoes;
use Funcoes\Layout\Datatable;
use Funcoes\Lib\Datatables\Definitions;
use Funcoes\Layout\Layout as L;
use Funcoes\Helpers\Format;

class DatatableAnotacoes extends Definitions
{
    public function __construct($tableID = "")
    {
        parent::__construct($tableID);

        //Definição de filtros e valores padrão
        $this->filters = [
            'ano_titulo' => ''
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
                ['name' => 'ano_id'],
                ['name' => 'ano_titulo'],
                ['name' => 'ano_data_hora'],
                ['name' => 'ano_usuario'],
                ['name' => 'ano_status'],
                ['name' => 'acoes'],
            ],
            'order' => [[1, 'asc']],
            'columnDefs' => [
                ['targets' => [0, 2, 3, 4, 5], 'className' => 'text-center'],
                ['targets' => [5], 'orderable' => false],
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
        $table->setAttrs(['id' => 'tabela-anotacoes']);
        $table->setSize('sm');
        $table->setFooter(false);

        $table->addHeader([
            'cols' => [
                ['value' => '#', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Título', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Data/Hora', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Usuário', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Status', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Ações', 'attrs' => ['class' => 'text-center']]
            ],
        ]);
    }

    public function getData($limit, $offset, $orderBy)
    {
        $anotacoesDAO = new Anotacoes();

        $where = ['', []];

        global $session;
        $usuario = $session->get('credentials.default');

        $where[0] .= ' AND ano_usuario = ?';
        $where[1][] = $usuario;

        if (!empty($this->filters['ano_titulo'])) {
            $where[0] .= ' AND ano_titulo LIKE ?';
            $where[1][] = '%' . $this->filters['ano_titulo'] . '%';
        }

        if ($limit == -1) {
            $limit = 0;
            $offset = 0;
        }

        $registros = $anotacoesDAO->getArray($where, $orderBy ?? 'ano_id ASC', $limit, $offset);

        $data = [];
        $total = 0;

        if (!empty($registros)) {
            $total = $registros[0]['total'] ?? count($registros);

            foreach ($registros as $reg) {
                $link = '?posicao=excluir&ano_id=' . $reg['ano_id'];

                $buttons = L::buttonGroup([
                    L::linkButton('', "?posicao=form&ano_id={$reg['ano_id']}", 'Editar Anotação', 'fas fa-edit', 'outline-secondary', 'sm'),
                    L::button('', "excluirAnotacao('{$link}')", 'Excluir Anotação', 'fas fa-trash', 'outline-danger', 'sm')
                ]);

                $data[] = array(
                    $reg['ano_id'],
                    $reg['ano_titulo'],
                    Format::datetime($reg['ano_data_hora']),
                    $reg['ano_usuario'],
                    $anotacoesDAO->getStatus($reg['ano_status']),
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
