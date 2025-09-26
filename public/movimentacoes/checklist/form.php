<?php

use Funcoes\Layout\Layout as L;
use App\MOVIMENTACOES\DAO\Checklist;
use Funcoes\Layout\Form;
use Funcoes\Layout\FormControls as FC;

$checklistDAO = new Checklist();

$chk_id = $request->get('chk_id', '');
$aChecklist = [];

if ($session->check('previous')) {
    $aChecklist = $session->get('previous');
}

if (!empty($chk_id)) {
    $aChecklist = $checklistDAO->get($chk_id);
    if (empty($aChecklist)) {
        $session->flash('error', 'Checklist não encontrado');
        return $response->back();
    }
}

$pageHeader = L::pageTitle(
    '<h1 class="m-0 text-dark">Item do Checklist</h1>',
    L::backButton()
);

$form = new Form();
$form->setTitle($chk_id ? 'Editar Item' . ": $chk_id - {$aChecklist['ano_titulo']}" : 'Novo Item do Checklist');
$form->setForm('id="form-checklist" action="?posicao=salvar" method="post"');
if (!empty($chk_id)) {
    $form->addHidden(FC::hidden('chk_id', $chk_id));
    $form->addHidden(FC::hidden('alterar', 'S'));
}

$campo_descricao = FC::input(
    'Descrição',
    'chk_descricao',
    $aChecklist['chk_descricao'] ?? '',
    ['class' => 'form-control form-control-sm', 'div_class' => 'col-md-10', 'autofocus' => 'autofocus']
);

$aForm = array(
    ['<div class="row">' . $campo_descricao . '</div>']
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
            $('#form-checklist').validate({
                rules: {
                    chk_descricao: {
                        required: true
                    }
                },
                messages: {
                    chk_descricao: {
                        required: 'Informe a Descrição'
                    }
                }
            });
        });   
    </script>
    HTML,
    ["title" => 'Item do Checklist']
);
