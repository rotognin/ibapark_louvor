<?php

/**
 * Não está sendo usado
 */

require_once(__DIR__ . '/../header.php');

use App\MOVIMENTACOES\DAO\Anexos;
use App\MOVIMENTACOES\DAO\Estudos;

$anexosDAO = new Anexos();
$estudosDAO = new Estudos();

$anx_id = $request->get('anx_id', 0);
if ($anx_id == 0) {
    echo 'erro';
    exit();
}

$aAnexo = $anexosDAO->get($anx_id);

$caminho = '/../upload/anexos_estudos/' . $aAnexo['anx_entidade_id'] . '/';

echo $caminho . $aAnexo['anx_nome_arquivo'];
