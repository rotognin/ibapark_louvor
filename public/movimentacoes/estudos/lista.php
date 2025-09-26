<?php

use Funcoes\Layout\Layout as L;
use App\MOVIMENTACOES\DAO\Estudos;
use App\CADASTRO\DAO\Grupos;
use App\MOVIMENTACOES\Datatables\DatatableEstudos;
use Funcoes\Layout\Form;
use Funcoes\Layout\FormControls as FC;
use Funcoes\Layout\Datatable;
use Funcoes\Layout\Table;
use Funcoes\Layout\ModalForm;

$estudosDAO = new Estudos();
$gruposDAO = new Grupos();

$pageHeader = L::pageTitle('<h1 class="m-0 text-dark">Estudos Bíblicos</h1>', L::button('Novo Estudo', 'novoEstudo()', '', 'fas fa-plus', 'primary'));

// Filtros
$form = new Form();
$form->setTitle('<i class="fas fa-filter"></i> Filtros');
$form->setForm('action="" method="GET"');
$form->setCollapsable(true);
$form->setCollapsed(count($request->getArray()) == 0);
$form->setActions(L::submit('Filtrar', 'fas fa-filter'));
$form->setInternalPadding('pt-1 pb-1');

$filtro_nome = FC::input(
    'Nome',
    'bib_titulo_filtro',
    $request->get('bib_titulo_filtro', ''),
    [
        'class' => 'form-control form-control-sm',
        'div_class' => 'col-md-5'
    ]
);

$filtro_referencia = FC::input(
    'Referência',
    'bib_referencia_filtro',
    $request->get('bib_referencia_filtro', ''),
    [
        'class' => 'form-control form-control-sm',
        'div_class' => 'col-md-4'
    ]
);

$where = array('');
$where[0] = ' AND gru_tipo = ?';
$where[1][] = 1;

$aGrupos = ['0' => 'Todos'] + $gruposDAO->montarArray($where);

$filtro_grupo = FC::select('Tipo', 'bib_grupo_id_filtro', $aGrupos, $request->get('bib_grupo_id_filtro', 0), [
    'class' => 'form-control form-control-sm',
    'div_class' => 'col-md-3'
]);

$form->setFields([
    ['<div class="row">' . $filtro_nome . $filtro_referencia . $filtro_grupo . '</div>']
]);

$table = new Datatable(DatatableEstudos::class);

$html = $table->html();

$modalEstudo = new ModalForm('modal-estudo');
$modalEstudo->setForm('id="form-estudo" method="POST" action="?posicao=salvar"');
$modalEstudo->setTitle('Estudo Bíblico - <span id="data-estudo"></span>');
$modalEstudo->setModalSize('modal-xl');

$modalEstudo->addHidden(FC::hidden('bib_id', ''));

$modal_titulo = FC::input('Título', 'bib_titulo', '', [
    'class' => 'form-control form-control-sm',
    'div_class' => 'col-md-5'
]);

$modal_referencia = FC::input('Referência', 'bib_referencia', '', [
    'class' => 'form-control form-control-sm',
    'div_class' => 'col-md-4'
]);

$where = array('');
$where[0] = ' AND gru_tipo = ?';
$where[1][] = 1;

$aGrupos = ['0' => 'Selecione...'] + $gruposDAO->montarArray($where);

$modal_grupo = FC::select('Tipo', 'bib_grupo_id', $aGrupos, $request->get('bib_grupo_id', 0), [
    'class' => 'form-control form-control-sm',
    'div_class' => 'col-md-3'
]);

$modal_texto = FC::textarea('Texto', 'bib_texto', '', [
    'class' => 'form-control form-control-sm',
    'div_class' => 'col-md-12',
    'rows' => 15
]);

$tabela = new Table('tabela-anexos');
$tabela->setBordered(true);
$tabela->setFooter(false);
$tabela->setStriped(true);
$tabela->setSize('sm');

$tabela->addHeader([
    'cols' => [
        ['value' => 'Arquivo', 'attrs' => ['class' => 'text-center', 'style' => 'width:30%']],
        ['value' => 'Descrição', 'attrs' => ['class' => 'text-center', 'style' => 'width:60%']],
        ['value' => 'Ações', 'attrs' => ['class' => 'text-center', 'style' => 'width:10%']]
    ]
]);

