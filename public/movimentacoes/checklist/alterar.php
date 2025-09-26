<?php

use App\MOVIMENTACOES\DAO\Checklist;

$checklistDAO = new Checklist();

$chk_id = $request->get('chk_id', 0);
if ($chk_id == 0) {
    $session->flash('error', 'ID não informado');
    $response->back();
}

$acao = $request->get('acao', '');
if ($acao == '') {
    $session->flash('error', 'Ação não informaca');
    $response->back();
}

$record = array();

if ($acao == 'marcar') {
    $record['chk_marcado'] = 'S';
    $record['chk_marcado_em'] = date('Y-m-d H:i:s');
}

if ($acao == 'desmarcar') {
    $record['chk_marcado'] = 'N';
}

if (!empty($record)) {
    $checklistDAO->update($chk_id, $record);
}

$session->flash('success', 'Item atualizado');
$response->redirect('?posicao=lista');
