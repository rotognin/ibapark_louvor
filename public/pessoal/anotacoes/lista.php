<?php

use Funcoes\Layout\Layout as L;
use App\PESSOAL\DAO\Anotacoes;
use App\PESSOAL\Datatables\DatatableAnotacoes;
use Funcoes\Layout\Form;
use Funcoes\Layout\FormControls as FC;
use Funcoes\Layout\Datatable;
use Funcoes\Layout\ModalForm;

$anotacoesDAO = new Anotacoes();

$pageHeader = L::pageTitle('<h1 class="m-0 text-dark">Anotações</h1>', L::linkButton('Nova Anotação', '?posicao=form', '', 'fas fa-plus', 'primary'));

// Filtros
$form = new Form();
$form->setTitle('<i class="fas fa-filter"></i> Filtros');
$form->setForm('action="" method="GET"');
$form->setCollapsable(true);
$form->setCollapsed(count($request->getArray()) == 0);
$form->setActions(L::submit('Filtrar', 'fas fa-filter'));
$form->setInternalPadding('pt-1 pb-1');

$filtro_titulo = FC::input(
    'Título',
    'ano_titulo',
    $request->get('ano_titulo', ''),
    [
        'class' => 'form-control form-control-sm',
        'div_class' => 'col-md-3'
    ]
);

$form->setFields([
    ['<div class="row">' . $filtro_titulo . '</div>']
]);

$table = new Datatable(DatatableAnotacoes::class);

$html = $table->html();

$modalAnotacao = new ModalForm('modal-anotacao');
$modalAnotacao->setForm('id="form-estudo" action="#"');
$modalAnotacao->setTitle('Anotação - <span id="titulo_anotacao"></span>');
$modalAnotacao->setModalSize('modal-xl');

$campo_texto = FC::textarea('Texto', 'ano_texto', '', [
    'class' => 'form-control form-control-sm',
    'div_class' => 'col-md-12',
    'rows' => 15,
    'readonly' => 'readonly'
]);

$modalAnotacao->setFields([
    [$campo_texto]
]);

$modalAnotacao->setActions(
    L::button('Fechar', '', '', '', 'secondary', 'sm', 'data-dismiss="modal"')
);

$response->page(
    <<<HTML
        {$pageHeader}
        <div class="content">
            <div class="container-fluid pb-1">
                {$form->html()}
                {$html}
            </div>
            {$modalAnotacao->html()}
        </div>
        <script>
            function lerAnotacao(ano_id){
                $("#titulo_anotacao").html('');
                $("#ano_texto").val('');

                // Buscar as informações da anotação
                $.ajax({
                    url: '/geral/xhr/pessoal/buscarAnotacao.php?ano_id=' + ano_id,
                    type: 'get'
                }).done(function(retorno){
                    if (retorno.startsWith('erro')){
                        var msg = retorno.slice(5);
                        mensagem(msg);
                        return false;
                    }

                    var dados = JSON.parse(retorno);
                    $("#titulo_anotacao").html(dados.ano_titulo);
                    $("#ano_texto").val(dados.ano_texto);

                    $("#modal-anotacao").modal();
                });
            }

            function excluirAnotacao(link){
                confirm('Tem certeza da exclusão da Anotação?').then(result => {
                    if (result.isConfirmed) {
                        window.location.href = link;
                    }
                });
            }
        </script>
    HTML,
    ["title" => 'Anotações']
);
