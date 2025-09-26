<?php

use Funcoes\Layout\Layout as L;
use App\MOVIMENTACOES\DAO\Diario;
use Funcoes\Layout\Form;
use Funcoes\Layout\FormControls as FC;
use Funcoes\Layout\ModalForm;
use Funcoes\Layout\Table;
use Funcoes\Lib\Funcoes\Datas;

$diarioDAO = new Diario();
$datas = new Datas();

$pageHeader = L::pageTitle('<h1 class="m-0 text-dark">Diário</h1>');

// Filtros
$formFiltro = new Form();
$formFiltro->setTitle('<i class="fas fa-filter"></i> Filtros');
$formFiltro->setForm('action="" method="GET"');
$formFiltro->setCollapsable(true);
$formFiltro->setCollapsed(false);
$formFiltro->setActions(L::submit('Filtrar', 'fas fa-filter'));
$formFiltro->setInternalPadding('pt-1 pb-1');

$mes = $request->get('mes', intval(date('m')));

$filtro_mes = FC::select(
    'Mês',
    'mes',
    $datas->nomeMes(),
    $mes,
    [
        'class' => 'form-control form-control-sm',
        'div_class' => 'col-md-2'
    ]
);

$ano = $request->get('ano', date('Y'));

$filtro_ano = FC::input('Ano', 'ano', $ano, [
    'class' => 'form-control form-control-sm',
    'div_class' => 'col-md-1'
]);

$formFiltro->setFields([
    ['<div class="row">' . $filtro_mes . $filtro_ano . '</div>']
]);

$table = new Table('mes-ano');
$table->setBordered(true);
$table->setFooter(false);
$table->setStriped(true);
$table->setSize('sm');
$table->addHeader([
    'cols' => [
        ['value' => 'Dia', 'attrs' => ['class' => 'text-center', 'style' => 'width:250px']],
        ['value' => 'Título', 'attrs' => ['class' => 'text-center']],
        ['value' => 'Editar', 'attrs' => ['class' => 'text-center', 'style' => 'width:70px']],
    ],
]);

// Buscar os dias do mês escolhido
$qtd_dias = $datas->qtdDiasMes($datas->nro_sigla[$mes], $ano);

$dia_atual = date('d');
$mes_atual = date('m');
$ano_atual = date('Y');

for ($dia = 1; $dia <= $qtd_dias; $dia++) {
    $antes = '';
    $depois = '';

    if ($dia == $dia_atual && $mes == $mes_atual && $ano == $ano_atual) {
        $antes = '<b>';
        $depois = '</b>';
    }

    $dia_diario = $datas->digitos($dia);
    $mes_diario = $datas->digitos($mes);

    $aDiario = $diarioDAO->getDia($ano . '-' . $datas->digitos($mes) . '-' . $datas->digitos($dia));
    $dia_titulo = '';

    if (!empty($aDiario)) {
        $dia_titulo = $aDiario['dia_titulo'];
    }

    $table->addRow([
        'cols' => [
            ['value' => $antes . $datas->digitos($dia) . '/' . $datas->nro_sigla[$mes] . '/' . $ano . ' - ' . $datas->montarDia($dia, $mes, $ano) . $depois, 'attrs' => ['class' => 'text-left']],
            ['value' => $dia_titulo, 'attrs' => ['class' => 'text-left']],
            ['value' => L::button('', "editarDia('{$ano}-{$mes_diario}-{$dia_diario}')", 'Editar', 'fas fa-edit', 'outline-info', 'sm'), 'attrs' => ['class' => 'text-center']]
        ]
    ]);
}

$html = $table->html();

$form = new ModalForm('modal-diario');
$form->setForm('id="form-diario" method="POST" action="?posicao=salvar"');
$form->setTitle('Diário - <span id="data-diario"></span>');
$form->setModalSize('modal-lg');

$form->addHidden(FC::hidden('dia_data', ''));

$modal_titulo = FC::input('Título', 'dia_titulo', '', [
    'class' => 'form-control form-control-sm',
    'div_class' => 'col-md-10'
]);

$modal_diario = FC::textarea('Texto', 'dia_texto', '', [
    'class' => 'form-control form-control-sm',
    'div_class' => 'col-md-12',
    'rows' => 10
]);

$form->setFields([
    ['<div class="row">' . $modal_titulo . '</div>'],
    ['<div class="row">' . $modal_diario . '</div>']
]);

$form->setActions(
    L::button('Fechar', '', '', '', 'secondary', 'sm', 'data-dismiss="modal"') . ' ' .
        L::button('Gravar', 'gravarDiario(event)', 'Gravar', 'fas fa-check', 'primary')
);

$response->page(
    <<<HTML
        {$pageHeader}
        <div class="content">
            <div class="container-fluid pb-1">
                {$formFiltro->html()}
                {$html}
            </div>
            {$form->html()}
        </div>

        <!--script src="https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/gsap.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/ScrollTrigger.min.js"></script-->

        <script>
            /*
            function iniciarGsap(){
                let telaAtual = window.innerWidth;

                let slider = document.querySelector(".slider");
                let sections = gsap.utils.toArray(".secao");

                ScrollTrigger.killAll();

                let sliderTl = gsap.timeline({
                defaults: {
                        ease: "none"
                    },
                    scrollTrigger:{
                        trigger: slider,
                        pin: true,
                        scrub: 2,
                        end: () => {
                    if(telaAtual > 1024){
                        return "+=" + (slider.offsetWidth * .3)
                    }else{
                        return "+=" + (slider.offsetWidth * .8)
                    }
                }
                    }
            })

                sliderTl.to(slider, {
                    x: ((sections.length - 1) * -100) + "vw"
                })
            }

            addEventListener("DOMContentLoaded", iniciarGsap)

            //Meus amigos, essa função abaixo faz com que a cada redimensionamento de tela, a página recarregue sem cache, para evitar bugs no scrollHorizontal

            window.addEventListener('resize', function() {
                window.location.href = window.location.href + '?v=' + Date.now();
            });
            */


            function editarDia(data_sel){
                $("input[name='dia_data']").val(data_sel);

                var aData = data_sel.split('-');
                var dia = aData[2];
                var mes = aData[1];
                var ano = aData[0];

                $("#data-diario").html(dia + '/' + mes + '/' + ano);
                $("#dia_titulo").val('');
                $("#dia_texto").val('');

                $("#modal-diario").modal();

                $.ajax({
                    url: '/geral/xhr/diario/buscarDiario.php?dia_data=' + data_sel,
                    type: 'get'
                }).done(function(retorno){
                    var dados = JSON.parse(retorno);

                    $("#dia_titulo").val(dados.dia_titulo);
                    $("#dia_texto").val(dados.dia_texto);
                });
            }

            function gravarDiario(e){
                e.preventDefault();

                var titulo = $("#dia_titulo").val();
                var texto = $("#dia_texto").val();

                if (titulo == '' || texto == ''){
                    mensagem('Informe o título e o texto');
                    return false;
                }

                var form = $("#form-diario");
                form.submit();
            }
        </script>
    HTML,
    ["title" => 'Diário']
);
