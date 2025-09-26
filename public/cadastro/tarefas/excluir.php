<?php

use App\CADASTRO\DAO\Tarefas;

$tarefasDAO = new Tarefas();

$tar_id = $request->get('tar_id', 0);
if ($tar_id == 0) {
    $session->flash('error', 'ID não informado');
    $response->back();
}

$aTarefa = $tarefasDAO->get($tar_id);
if (empty($aTarefa)) {
    $session->flash('error', 'Cadastro não encontrado');
    $response->back();
}

$tarefaDAO->delete($tar_id);

$session->flash('success', 'Registro excluído');
$response->back();
