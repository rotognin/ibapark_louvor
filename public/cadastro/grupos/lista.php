<?php

use Funcoes\Layout\Layout as L;
use App\CADASTRO\DAO\Grupos;
use App\CADASTRO\Datatables\DatatableGrupos;
use Funcoes\Layout\Form;
use Funcoes\Layout\FormControls as FC;
use Funcoes\Layout\Datatable;

$gruposDAO = new Grupos();

$pageHeader = L::pageTitle('<h1 class="m-0 text-dark">Grupos</h1>', L::linkButton('Novo Grupo', '?posicao=form', '', 'fas fa-plus', 'primary'));

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
    'gru_nome',
    $request->get('gru_nome', ''),
    [
        'class' => 'form-control form-control-sm',
        'div_class' => 'col-md-5'
    ]
);

$aTipos = ['0' => 'Todos'] + $gruposDAO->getTipo();

$filtro_tipo = FC::select('Tipo', 'gru_tipo', $aTipos, $request->get('gru_tipo', 0), [
    'class' => 'form-control form-control-sm',
    'div_class' => 'col-md-3'
]);

$form->setFields([
    ['<div class="row">' . $filtro_nome . $filtro_tipo . '</div>']
]);

$table = new Datatable(DatatableGrupos::class);

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
    HTML,
    ["title" => 'Grupos']
);
