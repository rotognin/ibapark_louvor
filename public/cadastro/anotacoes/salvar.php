<?php

use App\CADASTRO\DAO\Anotacoes;
use Funcoes\Helpers\Text;

$anotacoesDAO = new Anotacoes();

global $session;
$usuario = $session->get('credentials.default');

$ano_id = $request->post('ano_id', 0);
$novo = ($ano_id == 0);

$record = array(
    'ano_titulo' => Text::caracteresPermitidos($request->post('ano_titulo')),
    'ano_texto' => Text::caracteresPermitidos($request->post('ano_texto'))
);

if ($novo) {
    $record['ano_data_hora'] = date('Y-m-d H:i:s');
    $record['ano_usuario'] = $usuario;
    $record['ano_status'] = 'A';

    $ano_id = $anotacoesDAO->insert($record);
} else {
    $count = $anotacoesDAO->update($ano_id, $record);
}

$session->flash('success', 'Anotação gravada.');
$response->back(-2);
