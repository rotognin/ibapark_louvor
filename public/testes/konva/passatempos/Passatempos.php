<?php

namespace Passatempos;

class Passatempos
{
    private $classe;
    private array $info = [];

    public function __construct($tipo, $dificuldade, $numero)
    {
        $classe = $tipo . '_' . $dificuldade . '_' . $numero;
        require_once(__DIR__ . '/' . $classe . '.php');
        $classe = 'Passatempos\\' . $classe;
        $this->classe = new $classe();

        $this->montarInfo();
    }

    private function montarInfo()
    {
        $this->info = array(
            'linhas' => $this->classe->getLinhas(),
            'colunas' => $this->classe->getColunas(),
            'qtd_palavras' => $this->classe->getQtdPalavras(),
            'tema' => $this->classe->getTema(),
            'dificuldade' => $this->classe->getDificuldade(),
            'id' => $this->classe->getId(),
            'palavras' => $this->classe->getPalavras(),
            'diagrama' => $this->classe->getDiagrama()
        );
    }

    public function getInfo()
    {
        return $this->info;
    }
}
