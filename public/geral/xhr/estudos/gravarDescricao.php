<?php

require_once(__DIR__ . '/../header.php');

use App\MOVIMENTACOES\DAO\Anexos;

$anexosDAO = new Anexos();

$anx_id = $request->get('anx_id', 0);
if ($anx_id == 0) {
    echo 'erro';
    exit();
}

$descricao = $request->get('descricao', '');

$count = $anexosDAO->update($anx_id, ['anx_descricao' => $descricao]);

if (!$count) {
    echo 'erro';
} else {
    echo 'OK';
}

exit();
