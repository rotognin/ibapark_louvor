<?php

namespace App\MOVIMENTACOES\Datatables;

use App\MOVIMENTACOES\DAO\Financeiro;
use Funcoes\Layout\Datatable;
use Funcoes\Lib\Datatables\Definitions;
use Funcoes\Layout\Layout as L;
use Funcoes\Helpers\Format;

class DatatableFinanceiro extends Definitions
{
    public function __construct($tableID = "")
    {
        parent::__construct($tableID);

        //Definição de filtros e valores padrão
        $this->filters = [
            'fin_tipo' => '',
            'fin_fonte' => '',
            'fin_descricao' => '',
            'fin_data' => ''
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
                ['name' => 'fin_id'],
                ['name' => 'fin_tipo'],
                ['name' => 'fin_data'],
                ['name' => 'fin_valor'],
                ['name' => 'fin_fonte'],
                ['name' => 'fin_descricao'],
                ['name' => 'acoes'],
            ],
            'order' => [[0, 'desc']],
            'columnDefs' => [
                ['targets' => [0, 1, 2, 4, 6], 'className' => 'text-center'],
                ['targets' => [3], 'className' => 'text-right'],
                ['targets' => [6], 'orderable' => false],
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
        $table->setAttrs(['id' => 'tabela-financeiro']);
        $table->setSize('sm');
        $table->setFooter(false);

        $table->addHeader([
            'cols' => [
                ['value' => '#', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Tipo', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Data', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Valor', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Fonte', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Descrição', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Ações', 'attrs' => ['class' => 'text-center']]
            ],
        ]);
    }

    public function getData($limit, $offset, $orderBy)
    {
        $financeiroDAO = new Financeiro();

        $where = ['', []];

        global $session;
        $usuario = $session->get('credentials.default');

        $where[0] .= ' AND fin_usuario = ?';
        $where[1][] = $usuario;

        if (!empty($this->filters['fin_tipo']) && $this->filters['fin_tipo'] != 'T') {
            $where[0] .= ' AND fin_tipo = ?';
            $where[1][] = $this->filters['fin_tipo'];
        }

        if (!empty($this->filters['fin_fonte']) && $this->filters['fin_fonte'] != 'T') {
            $where[0] .= ' AND fin_fonte = ?';
            $where[1][] = $this->filters['fin_fonte'];
        }

        if (!empty($this->filters['fin_descricao'])) {
            $where[0] .= ' AND fin_descricao LIKE ?';
            $where[1][] = '%' . $this->filters['fin_descricao'] . '%';
        }

        if (!empty($this->filters['fin_data']) && $this->filters['fin_data'] != '') {
            $aData = explode(' - ', $this->filters['fin_data']);
            $data_ini = Format::sqlDatetime($aData[0], 'd/m/Y', 'Y-m-d');
            $data_fim = Format::sqlDatetime($aData[1], 'd/m/Y', 'Y-m-d');

            $where[0] .= ' AND fin_data BETWEEN ? AND ?';
            $where[1][] = $data_ini;
            $where[1][] = $data_fim;
        }

        if ($limit == -1) {
            $limit = 0;
            $offset = 0;
        }

        $registros = $financeiroDAO->getArray($where, $orderBy ?? 'fin_id DESC', $limit, $offset);

        $data = [];
        $total = 0;
        $soma = 0;

        if (!empty($registros)) {
            $total = $registros[0]['total'] ?? count($registros);

            foreach ($registros as $reg) {
                $buttons = L::buttonGroup([
                    L::linkButton('', "?posicao=form&fin_id={$reg['fin_id']}", 'Editar Lançamento', 'fas fa-edit', 'outline-secondary', 'sm')
                ]);

                $data[] = array(
                    $reg['fin_id'],
                    $financeiroDAO->getTipo($reg['fin_tipo']),
                    Format::date($reg['fin_data']),
                    number_format($reg['fin_valor'], 2, ',', '.'),
                    $financeiroDAO->getFonte($reg['fin_fonte']),
                    $reg['fin_descricao'],
                    $buttons
                );

                if ($financeiroDAO->getTipo($reg['fin_tipo']) == 'Entrada') {
                    $soma += $reg['fin_valor'];
                } else {
                    $soma -= $reg['fin_valor'];
                }
            }
        }

        $data[] = array(
            '<b>Total</b>',
            '',
            '',
            '<b>' . number_format($soma, 2, ',', '.') . '</b>',
            '',
            '',
            ''
        );

        return [
            'data' => $data,
            'total' => $total,
        ];
    }
}
