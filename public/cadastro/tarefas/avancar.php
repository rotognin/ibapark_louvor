<?php

use App\CADASTRO\DAO\Tarefas;

$tarefasDAO = new Tarefas();

$tar_id = $request->get('tar_id');
$status_alvo = $request->get('status_alvo');

$record = array(
    'tar_status' => $status_alvo
);

$count = $tarefasDAO->update($tar_id, $record);

$desc_alvo = $tarefasDAO->getStatus($status_alvo);

$session->flash('success', 'Status da tarefa alterado para ' . $desc_alvo);

$voltar = $request->get('voltar', '');

if ($voltar != '') {
    $response->redirect('/dashboard');
}

$response->back();
