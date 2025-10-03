<?php

use Funcoes\Layout\Layout as L;
use App\CADASTRO\DAO\Integrantes;
use App\SGC\DAO\Usuario;
use Funcoes\Layout\Form;
use Funcoes\Layout\FormControls as FC;

$integrantesDAO = new Integrantes();
$usuarioDAO = new Usuario();

$int_id = $request->get('int_id', '');
$aIntegrante = [];

if ($session->check('previous')) {
    $aIntegrante = $session->get('previous');
}

if (!empty($int_id)) {
    $aIntegrante = $integrantesDAO->get($int_id);
    if (empty($aIntegrante)) {
        $session->flash('error', 'Integrante não encontrado');
        return $response->back();
    }
}

$pageHeader = L::pageTitle(
    '<h1 class="m-0 text-dark">Cadastro de Integrante</h1>',
    L::backButton()
);

$form = new Form();
$form->setTitle($int_id ? 'Editar Integrante' . ": $int_id - {$aIntegrante['int_nome']}" : 'Novo Integrante');
$form->setForm('id="form-integrante" action="?posicao=salvar" method="post"');
if (!empty($int_id)) {
    $form->addHidden(FC::hidden('int_id', $int_id));
    $form->addHidden(FC::hidden('alterar', 'S'));
}

$campo_nome = FC::input(
    'Nome',
    'int_nome',
    $aIntegrante['int_nome'] ?? '',
    ['class' => 'form-control form-control-sm', 'div_class' => 'col-md-5', 'autofocus' => 'autofocus']
);

$aUsuarios = ['0' => 'Selecione'] + $usuarioDAO->obterAtivos();

$campo_usuario = FC::select('Usuário', 'int_usuario', $aUsuarios, $aIntegrante['int_usuario'] ?? '0', [
    'class' => 'form-control form-control-sm', 
    'div_class' => 'col-md-3'
]);

$campo_ativo = FC::select('Ativo?', 'int_ativo', ['S' => 'Sim', 'N' => 'Não'], $aIntegrante['int_ativo'] ?? 'S', [
    'class' => 'form-control form-control-sm', 
    'div_class' => 'col-md-1'
]);

$campo_observacoes = FC::input('Observações', 'int_observacoes', $aIntegrante['int_observacoes'] ?? '', [
    'class' => 'form-control form-control-sm', 
    'div_class' => 'col-md-8'
]);

$aForm = array(
    ['<div class="row">' . $campo_nome . $campo_usuario . $campo_ativo . '</div>'],
    ['<div class="row">' . $campo_observacoes . '</div>']
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
        $('#form-integrante').validate({
            rules: {
                int_nome: {
                    required: true
                }
            },
            messages: {
                gru_nome: {
                    required: 'Informe o Nome do Integrante'
                }
            }
        });
    });   
    </script>
    HTML,
    ["title" => 'Cadastro de Integrante']
);
