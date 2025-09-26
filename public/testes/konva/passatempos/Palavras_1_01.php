<?php

/**
 * palavras = Caça Palavras
 * 1 - Fácil
 * 01 - ID do passatempo
 */

namespace Passatempos;

class Palavras_1_01
{
    private int $linhas = 10;
    private int $colunas = 10;
    private int $qtd_palavras = 5;

    private string $tema = 'Informática';
    private string $dificuldade = 'Fácil';
    private string $id = 'P_1_01';

    private array $palavras = array(
        '1' => 'NOTEBOOK',
        '2' => 'MOUSE',
        '3' => 'TECLADO',
        '4' => 'DELETE',
        '5' => 'INTERNET'
    );

    private string $diagrama;

    public function __construct()
    {
        $this->diagrama = <<<DIAG
            JDOAYTPSLM
            MOUSETAGOT
            PROSIAYRUE
            AKQIELMDKC
            QPOTLAMVNL
            DKDELETESA
            TENRETNILD
            XNOTEBOOKO
            JSOAPQLWOS
            BAJSKWIQOX
        DIAG;
    }

    public function getLinhas()
    {
        return $this->linhas;
    }

    public function getColunas()
    {
        return $this->colunas;
    }

    public function getQtdPalavras()
    {
        return $this->qtd_palavras;
    }

    public function getTema()
    {
        return $this->tema;
    }

    public function getDificuldade()
    {
        return $this->dificuldade;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getPalavras()
    {
        return $this->palavras;
    }

    public function getDiagrama()
    {
        return $this->diagrama;
    }
}
