<?php

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

$caminho = __DIR__ . '/../../../upload/anexos_estudos/' . $aAnexo['anx_entidade_id'] . '/';

/*
$arq = urlencode($caminho . $aAnexo['anx_nome_arquivo']);

$titulo = $aAnexo['anx_descricao'];

$tag = 'img';

$fileParts = pathinfo($arq);

if (strtolower($fileParts['extension']) == 'pdf') {
    $tag = 'iframe';
}

$link = "/geral/abrir/doc.php?arquivo={$arq}&titulo={$titulo}&tag={$tag}";
header('Location: ' . $link);
*/

$file_url = $caminho . $aAnexo['anx_nome_arquivo'];
header('Content-Type: application/octet-stream');
header("Content-Transfer-Encoding: Binary");
header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\"");
readfile($file_url);
