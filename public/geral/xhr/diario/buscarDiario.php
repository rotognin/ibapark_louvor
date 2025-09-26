<?php

require_once(__DIR__ . '/../header.php');

use App\MOVIMENTACOES\DAO\Diario;

$diarioDAO = new Diario();

$array = array(
    'dia_titulo' => '',
    'dia_texto' => ''
);

$data = $request->get('dia_data', '');
if ($data == '') {
    echo json_encode($array);
    exit;
}

$aDiario = $diarioDAO->getDia($data);

if (!empty($aDiario)) {
    $array['dia_titulo'] = $aDiario['dia_titulo'];
    $array['dia_texto'] = $aDiario['dia_texto'];
}

echo json_encode($array);
