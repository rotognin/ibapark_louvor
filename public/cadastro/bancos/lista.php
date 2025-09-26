<?php

use Funcoes\Layout\Layout as L;
use App\CADASTRO\DAO\Bancos;
use App\CADASTRO\Datatables\DatatableBancos;
use Funcoes\Layout\Form;
use Funcoes\Layout\FormControls as FC;
use Funcoes\Layout\Datatable;

$bancosDAO = new Bancos();

$pageHeader = L::pageTitle('<h1 class="m-0 text-dark">Bancos</h1>', L::linkButton('Novo Banco', '?posicao=form', '', 'fas fa-plus', 'primary'));

// Filtros
$form = new Form();
$form->setTitle('<i class="fas fa-filter"></i> Filtros');
$form->setForm('action="" method="GET"');
$form->setCollapsable(false);
//$form->setActions(L::submit('Filtrar', 'fas fa-filter'));
$form->setInternalPadding('pt-1 pb-1');

$table = new Datatable(DatatableBancos::class);

$html = $table->html();

$response->page(
    <<<HTML
        {$pageHeader}
        <div class="content">
            <div class="container-fluid pb-1">
                
                {$html}
            </div>
        </div>
        <script>
            function alterarStatus(link, msg){
                confirm('Alterar o status do banco para ' + msg + '?').then(result => {
                    if (result.isConfirmed) {
                        window.location.href = link;
                    }
                });
            }
        </script>
    HTML,
    ["title" => 'Bancos']
);
