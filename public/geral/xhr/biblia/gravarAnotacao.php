<?php

require_once(__DIR__ . '/../header.php');

use App\MOVIMENTACOES\DAO\AnotacaoBiblica;

$anotacaoDAO = new AnotacaoBiblica();

global $session;
$usuario = $session->get('credentials.default');

$livro_id = $request->post('livro_id', 0);
$capitulo = $request->post('capitulo', 0);

if ($livro_id == 0 || $capitulo == 0) {
    echo 'erro|Livro e capítulo não informados';
    exit();
}

$anotacao = $request->post('anotacao', '');

$where = array('');
$where[0] = ' AND abi_livro = ? AND abi_capitulo = ? AND abi_usuario = ?';
$where[1][] = $livro_id;
$where[1][] = $capitulo;
$where[1][] = $usuario;

$aAnotacao = $anotacaoDAO->getArray($where);

$abi_id = 0;

if (!empty($aAnotacao)) {
    $abi_id = $aAnotacao[0]['abi_id'];
}

if ($abi_id == 0) {
    // Se não existir o registro
    $registro = array(
        'abi_usuario' => $usuario,
        'abi_livro' => $livro_id,
        'abi_capitulo' => $capitulo,
        'abi_gravado_em' => date('Y-m-d H:i:s'),
        'abi_anotacao' => $anotacao
    );

    $abi_id = $anotacaoDAO->insert($registro);
} else {
    $registro = array(
        'abi_anotacao' => $anotacao
    );

    $count = $anotacaoDAO->update($abi_id, $registro);
}

echo 'OK';
