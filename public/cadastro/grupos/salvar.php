<?php

use App\CADASTRO\DAO\Grupos;

$gruposDAO = new Grupos();

global $session;
$usuario = $session->get('credentials.default');

$gru_id = $request->post('gru_id', 0);
$novo = ($gru_id == 0);

$record = array(
    'gru_nome' => mb_strtoupper($request->post('gru_nome')),
    'gru_tipo' => $request->post('gru_tipo')
);

if ($novo) {
    $record['gru_usuario'] = $usuario;

    $gru_id = $gruposDAO->insert($record);
} else {
    $count = $gruposDAO->update($gru_id, $record);
}

$session->flash('success', 'Grupo gravada.');
$response->back(-2);
