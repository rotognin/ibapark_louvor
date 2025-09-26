<?php

use Funcoes\Layout\Layout as L;
use App\CADASTRO\DAO\Anotacoes;
use App\CADASTRO\Datatables\DatatableAnotacoes;
use Funcoes\Layout\Form;
use Funcoes\Layout\FormControls as FC;
use Funcoes\Layout\Datatable;

$anotacoesDAO = new Anotacoes();

$pageHeader = L::pageTitle('<h1 class="m-0 text-dark">Anotações</h1>', L::linkButton('Nova Anotação', '?posicao=form', '', 'fas fa-plus', 'primary'));

// Filtros
$form = new Form();
$form->setTitle('<i class="fas fa-filter"></i> Filtros');
$form->setForm('action="" method="GET"');
$form->setCollapsable(true);
$form->setCollapsed(count($request->getArray()) == 0);
$form->setActions(L::submit('Filtrar', 'fas fa-filter'));
$form->setInternalPadding('pt-1 pb-1');

$filtro_titulo = FC::input(
    'Título',
    'ano_titulo',
    $request->get('ano_titulo', ''),
    [
        'class' => 'form-control form-control-sm',
        'div_class' => 'col-md-3'
    ]
);

$form->setFields([
    ['<div class="row">' . $filtro_titulo . '</div>']
]);

$table = new Datatable(DatatableAnotacoes::class);

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
            function excluirAnotacao(link){
                confirm('Tem certeza da exclusão da Anotação?').then(result => {
                    if (result.isConfirmed) {
                        window.location.href = link;
                    }
                });
            }
        </script>
    HTML,
    ["title" => 'Anotações']
);
