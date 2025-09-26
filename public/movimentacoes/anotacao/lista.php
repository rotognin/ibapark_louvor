<?php

use Funcoes\Layout\Layout as L;
use App\MOVIMENTACOES\DAO\AnotacaoBiblica;
use App\CADASTRO\DAO\Livros;
use App\CADASTRO\DAO\Versiculos;
use Funcoes\Layout\Form;
use Funcoes\Layout\FormControls as FC;
use Funcoes\Helpers\Format;

global $session;
$usuario = $session->get('credentials.default');

$anotacaoDAO = new AnotacaoBiblica();
$livrosDAO = new Livros();
$versiculosDAO = new Versiculos();

$pageHeader = L::pageTitle('<h1 class="m-0 text-dark">Anotações Bíblicas</h1>');

$form = new Form();
$form->setTitle('Anotações Bíblicas');
$form->setForm('action="" method="GET"');
//$form->setCollapsable(true);
//$form->setCollapsed(false);
//$form->setActions(L::submit('Filtrar', 'fas fa-filter'));
$form->setInternalPadding('pt-1 pb-1');

$campo_livro = FC::select('Livro', 'sel_livro', ['0' => 'Selecione...'] + $livrosDAO->montarArray(), '0', [
    'class' => 'form-control form-control-sm',
    'div_class' => 'col-md-3',
    'event' => 'onchange="buscarCapitulos()"'
]);

$campo_capitulo = FC::select('Capítulo', 'sel_capitulo', [], '', [
    'class' => 'form-control form-control-sm',
    'div_class' => 'col-md-1',
    'event' => 'onchange="buscarTexto();buscarAnotacao()"'
]);

$campo_texto = FC::textarea('', 'texto', '', [
    'class' => 'form-control form-control-sm',
    'div_class' => 'col-md-10 mb-4',
    'rows' => 10,
    'readonly' => 'readonly'
]);

$campo_anotacao = FC::textarea('Anotações', 'anotacao', '', [
    'class' => 'form-control form-control-sm',
    'div_class' => 'col-md-10',
    'rows' => 6
]);

$botao_gravar = L::button('Salvar', 'salvarAnotacao(event)', 'Salvar', 'fas fa-check', 'primary', 'sm');

$form->setFields([
    ['<div class="row">' . $campo_livro . $campo_capitulo . '</div>'],
    ['<div class="row">' . $campo_texto . '</div>'],
    ['<div class="row">' . $campo_anotacao . '</div>'],
    [$botao_gravar . '&nbsp;&nbsp;&nbsp;&nbsp;<span id="msg_gravado">Anotação gravada!</span>'],
    ['<br>']
]);

$html = '';
$html = $form->html();

$response->page(
    <<<HTML
        <div class="content">
            <div class="container-fluid pb-1">
                {$html}
            </div>
        </div>
        <script>
            $("#msg_gravado").hide();

            function buscarCapitulos(){
                var livro = $("#sel_livro option:selected").val();
                $("#sel_capitulo").empty();

                if (livro > 0){
                    $.ajax({
                        url: '/geral/xhr/biblia/buscarLivro.php?id=' + livro,
                        type: 'get'
                    }).done(function(retorno){
                        if (retorno.startsWith('erro')){
                            var msg = retorno.slice(5);
                            mensagem(msg);
                            return false;
                        }

                        var dados = JSON.parse(retorno);
                        var qtd_caps = dados.qtd_caps;

                        if (qtd_caps > 0){
                            for(i = 1; i <= qtd_caps; i++){
                                var selected = '';

                                if (i == 1){
                                    selected = 'selected';
                                }

                                $("#sel_capitulo").append('<option value="' + i + '" ' + selected + '>' + i + '</option>');
                            }

                            // Buscar o texto do primeiro caítulo
                            buscarTexto();

                            // Buscar a anotação do primeiro caítulo
                            buscarAnotacao();
                        }
                    });
                }
            }

            function buscarTexto(){
                $("#texto").val('');
                var livro_id = $("#sel_livro option:selected").val();
                var capitulo = $("#sel_capitulo option:selected").val();

                $.ajax({
                    url: '/geral/xhr/biblia/buscarTexto.php?livro_id=' + livro_id + '&capitulo=' + capitulo,
                    type: 'get'
                }).done(function(retorno){
                    if (retorno.startsWith('erro')){
                        var msg = retorno.slice(5);
                        mensagem(msg);
                        return false;
                    }

                    var dados = JSON.parse(retorno);
                    var texto = '';

                    dados.forEach(function(ver){
                        texto += ver.ver_versiculo + '. ' + ver.ver_texto + `\n`;
                    });

                    $("#texto").val(texto);
                });
            }

            function buscarAnotacao(){
                $("#anotacao").val('');
                var livro_id = $("#sel_livro option:selected").val();
                var capitulo = $("#sel_capitulo option:selected").val();

                $.ajax({
                    url: '/geral/xhr/biblia/buscarAnotacao.php?livro_id=' + livro_id + '&capitulo=' + capitulo,
                    type: 'get'
                }).done(function(retorno){
                    if (retorno.startsWith('erro')){
                        var msg = retorno.slice(5);
                        mensagem(msg);
                        return false;
                    }

                    var dados = JSON.parse(retorno);
                    $("#anotacao").val(dados.abi_anotacao);
                });
            }

            function salvarAnotacao(e){
                e.preventDefault();

                var livro_id = $("#sel_livro option:selected").val();
                var capitulo = $("#sel_capitulo option:selected").val();
                var anotacao = $("#anotacao").val();

                if (livro_id == 0 || capitulo == 0 || anotacao == ''){
                    mensagem('Não há nada para gravar.');
                    return false;
                }

                var dados = {
                    livro_id: livro_id,
                    capitulo: capitulo,
                    anotacao: anotacao
                };

                $.ajax({
                    url: '/geral/xhr/biblia/gravarAnotacao.php',
                    type: 'POST',
                    data: dados
                }).done(function(retorno){
                    if (retorno.startsWith('erro')){
                        var msg = retorno.slice(5);
                        mensagem(msg);
                        return false;
                    }

                    $("#msg_gravado").show();

                    setTimeout(function(){
                        $("#msg_gravado").hide();
                    }, 3000);
                });
            }
        </script>
    HTML,
    ["title" => 'Anotações Bíblicas']
);
