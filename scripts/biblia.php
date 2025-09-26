<?php

require('../app/bootstrap.php');

use App\CADASTRO\DAO\Livros;
use App\CADASTRO\DAO\Versiculos;

$livrosDAO = new Livros();
$versiculosDAO = new Versiculos();

$arquivo = __DIR__ . '/../docs/nvi.min.xml';

echo PHP_EOL . ' ------------------------------------------- ' . PHP_EOL;

if (file_exists($arquivo)) {
    $xml = simplexml_load_file($arquivo);
    //print_r($xml);

    //print_r($xml->book[0]->attributes['name']);

    foreach ($xml->book as $key => $val) {
        $capitulo = 0;
        $livro = '';
        $abrev = '';
        $id_livro = 0;
        $obj = $val->attributes();

        foreach ($obj as $k1 => $v1) {
            if ($k1 == 'name') {
                //echo $v1 . PHP_EOL;
                $livro = $v1;
                echo 'LIVRO ' . $livro . PHP_EOL;
            }

            if ($k1 == 'abbrev') {
                $abrev = $v1;

                $aLivro = $livrosDAO->getAbrev($abrev);
                $id_livro = $aLivro['id'];
            }
        }

        foreach ($val->c as $k1 => $v1) {
            $capitulo++;
            //echo '----- Capítulo ' . $capitulo . PHP_EOL . PHP_EOL;

            // Buscar os versículos
            //echo $v1->v[0] . PHP_EOL;
            $versiculo = 0;

            foreach ($v1->v as $ver => $texto) {
                $versiculo++;
                //echo $versiculo . ': ' . $texto . PHP_EOL;
                $record = array(
                    'ver_livro' => $id_livro,
                    'ver_capitulo' => $capitulo,
                    'ver_versiculo' => $versiculo,
                    'ver_texto' => $texto
                );

                $versiculosDAO->insert($record);
            }
        }

        echo '............................................' . PHP_EOL;
    }
} else {
    echo 'Arquivo não encontrado';
}

echo PHP_EOL . ' ------------------------------------------- ACABOU' . PHP_EOL;
