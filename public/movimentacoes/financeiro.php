<?php

require_once('header.php');

$posicao = $request->get('posicao', 'lista');

$destino = 'financeiro/' . $posicao . '.php';

if (!file_exists($destino)) {
    $session->flash('error', _('Arquivo não encontrado: ' . $destino));
    return $response->back();
}

require_once($destino);
