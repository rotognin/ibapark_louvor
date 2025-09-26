<?php

use Funcoes\Layout\Layout as L;
use App\MOVIMENTACOES\DAO\Checklist;
use Funcoes\Layout\FormControls as FC;
use Funcoes\Layout\Table;
use Funcoes\Lib\Funcoes\Datas;
use Funcoes\Helpers\Format;

global $session;
$usuario = $session->get('credentials.default');

$checklistDAO = new Checklist();
$datas = new Datas();

$pageHeader = L::pageTitle('<h1 class="m-0 text-dark">Checklist</h1>', L::linkButton('Novo Item', '?posicao=form', '', 'fas fa-plus', 'primary'));

$table = new Table('checklist-desmarcados');
$table->setBordered(true);
$table->setFooter(false);
$table->setStriped(true);
$table->setSize('sm');
$table->addHeader([
    'cols' => [
        ['value' => 'Item', 'attrs' => ['class' => 'text-center']],
        ['value' => 'Criado Em', 'attrs' => ['class' => 'text-center']],
        ['value' => 'Check', 'attrs' => ['class' => 'text-center', 'style' => 'width:100px']],
    ],
]);

// Buscar os checks desmarcados
$aRegistros = $checklistDAO->getDesmarcados($usuario);

if (!empty($aRegistros)) {
    foreach ($aRegistros as $reg) {
        $check = FC::checkbox(
            '',
            'chk_id_' . $reg['chk_id'],
            '',
            [
                'div_class' => 'form-check-inline align-self-center',
                'checked' => '',
                'class' => 'form-control form-control-sm',
                'style' => 'height:13px',
                'event' => 'onclick="marcar(' . $reg['chk_id'] . ')"'
            ]
        );

        $table->addRow([
            'cols' => [
                ['value' => $reg['chk_descricao'], 'attrs' => ['class' => 'text-left']],
                ['value' => Format::datetime($reg['chk_criado_em']), 'attrs' => ['class' => 'text-center']],
                ['value' => $check, 'attrs' => ['class' => 'text-center']]
            ]
        ]);
    }
}

$tableMarcados = new Table('checklist-marcados');
$tableMarcados->setBordered(true);
$tableMarcados->setFooter(false);
$tableMarcados->setStriped(true);
$tableMarcados->setSize('sm');
$tableMarcados->addHeader([
    'cols' => [
        ['value' => 'Item', 'attrs' => ['class' => 'text-center']],
        ['value' => 'Criado Em', 'attrs' => ['class' => 'text-center']],
        ['value' => 'Marcado Em', 'attrs' => ['class' => 'text-center']],
        ['value' => 'Ações', 'attrs' => ['class' => 'text-center', 'style' => 'width:100px']],
        ['value' => 'Check', 'attrs' => ['class' => 'text-center', 'style' => 'width:100px']]
    ],
]);

// Buscar os checks marcados
$aRegistros = $checklistDAO->getMarcados($usuario);

if (!empty($aRegistros)) {
    foreach ($aRegistros as $reg) {
        $botoes = L::buttonGroup([
            L::button('', 'excluir(' . $reg['chk_id'] . ')', 'Excluir', 'fas fa-trash', 'outline-danger', 'sm')
        ]);

        $check = FC::checkbox(
            '',
            'chk_id_' . $reg['chk_id'],
            '',
            [
                'div_class' => 'form-check-inline align-self-center',
                'checked' => 'checked',
                'class' => 'form-control form-control-sm',
                'style' => 'height:13px',
                'event' => 'onclick="desmarcar(' . $reg['chk_id'] . ')"'
            ]
        );

        $tableMarcados->addRow([
            'cols' => [
                ['value' => $reg['chk_descricao'], 'attrs' => ['class' => 'text-left']],
                ['value' => Format::datetime($reg['chk_criado_em']), 'attrs' => ['class' => 'text-center']],
                ['value' => Format::datetime($reg['chk_marcado_em']), 'attrs' => ['class' => 'text-center']],
                ['value' => $botoes, 'attrs' => ['class' => 'text-center']],
                ['value' => $check, 'attrs' => ['class' => 'text-center']],
            ]
        ]);
    }
}

$html = '';

$html = $table->html();
$html .= '<hr>';
$html .= $tableMarcados->html();

$response->page(
    <<<HTML
        {$pageHeader}
        <div class="content">
            <div class="container-fluid pb-1">
                {$html}
            </div>
        </div>
        <script>
            function marcar(chk_id){
                window.location.href = '?posicao=alterar&chk_id=' + chk_id + '&acao=marcar';
            }

            function desmarcar(chk_id){
                window.location.href = '?posicao=alterar&chk_id=' + chk_id + '&acao=desmarcar';
            }

            function excluir(chk_id){
                confirm('Deseja realmente excluir este item?').then(result => {
                    if (result.isConfirmed) {
                        window.location.href = '?posicao=excluir&chk_id=' + chk_id;
                    }
                });
            }
        </script>
    HTML,
    ["title" => 'Checklist']
);
