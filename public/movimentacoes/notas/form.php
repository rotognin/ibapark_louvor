<?php

use Funcoes\Layout\Layout as L;
use App\MOVIMENTACOES\DAO\Notas;
use Funcoes\Layout\Form;
use Funcoes\Layout\FormControls as FC;

$notasDAO = new Notas();

$not_id = $request->get('id', '');
$aNota = [];

if (!empty($not_id)) {
    $aNota = $notasDAO->get($not_id);
    if (empty($aNota)) {
        $session->flash('error', 'Registro não encontrado');
        return $response->back();
    }
}

$not_id_pai = $request->get('id_pai', 0);

$pageHeader = L::pageTitle(
    '<h1 class="m-0 text-dark">Nota</h1>',
    L::backButton()
);

$form = new Form();
$form->setTitle($not_id ? 'Editar Registro' . ": $not_id - {$aNota['not_titulo']}" : 'Novo Registro');
$form->setForm('id="form-nota" action="?posicao=salvar" method="post"');
$form->addHidden(FC::hidden('id_pai', $not_id_pai));

if (!empty($not_id)) {
    $form->addHidden(FC::hidden('id', $not_id));
    $form->addHidden(FC::hidden('alterar', 'S'));
}

$campo_descricao = FC::input('Descrição', 'titulo', $aNota['not_titulo'] ?? '', [
    'class' => 'form-control form-control-sm',
    'div_class' => 'col-md-5',
    'autofocus' => 'autofocus',
    'style' => 'text-transform: uppercase'
]);

$campo_texto = FC::textarea('Texto', 'texto', $aNota['not_texto'] ?? '', [
    'class' => 'form-control form-control-sm',
    'div_class' => 'col-md-10',
    'rows' => 5
]);

$aForm = array(
    ['<div class="row">' . $campo_descricao . '</div>'],
    ['<div class="row">' . $campo_texto . '</div>']
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
        $('#form-nota').validate({
            rules: {
                not_titulo: {
                    required: true
                }
            },
            messages: {
                not_titulo: {
                    required: 'Informe a Descrição'
                }
            }
        });
    });   
    </script>
    HTML,
    ["title" => 'Nota']
);
