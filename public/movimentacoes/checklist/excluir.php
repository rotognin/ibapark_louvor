<?php

use App\MOVIMENTACOES\DAO\Checklist;

$checklistDAO = new Checklist();

$chk_id = $request->get('chk_id', 0);
if ($chk_id == 0) {
    $session->flash('error', 'ID não informado');
    $response->back();
}

$checklistDAO->delete($chk_id);

$session->flash('success', 'Item excluído');
$response->redirect('?posicao=lista');