$botao_add_anexo = L::button('Adicionar', 'adicionar(event)', 'Adicionar Anexo', 'fas fa-plus', 'primary', 'sm', 'id="botao-add-anx"', 'invisible');

$modalEstudo->setFields([
    ['<div class="row">' . $modal_titulo . $modal_referencia . $modal_grupo . '</div>'],
    ['<div class="row">' . $modal_texto . '</div>'],
    ['<hr>'],
    ['<h3>Anexos</h3>'],
    [$tabela->html()],
    [$botao_add_anexo]
]);

$modalEstudo->setActions(
    L::button('Fechar', '', '', '', 'secondary', 'sm', 'data-dismiss="modal"') . ' ' .
        L::button('Gravar', 'gravarEstudo(event)', 'Gravar', 'fas fa-check', 'primary')
);

$response->page(
    <<<HTML
        {$pageHeader}
        <div class="content">
            <div class="container-fluid pb-1">
                {$form->html()}
                {$html}
            </div>
            {$modalEstudo->html()}
        </div>
        <script>
            function limparModal(){
                $("#data-estudo").html('');
                $("input[name='bib_id']").val(0);
                $("#bib_titulo").val('');
                $("#bib_referencia").val('');
                $("#bib_grupo_id").val('0');
                $("#bib_texto").val('');
                $("#tabela-anexos table tbody").html('');

                $("#botao-add-anx").removeClass('invisible').addClass('invisible');
            }

            function editar(bib_id){
                limparModal();
                $("#modal-estudo").modal();

                $.ajax({
                    url: '/geral/xhr/estudos/buscarEstudo.php?bib_id=' + bib_id,
                    type: 'get'
                }).done(function(retorno){
                    if (retorno.startsWith('erro')){
                        var msg = retorno.slice(5);
                        mensagem(msg);
                        $("#modal-estudo").modal('hide');
                        return false;
                    }

                    var dados = JSON.parse(retorno);

                    var aDataHora = dados.bib_data_hora.split(' ');
                    var aData = aDataHora[0].split('-');

                    var dataFormatada = aData[2] + '/' + aData[1] + '/' + aData[0];
                    $("#data-estudo").html(dataFormatada);

                    $("input[name='bib_id']").val(dados.bib_id);
                    $("#bib_titulo").val(dados.bib_titulo);
                    $("#bib_referencia").val(dados.bib_referencia);
                    $("#bib_grupo_id").val(dados.bib_grupo_id);
                    $("#bib_texto").val(dados.bib_texto);

                    $("#botao-add-anx").removeClass('invisible');

                    console.log(dados);

                    if (dados.anexos.length > 0){
                        var tabela = '';
                        var botoes = '';
                        var botao_abrir = '';
                        var botao_excluir = '';

                        dados.anexos.forEach(function(dado, idx){
                            adicionarAnexoTabela(dado);

                            /*
                            botao_abrir = '<button title="Abrir arquivo" class="btn btn-outline-secondary btn-sm" onclick="abrir(event, ' + dado.anx_id + ')"><i class="fas fa-eye"></i></button>';
                            botao_excluir = '<button title="Excluir anexo" class="btn btn-outline-danger btn-sm" onclick="excluir(event, ' + dado.anx_id + ')"><i class="fas fa-trash"></i></button>';
                            botoes = botao_abrir + botao_excluir;

                            var descricao = '<input type="text" style="width:100%" id="descricao_' + dado.anx_id + '" onblur="salvarDescricao(' + dado.anx_id + ')" value="' + dado.anx_descricao + '">';

                            tabela += '<tr id="linha_' + dado.anx_id + '">';
                                tabela += '<td>' + dado.anx_fisico_arquivo + '</td>';
                                tabela += '<td>' + descricao + '</td>';
                                tabela += '<td class="text-center">' + botoes + '</td>';
                            tabela += '</tr>';
                            */
                        });

                        //$("#tabela-anexos table tbody").html(tabela);
                    }
                });
            }

            function adicionarAnexoTabela(dados){
                botao_abrir = '<button title="Abrir arquivo" class="btn btn-outline-secondary btn-sm" onclick="abrir(event, ' + dados.anx_id + ')"><i class="fas fa-eye"></i></button>';
                botao_excluir = '<button title="Excluir anexo" class="btn btn-outline-danger btn-sm" onclick="excluir(event, ' + dados.anx_id + ')"><i class="fas fa-trash"></i></button>';
                botoes = botao_abrir + botao_excluir;

                var descricao = '<input type="text" style="width:100%" id="descricao_' + dados.anx_id + '" onblur="salvarDescricao(' + dados.anx_id + ')" value="' + dados.anx_descricao + '">';

                var html = '';
                html += '<tr id="linha_' + dados.anx_id + '">';
                    html += '<td>' + dados.anx_fisico_arquivo + '</td>';
                    html += '<td>' + descricao + '</td>';
                    html += '<td class="text-center">' + botoes + '</td>';
                html += '</tr>';

                $("#tabela-anexos table tbody").append(html);
            }

            function novoEstudo(){
                limparModal();

                const dataAtual = new Date();
                const dia = String(dataAtual.getDate()).padStart(2, '0');
                const mes = String(dataAtual.getMonth() + 1).padStart(2, '0');
                const ano = dataAtual.getFullYear();

                const dataFormatada = dia + '/' + mes + '/' + ano;

                $("#data-estudo").html(dataFormatada);

                $("#modal-estudo").modal();

                setTimeout(function(){
                    $("#bib_titulo").focus();
                }, 500);
            }

            function adicionar(e){
                e.preventDefault();

                var input = document.createElement('input');
                input.type = 'file';

                input.onchange = e => {
                    var file = e.target.files[0];
                    var bib_id = $("input[name='bib_id']").val();

                    var extensao = file.name.split('.').pop().toLowerCase();
                    const extensoes = ['pdf', 'jpg', 'jpeg', 'bmp', 'png', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'];

                    if ($.inArray(extensao, extensoes) == -1) {
                        alert('Extensão não permitida. Extensões permitidas: ' + extensoes.join(', '));
                        return false;
                    }

                    var formData = new FormData();
                    formData.append('arquivo', file);
                    formData.append('bib_id', bib_id);

                    $.ajax({
                        url: '?posicao=upload',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(retorno){
                            if (retorno == 'erro'){
                                alert('Não foi possível enviar o arquivo');
                            } else {
                                var dados = JSON.parse(retorno);
                                adicionarAnexoTabela(dados);

                                /*
                                botao_abrir = '<button title="Abrir arquivo" class="btn btn-outline-secondary btn-sm" onclick="abrir(event, ' + dados.anx_id + ')"><i class="fas fa-eye"></i></button>';
                                botao_excluir = '<button title="Excluir anexo" class="btn btn-outline-danger btn-sm" onclick="excluir(event, ' + dados.anx_id + ')"><i class="fas fa-trash"></i></button>';
                                botoes = botao_abrir + botao_excluir;

                                var descricao = '<input type="text" style="width:100%" id="descricao_' + dados.anx_id + '" onblur="salvarDescricao(' + dados.anx_id + ')">';

                                var html = '';
                                html += '<tr id="linha_' + dados.anx_id + '">';
                                    html += '<td>' + dados.anx_fisico_arquivo + '</td>';
                                    html += '<td>' + descricao + '</td>';
                                    html += '<td class="text-center">' + botoes + '</td>';
                                html += '</tr>';

                                $("#tabela-anexos table tbody").append(html);
                                */
                            }
                        }
                    });
                }

                input.click();
            }

            function abrir(e, anx_id){
                e.preventDefault();
                window.open('/geral/abrir/abrirAnexo.php?anx_id=' + anx_id, '_blank');
            }

            function salvarDescricao(anx_id){
                var descricao = $("#descricao_" + anx_id).val();

                $.ajax({
                    url: '/geral/xhr/estudos/gravarDescricao.php?anx_id=' + anx_id + '&descricao=' + descricao,
                    type: 'get'
                }).done(function(retorno){
                    if (retorno == 'erro'){
                        mensagem('Não foi possível gravar a descrição informada.');
                    }
                });
            }

            function excluir(e, anx_id){
                e.preventDefault();

                confirm('Tem certeza da exclusão do Anexo?').then(result => {

                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/geral/xhr/estudos/excluirAnexo.php?anx_id=' + anx_id,
                            type: 'get'
                        }).done(function(retorno){
                            if (retorno == 'erro'){
                                mensagem('Não foi possível excluir o anexo');
                                return false;
                            }

                            $("#linha_" + anx_id).remove();
                        });
                    }
                });
            }
        </script>
    HTML,
    ["title" => 'Estudos Bíblicos']
);
