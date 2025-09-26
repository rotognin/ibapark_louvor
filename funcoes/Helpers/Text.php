<?php

namespace Funcoes\Helpers;

class Text
{
    public static function caracteresPermitidos(string $texto)
    {
        $permitidos = array(
            '&amp;' => '&',
            '&#039;' => "'",
            '&quot;' => '"'
        );

        foreach ($permitidos as $antes => $depois) {
            $novo = str_replace($antes, $depois, $texto);
        }

        return $novo;
    }
}
