<?php

use App\CADASTRO\DAO\Bancos;

$bancosDAO = new Bancos();

$ban_id = $request->get('ban_id');
$status_alvo = $request->get('status_alvo');

$record = array(
    'ban_status' => $status_alvo
);

$count = $bancosDAO->update($ban_id, $record);

$desc_alvo = $bancosDAO->getStatus($status_alvo);

$session->flash('success', 'Status do banco alterado para ' . $desc_alvo);
$response->back();
