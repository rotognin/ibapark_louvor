<?php

use App\MOVIMENTACOES\DAO\Checklist;
use Funcoes\Helpers\Text;

$checklistDAO = new Checklist();

global $session;
$usuario = $session->get('credentials.default');

$chk_id = $request->post('chk_id', 0);
$novo = ($chk_id == 0);

$record = array(
    'chk_descricao' => Text::caracteresPermitidos($request->post('chk_descricao')),
    'chk_usuario' => $usuario
);

if ($novo) {
    $record['chk_criado_em'] = date('Y-m-d H:i:s');
    $record['chk_marcado'] = 'N';

    $chk_id = $checklistDAO->insert($record);
} else {
    $count = $checklistDAO->update($chk_id, $record);
}

$session->flash('success', 'Item gravado.');
$response->redirect('?posicao=lista');
