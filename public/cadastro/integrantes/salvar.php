<?php

use App\CADASTRO\DAO\Integrantes;

$integrantesDAO = new Integrantes();

global $session;
$usuario = $session->get('credentials.default');

$int_id = $request->post('int_id', 0);
$novo = ($int_id == 0);

$record = array(
    'int_nome' => $request->post('int_nome'),
    'int_usuario' => $request->post('int_usuario'),
    'int_observacoes' => $request->post('int_observacoes'),
    'int_ativo' => $request->post('int_ativo')
);

if ($novo) {
    $record['int_criado_em'] = date('Y-m-d H:i:s');
    $record['int_criado_por'] = $usuario;

    $int_id = $integrantesDAO->insert($record);
} else {
    $record['int_alterado_em'] = date('Y-m-d H:i:s');
    $record['int_alterado_por'] = $usuario;

    $count = $integrantesDAO->update($int_id, $record);
}

$session->flash('success', 'Integrante gravado.');
$response->back(-2);
