<?php

require_once(__DIR__ . '/../header.php');

use App\MOVIMENTACOES\DAO\Estudos;
use App\MOVIMENTACOES\DAO\Anexos;

$estudosDAO = new Estudos();
$anexosDAO = new Anexos();

$bib_id = $request->get('bib_id', 0);
if ($bib_id == 0) {
    echo 'erro|ID do Estudo não informado';
    exit();
}

$aEstudo = $estudosDAO->get($bib_id);
if (empty($aEstudo)) {
    echo 'erro|Estudo não encontrado';
    exit();
}

// Buscar os Anexos
$where = array('');
$where[0] = ' AND anx_entidade = ? AND anx_entidade_id = ?';
$where[1][] = $estudosDAO->tipo_anexo;
$where[1][] = $bib_id;

$aAnexos = $anexosDAO->getArray($where);

$aEstudo['anexos'] = $aAnexos;

echo json_encode($aEstudo);
