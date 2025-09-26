<?php

use Funcoes\Layout\Layout as L;
use App\MOVIMENTACOES\DAO\Financeiro;
use App\MOVIMENTACOES\Datatables\DatatableFinanceiro;
use Funcoes\Layout\Form;
use Funcoes\Layout\FormControls as FC;
use Funcoes\Layout\Datatable;
use Funcoes\Layout\Table;
use Funcoes\Layout\ModalForm;

$financeiroDAO = new Financeiro();

$pageHeader = L::pageTitle('<h1 class="m-0 text-dark">Financeiro</h1>', L::linkButton('Novo Lançamento', '?posicao=form', '', 'fas fa-plus', 'primary'));

// Filtros
$form = new Form();
$form->setTitle('<i class="fas fa-filter"></i> Filtros');
$form->setForm('action="" method="GET"');
$form->setCollapsable(true);
$form->setCollapsed(false);
$form->setActions(L::submit('Filtrar', 'fas fa-filter'));
$form->setInternalPadding('pt-1 pb-1');
$form->addHidden(FC::hidden('novo', 'N'));

$filtro_tipo = FC::select('Tipo', 'fin_tipo', ['T' => 'Todos'] + $financeiroDAO->getTipo(), $request->get('fin_tipo', 'T'), [
    'class' => 'form-control form-control-sm',
    'div_class' => 'col-md-2'
]);

$filtro_fonte = FC::select('Fonte', 'fin_fonte', ['T' => 'Todas'] + $financeiroDAO->getFonte(), $request->get('fin_fonte', 'T'), [
    'class' => 'form-control form-control-sm',
    'div_class' => 'col-md-3'
]);

$filtro_descricao = FC::input('Descrição', 'fin_descricao', $request->get('fin_descricao', ''), [
    'class' => 'form-control form-control-sm',
    'div_class' => 'col-md-3'
]);

$filtro_periodo = FC::dateRange('Período', 'fin_data', $request->get('fin_data', date('01/m/Y') . ' - ' . date('d/m/Y')), [
    'class' => 'form-control form-control-sm',
    'div_class' => 'col-md-3'
]);

$form->setFields([
    ['<div class="row">' . $filtro_tipo . $filtro_fonte . $filtro_descricao . $filtro_periodo . '</div>']
]);

$table = new Datatable(DatatableFinanceiro::class);

if ($request->get('novo', 'S') == 'S') {
    $table->addFilters([
        'fin_data' => date('01/m/Y') . ' - ' . date('d/m/Y')
    ]);
}

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
        </script>
    HTML,
    ["title" => 'Financeiro']
);
