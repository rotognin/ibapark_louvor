<?php

use Funcoes\Layout\Layout;
use App\Dashboard\DAO\Dashboard;

require_once('header.php');

$usuario = $session->get('credentials.default');

$pageHeader = Layout::pageTitle(
    '<h1 class="m-0 text-dark">' . _('Bem vindo ao Dashboard') . ', ' . $activeUser->data['usu_nome'] . '</h1>'
);

// Buscar últimos programas e programas mais acessados do usuário logado
$dashboardDAO = new Dashboard();

$html = $dashboardDAO->inicio();
//$html .= $dashboardDAO->montarMaisAcessados($usuario);
//$html .= $dashboardDAO->montarUltimosAcessos($usuario);

$html .= $dashboardDAO->tarefasDashboard();

$html .= $dashboardDAO->fechamento();

$response->page(
    <<<HTML
    $pageHeader
    $html
    <script>
        function irFazendo(tar_id){
            confirm('Avançar a tarefa para "Fazendo"?').then(result => {
                if (result.isConfirmed) {
                    window.location.href = '/cadastro/tarefas.php?posicao=avancar&tar_id=' + tar_id + '&status_alvo=2&voltar=dashboard';
                }
            });
        }

        function irFeito(tar_id){
            confirm('Avançar a tarefa para "Feito"?').then(result => {
                if (result.isConfirmed) {
                    window.location.href = '/cadastro/tarefas.php?posicao=avancar&tar_id=' + tar_id + '&status_alvo=4&voltar=dashboard';
                }
            });
        }
    </script>
    HTML,
    ['title' => 'Dashboard']
);
