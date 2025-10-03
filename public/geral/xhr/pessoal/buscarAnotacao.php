<?php

require_once(__DIR__ . '/../header.php');

use App\PESSOAL\DAO\Anotacoes;

$anotacoesDAO = new ANotacoes();

$ano_id = $request->get('ano_id', 0);
if ($ano_id == 0){
    echo 'erro|ID não informado';
    exit();
}

$aAnotacao = $anotacoesDAO->get($ano_id);
if(empty($aAnotacao)){
    echo 'erro|Registro não encontrado';
    exit();
}

echo json_encode($aAnotacao);