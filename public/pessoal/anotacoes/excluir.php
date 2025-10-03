<?php

use App\PESSOAL\DAO\Anotacoes;

$anotacoesDAO = new Anotacoes();

$ano_id = $request->get('ano_id', 0);
if ($ano_id == 0) {
    $session->flash('error', 'ID não informado');
    $response->back();
}

$aAnotacao = $anotacoesDAO->get($ano_id);
if (empty($aAnotacao)) {
    $session->flash('error', 'Cadastro não encontrado');
    $response->back();
}

$anotacoesDAO->delete($ano_id);

$session->flash('success', 'Registro excluído');
$response->back();
