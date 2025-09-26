<?php

require_once(__DIR__ . '/../header.php');

use App\CADASTRO\DAO\Versiculos;

$versiculosDAO = new Versiculos();

$livro_id = $request->get('livro_id', 0);
$capitulo = $request->get('capitulo', 0);

if ($livro_id == 0 || $capitulo == 0) {
    echo 'erro|Livro e capítulo não informados';
    exit();
}

$where = array('');
$where[0] = ' AND ver_livro = ? AND ver_capitulo = ?';
$where[1][] = $livro_id;
$where[1][] = $capitulo;

$versiculos = $versiculosDAO->getArray($where, 'ver_versiculo ASC');

echo json_encode($versiculos);
