<?php

require_once(__DIR__ . '/../header.php');

use App\CADASTRO\DAO\Livros;

$livrosDAO = new Livros();
$id = $request->get('id', 0);
if ($id == 0) {
    echo 'erro|ID não informado';
    exit();
}

$aLivro = $livrosDAO->get($id);
if (empty($aLivro)) {
    echo 'erro|Livro não encontrado';
    exit();
}

echo json_encode($aLivro);
