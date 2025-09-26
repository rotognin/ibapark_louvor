<?php

namespace App\CADASTRO\Datatables;

use App\CADASTRO\DAO\Tarefas;
use Funcoes\Layout\Datatable;
use Funcoes\Lib\Datatables\Definitions;
use Funcoes\Layout\Layout as L;
use Funcoes\Helpers\Format;

class DatatableTarefas extends Definitions
{
    public function __construct($tableID = "")
    {
        parent::__construct($tableID);

        //Definição de filtros e valores padrão
        $this->filters = [
            'tar_descricao' => '',
            'tar_status' => ''
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
                ['name' => 'tar_id'],
                ['name' => 'tar_descricao'],
                ['name' => 'tar_status'],
                ['name' => 'tar_usuario'],
                ['name' => 'tar_data'],
                ['name' => 'tar_data_limite'],
                ['name' => 'acoes'],
            ],
            'order' => [[2, 'asc']],
            'columnDefs' => [
                ['targets' => [0, 2, 3, 4, 5, 6], 'className' => 'text-center'],
                ['targets' => [6], 'orderable' => false],
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
        $table->setAttrs(['id' => 'tabela-tarefas']);
        $table->setSize('sm');
        $table->setFooter(false);

        $table->addHeader([
            'cols' => [
                ['value' => '#', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Descrição', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Status', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Usuário', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Data', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Data Limite', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Ações', 'attrs' => ['class' => 'text-center']]
            ],
        ]);
    }

    public function getData($limit, $offset, $orderBy)
    {
        $tarefasDAO = new Tarefas();

        $where = ['', []];

        global $session;
        $usuario = $session->get('credentials.default');

        $where[0] .= ' AND tar_usuario = ?';
        $where[1][] = $usuario;

        if (!empty($this->filters['tar_descricao'])) {
            $where[0] .= ' AND tar_descricao LIKE ?';
            $where[1][] = '%' . $this->filters['tar_descricao'] . '%';
        }

        if (!empty($this->filters['tar_status']) && $this->filters['tar_status'] != 'T') {
            $where[0] .= ' AND tar_status = ?';
            $where[1][] = $this->filters['tar_status'];
        }

        if ($limit == -1) {
            $limit = 0;
            $offset = 0;
        }

        $registros = $tarefasDAO->getArray($where, $orderBy ?? 'tar_id DESC', $limit, $offset);

        $data = [];
        $total = 0;

        if (!empty($registros)) {
            $total = $registros[0]['total'] ?? count($registros);

            foreach ($registros as $reg) {
                $avancar = '';
                $attrs_avancar = '';
                $attrs_aguardar = 'disabled';
                $attrs_cancelar = 'disabled';
                $attrs_excluir = 'disabled';
                $attrs_editar = '';
                $status_alvo = 2;

                switch ($reg['tar_status']) {
                    case 1: // A fazer
                        $avancar = 'Fazendo';
                        break;
                    case 2: // Fazendo
                        $avancar = 'Finalizado';
                        $status_alvo = 4;
                        break;
                    case 3: // Aguardando
                        $avancar = 'Finalizado';
                        $status_alvo = 4;
                        break;
                    case 4: // Finalizado
                        $attrs_avancar = 'disabled';
                        break;
                    case 5: // Cancelado
                        $attrs_avancar = 'disabled';
                        break;
                }

                $link = '?posicao=excluir&tar_id=' . $reg['tar_id'];
                $link_avancar = '?posicao=avancar&tar_id=' . $reg['tar_id'] . '&status_alvo=' . $status_alvo;
                $link_aguardar = '';
                $link_cancelar = '';

                if ($reg['tar_status'] == 2) {
                    $link_aguardar = '?posicao=avancar&tar_id=' . $reg['tar_id'] . '&status_alvo=3';
                    $attrs_aguardar = '';
                }

                if (in_array($reg['tar_status'], [1, 2, 3])) {
                    $link_cancelar = '?posicao=avancar&tar_id=' . $reg['tar_id'] . '&status_alvo=5';
                    $attrs_cancelar = '';
                }

                if (in_array($reg['tar_status'], [1, 2, 3])) {
                    $attrs_excluir = '';
                }

                if ($reg['tar_status'] > 1) {
                    $attrs_editar = 'disabled';
                }

                $buttons = L::buttonGroup([
                    L::linkButton('', "?posicao=form&tar_id={$reg['tar_id']}", 'Editar Tarefa', 'fas fa-edit', 'outline-secondary', 'sm', $attrs_editar),
                    L::button('', "avancar('{$link_avancar}', 'Avançar Tarefa para {$avancar}?')", 'Avançar para ' . $avancar, 'fas fa-play', 'outline-success', 'sm', $attrs_avancar),
                    L::button('', "aguardar('{$link_aguardar}', 'Colocar Tarefa em Aguardando')", 'Colocar em Aguardando', 'fas fa-clock', 'outline-info', 'sm', $attrs_aguardar),
                    L::button('', "cancelar('{$link_cancelar}', 'Cancelar Tarefa')", 'Cancelar Tarefa', 'fas fa-ban', 'outline-danger', 'sm', $attrs_cancelar),
                    L::button('', "excluirTarefa('{$link}')", 'Excluir Tarefa', 'fas fa-trash', 'outline-danger', 'sm', $attrs_excluir)
                ]);

                $data[] = array(
                    $reg['tar_id'],
                    $reg['tar_descricao'],
                    $tarefasDAO->getStatus($reg['tar_status']),
                    $reg['tar_usuario'],
                    Format::date($reg['tar_data']),
                    Format::date($reg['tar_data_limite']),
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
