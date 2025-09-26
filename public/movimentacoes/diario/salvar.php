<?php

use App\MOVIMENTACOES\DAO\Diario;
use Funcoes\Helpers\Text;

$diarioDAO = new Diario();

$dia_data = $request->post('dia_data');
$usuario = $session->get('credentials.default');

// Ver se já existe um lançamento para esse dia
$aDiario = $diarioDAO->getDia($dia_data);

$novo = (empty($aDiario));

if ($novo) {
    $record = array(
        'dia_data' => $dia_data,
        'dia_titulo' => Text::caracteresPermitidos($request->post('dia_titulo')),
        'dia_texto' => Text::caracteresPermitidos($request->post('dia_texto')),
        'dia_usuario' => $usuario,
        'dia_data_hora' => date('Y-m-d H:i:s')
    );

    $dia_id = $diarioDAO->insert($record);
} else {
    $dia_id = $aDiario['dia_id'];

    $record = array(
        'dia_titulo' => Text::caracteresPermitidos($request->post('dia_titulo')),
        'dia_texto' => Text::caracteresPermitidos($request->post('dia_texto'))
    );

    $count = $diarioDAO->update($dia_id, $record);
}

$session->flash('success', 'Diário Gravado');
$response->back();
