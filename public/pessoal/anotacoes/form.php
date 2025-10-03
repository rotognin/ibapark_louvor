<?php

use Funcoes\Layout\Layout as L;
use App\PESSOAL\DAO\Anotacoes;
use Funcoes\Layout\Form;
use Funcoes\Layout\FormControls as FC;

$anotacaoDAO = new Anotacoes();

$ano_id = $request->get('ano_id', '');
$aAnotacao = [];

if ($session->check('previous')) {
    $aAnotacao = $session->get('previous');
}

if (!empty($ano_id)) {
    $aAnotacao = $anotacaoDAO->get($ano_id);
    if (empty($aAnotacao)) {
        $session->flash('error', 'Anotação não encontrada');
        return $response->back();
    }
}

$pageHeader = L::pageTitle(
    '<h1 class="m-0 text-dark">Cadastro de Anotação</h1>',
    L::backButton()
);

$form = new Form();
$form->setTitle($ano_id ? 'Editar Anotação' . ": $ano_id - {$aAnotacao['ano_titulo']}" : 'Nova Anotação');
$form->setForm('id="form-anotacao" action="?posicao=salvar" method="post"');
if (!empty($ano_id)) {
    $form->addHidden(FC::hidden('ano_id', $ano_id));
    $form->addHidden(FC::hidden('alterar', 'S'));
}

$campo_titulo = FC::input(
    'Título',
    'ano_titulo',
    $aAnotacao['ano_titulo'] ?? '',
    ['class' => 'form-control form-control-sm', 'div_class' => 'col-md-7', 'autofocus' => 'autofocus']
);

$campo_texto = FC::textarea('Texto', 'ano_texto', $aAnotacao['ano_texto'] ?? '', [
    'class' => 'form-control form-control-sm',
    'div_class' => 'col-md-8',
    'rows' => 10
]);

$aForm = array(
    ['<div class="row">' . $campo_titulo . '</div>'],
    ['<div class="row">' . $campo_texto . '</div>']
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
        $('#form-anotacao').validate({
            rules: {
                ano_titulo: {
                    required: true
                },
                ano_texto: {
                    required: true
                }
            },
            messages: {
                ano_titulo: {
                    required: 'Informe o Título'
                },
                ano_texto: {
                    required: "Informe o Texto"
                }
            }
        });
    });   
    </script>
    HTML,
    ["title" => 'Cadastro de Anotação']
);
