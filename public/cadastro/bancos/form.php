<?php

use Funcoes\Layout\Layout as L;
use App\CADASTRO\DAO\Bancos;
use Funcoes\Layout\Form;
use Funcoes\Helpers\Format;
use Funcoes\Layout\FormControls as FC;

$bancosDAO = new Bancos();

$ban_id = $request->get('ban_id', '');
$aBanco = [];

if ($session->check('previous')) {
    $aBanco = $session->get('previous');
}

if (!empty($ban_id)) {
    $aBanco = $bancosDAO->get($ban_id);
    if (empty($aBanco)) {
        $session->flash('error', 'Banco não encontrada');
        return $response->back();
    }
}

$pageHeader = L::pageTitle(
    '<h1 class="m-0 text-dark">Cadastro de Bancos</h1>',
    L::backButton()
);

$form = new Form();
$form->setTitle($ban_id ? 'Editar Banco' . ": $ban_id - {$aBanco['ban_sigla']}" : 'Novo Banco');
$form->setForm('id="form-banco" action="?posicao=salvar" method="post"');
if (!empty($ban_id)) {
    $form->addHidden(FC::hidden('ban_id', $ban_id));
    $form->addHidden(FC::hidden('alterar', 'S'));
}

$campo_descricao = FC::input(
    'Descrição',
    'ban_descricao',
    $aBanco['ban_descricao'] ?? '',
    ['class' => 'form-control form-control-sm', 'div_class' => 'col-md-6', 'autofocus' => 'autofocus', 'style' => 'text-transform: uppercase']
);

$campo_sigla = FC::input(
    'Sigla',
    'ban_sigla',
    $aBanco['ban_sigla'] ?? '',
    ['class' => 'form-control form-control-sm', 'div_class' => 'col-md-2', 'style' => 'text-transform: uppercase']
);

$campo_tipo = FC::select('Tipo', 'ban_tipo', $bancosDAO->getTipo(), $aBanco['ban_tipo'] ?? 1, [
    'class' => 'form-control form-control-sm',
    'div_class' => 'col-md-2'
]);

$campo_especificacao = FC::input(
    'Especificação',
    'ban_especificacao',
    $aBanco['ban_especificacao'] ?? '',
    ['class' => 'form-control form-control-sm', 'div_class' => 'col-md-4']
);

$aForm = array(
    ['<div class="row">' . $campo_descricao . $campo_sigla . '</div>'],
    ['<div class="row">' . $campo_tipo . $campo_especificacao . '</div>']
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
    $(function() {
        $('#form-banco').validate({
            onfocusout: false,
            onkeyup: false,
            onclick: false,
            onsubmit: true,
            rules: {
                ban_descricao: {
                    required: true
                },
                ban_sigla: {
                    required: true
                },
            },
            messages: {
                ban_descricao: {
                    required: 'Informe a Descrição'
                },
                ban_sigla: {
                    required: 'Informe a Sigla'
                }
            }
        });
    });   
    </script>
    HTML,
    ["title" => 'Cadastro de Bancos']
);
