<?php

use App\CADASTRO\DAO\Integrantes;

$integrantesDAO = new Integrantes();

$int_id = $request->get('int_id', 0);
if ($int_id == 0) {
    $session->flash('error', 'ID não informado');
    $response->back();
}

$aAnotacao = $integrantesDAO->get($int_id);
if (empty($aAnotacao)) {
    $session->flash('error', 'Cadastro não encontrado');
    $response->back();
}

$integrantesDAO->delete($int_id);

$session->flash('success', 'Registro excluído');
$response->back();
