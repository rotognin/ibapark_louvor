<?php

use Funcoes\Layout\Layout as L;
use App\MOVIMENTACOES\DAO\Financeiro;
use Funcoes\Layout\Form;
use Funcoes\Layout\FormControls as FC;
use Funcoes\Helpers\Format;

$financeiroDAO = new Financeiro();

$fin_id = $request->get('fin_id', '');
$aFinanceiro = [];

if ($session->check('previous')) {
    $aFinanceiro = $session->get('previous');
}

if (!empty($fin_id)) {
    $aFinanceiro = $financeiroDAO->get($fin_id);
    if (empty($aFinanceiro)) {
        $session->flash('error', 'Registro não encontrado');
        return $response->back();
    }
}

$pageHeader = L::pageTitle(
    '<h1 class="m-0 text-dark">Registro Financeiro</h1>',
    L::backButton()
);

$form = new Form();
$form->setTitle($fin_id ? 'Editar Registro' . ": $fin_id - {$aFinanceiro['fin_descricao']}" : 'Novo Registro');
$form->setForm('id="form-financeiro" action="?posicao=salvar" method="post"');
if (!empty($fin_id)) {
    $form->addHidden(FC::hidden('fin_id', $fin_id));
    $form->addHidden(FC::hidden('alterar', 'S'));
}

$campo_tipo = FC::select('Tipo', 'fin_tipo', ['E' => 'Entrada', 'S' => 'Saída'], $aFinanceiro['fin_tipo'] ?? 'S', [
    'class' => 'form-control form-control-sm',
    'div_class' => 'col-md-2'
]);

$campo_descricao = FC::input('Descrição', 'fin_descricao', $aFinanceiro['fin_descricao'] ?? '', [
    'class' => 'form-control form-control-sm',
    'div_class' => 'col-md-7',
    'autofocus' => 'autofocus',
    'style' => 'text-transform: uppercase'
]);

$data = empty($aFinanceiro) ? date('d/m/Y') : Format::date($aFinanceiro['fin_data']);

$campo_data = FC::date(
    'Data',
    'fin_data',
    $data,
    [
        'class' => 'form-control form-control-sm',
        'div_class' => 'col-md-2'
    ]
);

$campo_valor = FC::input('Valor', 'fin_valor', $aFinanceiro['fin_valor'] ?? '', [
    'class' => 'form-control form-control-sm valor-mask',
    'div_class' => 'col-md-2'
]);

$campo_fonte = FC::select('Fonte', 'fin_fonte', $financeiroDAO->getFonte(), $aFinanceiro['fin_fonte'] ?? 1, [
    'class' => 'form-control form-control-sm',
    'div_class' => 'col-md-3'
]);

$aForm = array(
    ['<div class="row">' . $campo_descricao . $campo_tipo . '</div>'],
    ['<div class="row">' . $campo_data . $campo_valor . $campo_fonte . '</div>']
);

$form->setFields($aForm);
$form->setActions(L::submit('Salvar'));

$response->page(
    <<<HTML
    $pageHeader
    <div class="content pb-1">
        <div class="container-fluid pb-1">
            {$form->html()}
        </div>
    </div>
    <script>

    $('.valor-mask').mask('#,00', {reverse: true});

    $(function() {
        $('#form-financeiro').validate({
            rules: {
                fin_descricao: {
                    required: true
                }
            },
            messages: {
                fin_descricao: {
                    required: 'Informe a Descrição'
                }
            }
        });
    });   
    </script>
    HTML,
    ["title" => 'Registro Financeiro']
);
