<?php

use Funcoes\Layout\Layout as L;
use App\MOVIMENTACOES\DAO\Notas;
use Funcoes\Layout\Form;
use Funcoes\Layout\FormControls as FC;
use Funcoes\Layout\Table;
use Funcoes\Lib\Funcoes\Image;

$notasDAO = new Notas();

$pageHeader = L::pageTitle('<h1 class="m-0 text-dark">Notas Encadeadas</h1>');

// Filtros
$formFiltro = new Form();
$formFiltro->setTitle('<i class="fas fa-filter"></i> Filtros');
$formFiltro->setForm('action="" method="GET"');
$formFiltro->setCollapsable(true);
$formFiltro->setCollapsed(false);
$formFiltro->setActions(L::submit('Filtrar', 'fas fa-filter'));
$formFiltro->setInternalPadding('pt-1 pb-1');

$filtro_pai = FC::select2('Principal', 'not_pai', ['0' => 'Todos'] + $notasDAO->buscarPais(), $request->get('not_pai', 0), [
    'class' => 'form-control form-control-sm',
    'div_class' => 'col-md-4'
]);

$formFiltro->setFields([
    ['<div class="row">' . $filtro_pai . '</div>']
]);

$imgNivel0 = new Image('plus.png', 'Adicionar Item Nível 1');
$imgNivel0->setLink("?posicao=form");
$imgNivel0->setWidth(24);
$imgNivel0->setHeight(24);

$table = new Table('tabela-notas');
$table->setBordered(true);
$table->setFooter(false);
$table->setStriped(true);
$table->setSize('sm');
$table->addHeader([
    'cols' => [
        ['value' => 'Nível / Item', 'attrs' => ['class' => 'text-left']],
        ['value' => 'Descrição', 'attrs' => ['class' => 'text-left']],
        ['value' => $imgNivel0->html(), 'attrs' => ['class' => 'text-center']]
    ],
]);

$aNotas = array();
$nivel = 0;
$aFamilia = array();

$id_pai = $request->get('not_pai', '');

$aEstrutura = $notasDAO->estrutura($id_pai, $aNotas, $nivel, $aFamilia);

if (!empty($aNotas)) {
    foreach ($aNotas as $aNota) {
        $id = $aNota['id'];
        $descricao = $aNota['descricao'];
        $texto = $aNota['texto'];
        $id_pai = $aNota['id_pai'];
        $nivel = $aNota['nivel'];
        $ultimo = $aNota['ultimo'];
        $aFamilia = $aNota['familia'];

        $img = '';
        for ($x = 1; $x <= $nivel; $x++) {
            $a = '';
            if ($x == $nivel) {
                if ($ultimo)
                    $a = new Image('estrutura_quina.png');
                else
                    $a = new Image('estrutura_cruzamento.png');
            } else {
                if (@$aFamilia[$x] == 1)
                    $a = new Image('estrutura_linha.png');
            }
            if ($a == '')
                $a = new Image('estrutura_vazio.png');
            $img .= $a->html();
        }

        if ($nivel == 1) {
            $icone = '<i class="fas fa-plus-square" onclick="mostrar(' . $id . ')" id="icon_' . $id . '"></i>';
            $class = '';
        } else {
            $icone = $icone = '<i class="fas fa-plus-square" onclick="mostrar(' . $id . ')" id="icon_' . $id . '"></i>';
            $class = 'd-none';
        }

        $botoes = L::buttonGroup([
            L::linkButton('', '?posicao=form&id_pai=' . $id, 'Adicionar Filho', 'fas fa-plus-circle', 'outline-success'),
            L::linkButton('', '?posicao=form&id=' . $id, 'Editar', 'fas fa-edit', 'outline-success'),
            L::button('', 'anexos(' . $id . ')', 'Anexos', 'fas fa-file', 'outline-success')
        ]);

        $table->addRow([
            'cols' => [
                ['value' => '<nobr>' . $icone . $img . "&nbsp;&nbsp;<strong>" . $descricao . "</strong></nobr>"],
                ['value' => nl2br($texto)],
                ['value' => $botoes, 'attrs' => ['class' => 'text-center']]
            ],
            'attrs' => [
                'class' => $class . ' id_pai_' . $id_pai,
                'id' => $id
            ]
        ]);
    }
}

$html = $table->html();

$response->page(
    <<<HTML
        {$pageHeader}
        <div class="content">
            <div class="container-fluid pb-1">
                {$formFiltro->html()}
                {$html}
            </div>
        </div>
        <script>
            function mostrar(id){
                if ($("#icon_" + id).hasClass('fas fa-plus-square')){
                    $(".id_pai_" + id).removeClass('d-none');
                    $("#icon_" + id).removeClass('fas fa-plus-square').addClass('far fa-minus-square');
                } else {
                    $(".id_pai_" + id).addClass('d-none');
                    $("#icon_" + id).removeClass('far fa-minus-square').addClass('fas fa-plus-square');
                }
            }
        </script>
    HTML,
    ["title" => 'Notas Encadeadas']
);
