<?php

use Funcoes\Layout\Layout as L;
use App\CADASTRO\DAO\Tarefas;
use Funcoes\Layout\Form;
use Funcoes\Helpers\Format;
use Funcoes\Layout\FormControls as FC;

$tarefasDAO = new Tarefas();

$tar_id = $request->get('tar_id', '');
$aTarefa = [];

if ($session->check('previous')) {
    $aTarefa = $session->get('previous');
}

if (!empty($tar_id)) {
    $aTarefa = $tarefasDAO->get($tar_id);
    if (empty($aTarefa)) {
        $session->flash('error', 'Tarefa não encontrada');
        return $response->back();
    }
}

$pageHeader = L::pageTitle(
    '<h1 class="m-0 text-dark">Cadastro de Tarefas</h1>',
    L::backButton()
);

$form = new Form();
$form->setTitle($tar_id ? 'Editar Tarefa' . ": $tar_id - {$aTarefa['tar_descricao']}" : 'Nova Tarefa');
$form->setForm('id="form-tarefa" action="?posicao=salvar" method="post"');
if (!empty($tar_id)) {
    $form->addHidden(FC::hidden('tar_id', $tar_id));
    $form->addHidden(FC::hidden('alterar', 'S'));
}

$campo_descricao = FC::input(
    'Descrição',
    'tar_descricao',
    $aTarefa['tar_descricao'] ?? '',
    ['class' => 'form-control form-control-sm', 'div_class' => 'col-md-10', 'autofocus' => 'autofocus', 'style' => 'text-transform: uppercase']
);

$tar_data = date('Y-m-d');

if (!empty($tar_id)) {
    $tar_data = $aTarefa['tar_data'];
}

$campo_data = FC::date('Data', 'tar_data', Format::date($tar_data), [
    'class' => 'form-control form-control-sm',
    'div_class' => 'col-md-2'
]);

$tar_data_limite = date('Y-m-d');

if (!empty($tar_id)) {
    $tar_data_limite = $aTarefa['tar_data_limite'];
}

$campo_data_limite = FC::date('Data Limite', 'tar_data_limite', Format::date($tar_data_limite), [
    'class' => 'form-control form-control-sm',
    'div_class' => 'col-md-2'
]);

$aForm = array(
    ['<div class="row">' . $campo_descricao . '</div>'],
    ['<div class="row">' . $campo_data . $campo_data_limite . '</div>']
);

$form->setFields($aForm);
$form->setActions(L::submit(_('Salvar')));

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
        $.validator.addMethod("verificarData", function(data){
            if (data == '' || data == undefined){
                return false;
            }

            if (!dataValida(data)){
                return false;
            }

            return true;
        }, "Data inválida");

        $.validator.addMethod("verificarDataLimite", function(data){
            if (data == '' || data == undefined){
                return false;
            }

            if (!dataValida(data)){
                return false;
            }

            return true;
        }, "Data inválida");

        $('#form-tarefa').validate({
            onfocusout: false,
            onkeyup: false,
            onclick: false,
            onsubmit: true,
            rules: {
                tar_descricao: {
                    required: true
                },
                tar_data: "verificarData",
                tar_data_limite: "verificarDataLimite"
            },
            messages: {
                tar_descricao: {
                    required: 'Informe a Descrição'
                }
            }
        });
    });   
    </script>
    HTML,
    ["title" => 'Cadastro de Tarefa']
);
