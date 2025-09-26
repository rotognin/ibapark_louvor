<?php

require_once(__DIR__ . '/../header.php');

use App\MOVIMENTACOES\DAO\AnotacaoBiblica;

$anotacaoDAO = new AnotacaoBiblica();

global $session;
$usuario = $session->get('credentials.default');

$livro_id = $request->get('livro_id', 0);
$capitulo = $request->get('capitulo', 0);

if ($livro_id == 0 || $capitulo == 0) {
    echo 'erro|Livro e capítulo não informados';
    exit();
}

$anotacao = $anotacaoDAO->getAnotacao($livro_id, $capitulo);

echo json_encode(['abi_anotacao' => $anotacao]);
