<?php

use App\CADASTRO\DAO\Bancos;

$bancosDAO = new Bancos();

global $session;
$usuario = $session->get('credentials.default');

$ban_id = $request->post('ban_id', 0);
$novo = ($ban_id == 0);

$record = array(
    'ban_descricao' => mb_strtoupper($request->post('ban_descricao')),
    'ban_sigla' => mb_strtoupper($request->post('ban_sigla')),
    'ban_tipo' => $request->post('ban_tipo'),
    'ban_especificacao' => $request->post('ban_especificacao')
);

if ($novo) {
    $record['ban_status'] = 'A';
    $record['ban_usuario'] = $usuario;
    $record['ban_data_hora'] = date('Y-m-d H:i:s');

    $ban_id = $bancosDAO->insert($record);
} else {
    $count = $bancosDAO->update($ban_id, $record);
}

$session->flash('success', 'Banco gravado.');
$response->back(-2);
