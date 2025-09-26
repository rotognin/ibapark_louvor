<?php

use Funcoes\Layout\Layout as L;
use App\CADASTRO\DAO\Grupos;
use Funcoes\Layout\Form;
use Funcoes\Layout\FormControls as FC;

$gruposDAO = new Grupos();

$gru_id = $request->get('gru_id', '');
$aGrupo = [];

if ($session->check('previous')) {
    $aGrupo = $session->get('previous');
}

if (!empty($gru_id)) {
    $aGrupo = $gruposDAO->get($gru_id);
    if (empty($aGrupo)) {
        $session->flash('error', 'Grupo não encontrado');
        return $response->back();
    }
}

$pageHeader = L::pageTitle(
    '<h1 class="m-0 text-dark">Cadastro de Grupo</h1>',
    L::backButton()
);

$form = new Form();
$form->setTitle($gru_id ? 'Editar Grupo' . ": $gru_id - {$aGrupo['gru_nome']}" : 'Novo Grupo');
$form->setForm('id="form-grupo" action="?posicao=salvar" method="post"');
if (!empty($gru_id)) {
    $form->addHidden(FC::hidden('gru_id', $gru_id));
    $form->addHidden(FC::hidden('alterar', 'S'));
}

$campo_nome = FC::input(
    'Nome',
    'gru_nome',
    $aGrupo['gru_nome'] ?? '',
    ['class' => 'form-control form-control-sm', 'div_class' => 'col-md-5', 'autofocus' => 'autofocus', 'style' => 'text-transform: uppercase']
);

$aTipos = ['0' => 'Selecione...'] + $gruposDAO->getTipo();

$campo_tipo = FC::select('Tipo', 'gru_tipo', $aTipos, $aGrupo['gru_tipo'] ?? 0, [
    'class' => 'form-control form-control-sm',
    'div_class' => 'col-md-3'
]);

$aForm = array(
    ['<div class="row">' . $campo_nome . '</div>'],
    ['<div class="row">' . $campo_tipo . '</div>']
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
        $.validator.addMethod("verificarTipo", function(tipo){
            if (tipo == '' || tipo == 0 || tipo == undefined){
                return false;
            }

            return true;
        }, "Informe o tipo");

        $('#form-grupo').validate({
            rules: {
                gru_nome: {
                    required: true
                },
                gru_tipo: "verificarTipo"
            },
            messages: {
                gru_nome: {
                    required: 'Informe o Nome do Grupo'
                }
            }
        });
    });   
    </script>
    HTML,
    ["title" => 'Cadastro de Grupo']
);
