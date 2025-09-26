<?php

use App\MOVIMENTACOES\DAO\Estudos;
use Funcoes\Helpers\Text;

$estudosDAO = new Estudos();

$bib_id = $request->post('bib_id');
$novo = $bib_id == 0;
$usuario = $session->get('credentials.default');

if ($novo) {
    $record = array(
        'bib_titulo' => Text::caracteresPermitidos($request->post('bib_titulo')),
        'bib_referencia' => Text::caracteresPermitidos($request->post('bib_referencia')),
        'bib_texto' => Text::caracteresPermitidos($request->post('bib_texto')),
        'bib_grupo_id' => $request->post('bib_grupo_id'),
        'bib_usuario' => $usuario,
        'bib_data_hora' => date('Y-m-d H:i:s')
    );

    $bib_id = $estudosDAO->insert($record);
} else {
    $record = array(
        'bib_titulo' => Text::caracteresPermitidos($request->post('bib_titulo')),
        'bib_referencia' => Text::caracteresPermitidos($request->post('bib_referencia')),
        'bib_texto' => Text::caracteresPermitidos($request->post('bib_texto')),
        'bib_grupo_id' => $request->post('bib_grupo_id')
    );

    $count = $estudosDAO->update($bib_id, $record);
}

$session->flash('success', 'Estudo Bíblico Gravado');
$response->back();
