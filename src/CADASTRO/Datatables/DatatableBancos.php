<?php

namespace App\CADASTRO\Datatables;

use App\CADASTRO\DAO\Bancos;
use Funcoes\Layout\Datatable;
use Funcoes\Lib\Datatables\Definitions;
use Funcoes\Layout\Layout as L;
use Funcoes\Helpers\Format;

class DatatableBancos extends Definitions
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
                ['name' => 'ban_id'],
                ['name' => 'ban_descricao'],
                ['name' => 'ban_sigla'],
                ['name' => 'ban_tipo'],
                ['name' => 'ban_status'],
                ['name' => 'acoes'],
            ],
            'order' => [[2, 'asc']],
            'columnDefs' => [
                ['targets' => [0, 3, 4, 5], 'className' => 'text-center'],
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
        $table->setAttrs(['id' => 'tabela-bancos']);
        $table->setSize('sm');
        $table->setFooter(false);

        $table->addHeader([
            'cols' => [
                ['value' => '#', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Descrição', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Sigla', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Tipo', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Status', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Ações', 'attrs' => ['class' => 'text-center']]
            ],
        ]);
    }

    public function getData($limit, $offset, $orderBy)
    {
        $bancosDAO = new Bancos();

        $where = ['', []];

        global $session;
        $usuario = $session->get('credentials.default');

        $where[0] .= ' AND ban_usuario = ?';
        $where[1][] = $usuario;

        if ($limit == -1) {
            $limit = 0;
            $offset = 0;
        }

        $registros = $bancosDAO->getArray($where, $orderBy ?? 'ban_id ASC', $limit, $offset);

        $data = [];
        $total = 0;

        if (!empty($registros)) {
            $total = $registros[0]['total'] ?? count($registros);

            foreach ($registros as $reg) {
                $novo_status = ($reg['ban_status'] == 'A') ? 'Inativar' : 'Ativar';
                $status_alvo = ($reg['ban_status'] == 'A') ? 'I' : 'A';
                $icon_status = ($reg['ban_status'] == 'A') ? 'fas fa-eye-slash' : 'fas fa-eye';
                $tipo_status = ($reg['ban_status'] == 'A') ? 'outline-danger' : 'outline-success';
                $msg = ($reg['ban_status'] == 'A') ? 'Inativo' : 'Ativo';

                $link = '?posicao=alterarStatus&ban_id=' . $reg['ban_id'] . '&status_alvo=' . $status_alvo;

                $buttons = L::buttonGroup([
                    L::linkButton('', "?posicao=form&ban_id={$reg['ban_id']}", 'Editar Banco', 'fas fa-edit', 'outline-secondary', 'sm'),
                    L::button('', "alterarStatus('{$link}', '{$msg}')", $novo_status, $icon_status, $tipo_status, 'sm')
                ]);

                $data[] = array(
                    $reg['ban_id'],
                    $reg['ban_descricao'],
                    $reg['ban_sigla'],
                    $bancosDAO->getTipo($reg['ban_tipo']),
                    $bancosDAO->getStatus($reg['ban_status']),
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
