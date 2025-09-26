<?php

use App\MOVIMENTACOES\DAO\Financeiro;
use Funcoes\Helpers\Text;
use Funcoes\Helpers\Format;

$financeiroDAO = new Financeiro();

global $session;
$usuario = $session->get('credentials.default');

$fin_id = $request->post('fin_id', 0);
$novo = ($fin_id == 0);

$record = array(
    'fin_descricao' => mb_strtoupper(Text::caracteresPermitidos($request->post('fin_descricao'))),
    'fin_valor' => Format::converterDecimal($request->post('fin_valor')),
    'fin_tipo' => $request->post('fin_tipo'),
    'fin_data' => Format::sqlDatetime($request->post('fin_data'), 'd/m/Y', 'Y-m-d'),
    'fin_fonte' => $request->post('fin_fonte')
);

if ($novo) {
    $record['fin_data_hora_cadastro'] = date('Y-m-d H:i:s');
    $record['fin_usuario'] = $usuario;

    $fin_id = $financeiroDAO->insert($record);
} else {
    $count = $financeiroDAO->update($fin_id, $record);
}

$session->flash('success', 'Registro gravado.');
$response->back(-2);
