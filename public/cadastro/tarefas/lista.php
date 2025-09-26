<?php

use Funcoes\Layout\Layout as L;
use App\CADASTRO\DAO\Tarefas;
use App\CADASTRO\Datatables\DatatableTarefas;
use Funcoes\Layout\Form;
use Funcoes\Layout\FormControls as FC;
use Funcoes\Layout\Datatable;

$tarefasDAO = new Tarefas();

$pageHeader = L::pageTitle('<h1 class="m-0 text-dark">Tarefas</h1>', L::linkButton('Nova Tarefas', '?posicao=form', '', 'fas fa-plus', 'primary'));

// Filtros
$form = new Form();
$form->setTitle('<i class="fas fa-filter"></i> Filtros');
$form->setForm('action="" method="GET"');
$form->setCollapsable(true);
$form->setCollapsed(count($request->getArray()) == 0);
$form->setActions(L::submit('Filtrar', 'fas fa-filter'));
$form->setInternalPadding('pt-1 pb-1');

$filtro_descricao = FC::input(
    'Descrição',
    'tar_descricao',
    $request->get('tar_descricao', ''),
    [
        'class' => 'form-control form-control-sm',
        'div_class' => 'col-md-3'
    ]
);

$filtro_status = FC::select('Status', 'tar_status', ['T' => 'Todas'] + $tarefasDAO->getStatus(), $request->get('tar_status', 'T'), [
    'class' => 'form-control form-control-sm',
    'div_class' => 'col-md-2'
]);

$form->setFields([
    ['<div class="row">' . $filtro_descricao . $filtro_status . '</div>']
]);

$table = new Datatable(DatatableTarefas::class);

$html = $table->html();

$response->page(
    <<<HTML
        {$pageHeader}
        <div class="content">
            <div class="container-fluid pb-1">
                {$form->html()}
                {$html}
            </div>
        </div>
        <script>
            function excluirTarefa(link){
                confirm('Tem certeza da exclusão da Tarefa?').then(result => {
                    if (result.isConfirmed) {
                        window.location.href = link;
                    }
                });
            }

            function avancar(link, msg){
                confirm(msg).then(result => {
                    if (result.isConfirmed) {
                        window.location.href = link;
                    }
                });
            }

            function aguardar(link, msg){
                confirm(msg).then(result => {
                    if (result.isConfirmed) {
                        window.location.href = link;
                    }
                });
            }

            function cancelar(link, msg){
                confirm(msg).then(result => {
                    if (result.isConfirmed) {
                        window.location.href = link;
                    }
                });
            }
        </script>
    HTML,
    ["title" => 'Tarefas']
);
