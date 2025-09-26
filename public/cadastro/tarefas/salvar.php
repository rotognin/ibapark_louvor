<?php

use App\CADASTRO\DAO\Tarefas;
use Funcoes\Helpers\Format;
use Funcoes\Helpers\Text;

$tarefasDAO = new Tarefas();

global $session;
$usuario = $session->get('credentials.default');

$tar_id = $request->post('tar_id', 0);
$novo = ($tar_id == 0);

$record = array(
    'tar_descricao' => mb_strtoupper(Text::caracteresPermitidos($request->post('tar_descricao'))),
    'tar_data' => Format::sqlDatetime($request->post('tar_data'), 'd/m/Y', 'Y-m-d'),
    'tar_data_limite' => Format::sqlDatetime($request->post('tar_data_limite'), 'd/m/Y', 'Y-m-d'),
    'tar_usuario' => $usuario
);

if ($novo) {
    $record['tar_status'] = 1;
    $tar_id = $tarefasDAO->insert($record);
} else {
    $count = $tarefasDAO->update($tar_id, $record);
}

$session->flash('success', 'Tarefa gravada.');
$response->back(-2);
