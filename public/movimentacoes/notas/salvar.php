<?php

use App\MOVIMENTACOES\DAO\Notas;
use Funcoes\Helpers\Text;

$notasDAO = new Notas();

global $session;
$usuario = $session->get('credentials.default');

$not_id = $request->post('id', 0);
$novo = ($not_id == 0);

$record = array(
    'not_titulo' => mb_strtoupper(Text::caracteresPermitidos($request->post('titulo'))),
    'not_texto' => Text::caracteresPermitidos($request->post('texto')),
    'not_status' => 'A'
);

if ($novo) {
    $record['not_usuario'] = $usuario;
    $record['not_id_pai'] = $request->post('id_pai');

    $not_id = $notasDAO->insert($record);
} else {
    $count = $notasDAO->update($not_id, $record);
}

$session->flash('success', 'Registro gravado.');
//$response->back(-2);

$response->redirect('?posicao=lista');
