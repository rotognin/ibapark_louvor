<?php

require_once('../header.php');

/**
 * Usar o "motorzinho" de gerar tree view
 */

echo <<<TREE
<pre>
<div class="content">
    <table class="table">
    + Motos
    - Carros
        + VW
        - Honda
            . City
            . Accord
            . Civic
        + BYD
    + Caminhões
    </table>
</div>
</pre>
TREE;
