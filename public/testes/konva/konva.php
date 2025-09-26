<?php

/**
 * https://konvajs.org/docs/index.html
 */

require_once('../header.php');

$script = file_get_contents('konvatest.js');

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
