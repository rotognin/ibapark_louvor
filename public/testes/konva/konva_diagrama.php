<?php

$diagrama = $info['diagrama'];

$script_add = <<<JS
    const tituloTexto2 = new Konva.Text({
        x: stage.width() / 2,
        y: 100,
        text: `{$diagrama}`,
        fontSize: 30,
        fontFamily: 'Arial',
        fill: 'black'
    });

    tituloTexto2.offsetX(tituloTexto2.width() / 2);

    layer.add(tituloTexto2);
JS;
