<?php

use Funcoes\Layout\Layout as L;
use App\CADASTRO\DAO\Integrantes;
use App\CADASTRO\Datatables\DatatableIntegrantes;
use Funcoes\Layout\Form;
use Funcoes\Layout\FormControls as FC;
use Funcoes\Layout\Datatable;

$integrantesDAO = new Integrantes();

$pageHeader = L::pageTitle('<h1 class="m-0 text-dark">Integrantes</h1>', L::linkButton('Novo Integrante', '?posicao=form', '', 'fas fa-plus', 'primary'));

// Filtros
$form = new Form();
$form->setTitle('<i class="fas fa-filter"></i> Filtros');
$form->setForm('action="" method="GET"');
$form->setCollapsable(true);
$form->setCollapsed(count($request->getArray()) == 0);
$form->setActions(L::submit('Filtrar', 'fas fa-filter'));
$form->setInternalPadding('pt-1 pb-1');

$filtro_nome = FC::input(
    'Nome',
    'int_nome',
    $request->get('int_nome', ''),
    [
        'class' => 'form-control form-control-sm',
        'div_class' => 'col-md-5'
    ]
);

$form->setFields([
    ['<div class="row">' . $filtro_nome . '</div>']
]);

$table = new Datatable(DatatableIntegrantes::class, 'tabela-integrantes');

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
            function excluir(link){
                confirm('Tem certeza da exclusão do Integrante?').then(result => {
                    if (result.isConfirmed) {
                        window.location.href = link;
                    }
                });
            }
        </script>
    HTML,
    ["title" => 'Integrantes']
);
