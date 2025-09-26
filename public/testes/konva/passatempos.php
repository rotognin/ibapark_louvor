<?php

require_once(__DIR__ . '/../../../vendor/autoload.php');

//require_once('../header.php');

use Passatempos\Passatempos;

$passatempo = new Passatempos('Palavras', '1', '01');
$info = $passatempo->getInfo();

/*
echo '<pre>';
var_dump($passa->getInfo());
echo '</pre>';
*/

$script = file_get_contents('konva_passatempo.js');
$script .= file_get_contents('konva_titulo.js');

require('konva_diagrama.php');
$script .= $script_add;

echo <<<HTML
    <html>

    <head>
        <title>Teste konva</title>
        <script src="https://unpkg.com/konva@9/konva.min.js"></script>
    </head>

    <body>
        <div id="container"></div>
        <script>{$script}</script>
    </body>

    </html>
HTML;
