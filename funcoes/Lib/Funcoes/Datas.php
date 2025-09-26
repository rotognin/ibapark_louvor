<?php

/**
 * Coleções de arrays e funções relacionadas a montagens de datas (dias da semana, meses. etc...)
 */

namespace Funcoes\Lib\Funcoes;

class Datas
{
    public array $dias_semana = array(
        'dom' => 'Domingo',
        'seg' => 'Segunda-feira',
        'ter' => 'Terça-feira',
        'qua' => 'Quarta-feira',
        'qui' => 'Quinta-feira',
        'sex' => 'Sexta-feira',
        'sab' => 'Sábado'
    );

    public array $meses_ano = array(
        'jan' => 'Janeiro',
        'fev' => 'Fevereiro',
        'mar' => 'Março',
        'abr' => 'Abril',
        'mai' => 'Maio',
        'jun' => 'Junho',
        'jul' => 'Julho',
        'ago' => 'Agosto',
        'set' => 'Setembro',
        'out' => 'Outubro',
        'nov' => 'Novembro',
        'dez' => 'Dezembro'
    );

    public array $numero_mes = array(
        1 => 'Janeiro',
        2 => 'Fevereiro',
        3 => 'Março',
        4 => 'Abril',
        5 => 'Maio',
        6 => 'Junho',
        7 => 'Julho',
        8 => 'Agosto',
        9 => 'Setembro',
        10 => 'Outubro',
        11 => 'Novembro',
        12 => 'Dezembro'
    );

    public array $dias_mes = array(
        'jan' => 31,
        'fev' => 28,
        'mar' => 31,
        'abr' => 30,
        'mai' => 31,
        'jun' => 30,
        'jul' => 31,
        'ago' => 31,
        'set' => 30,
        'out' => 31,
        'nov' => 30,
        'dez' => 31,
        'fev2' => 29
    );

    public array $nro_sigla = array(
        1 => 'jan',
        2 => 'fev',
        3 => 'mar',
        4 => 'abr',
        5 => 'mai',
        6 => 'jun',
        7 => 'jul',
        8 => 'ago',
        9 => 'set',
        10 => 'out',
        11 => 'nov',
        12 => 'dez'
    );

    public function nomeMes(int $numero_mes = 0): string|array
    {
        return ($numero_mes == 0) ? $this->numero_mes : $this->numero_mes[$numero_mes];
    }

    public function siglasDiasSemana(): array
    {
        $siglas = array_map(function ($dia) {
            return $dia;
        }, array_keys($this->dias_semana));

        return $siglas;
    }

    public function montarDia($dia, $mes, $ano)
    {
        $diasDaSemana = array(
            0 => 'domingo',
            1 => 'segunda-feira',
            2 => 'terça-feira',
            3 => 'quarta-feira',
            4 => 'quinta-feira',
            5 => 'sexta-feira',
            6 => 'sábado'
        );

        $timestamp = mktime(0, 0, 0, $mes, $dia, $ano);
        $diaDaSemana = date('w', $timestamp);

        return $diasDaSemana[$diaDaSemana];
    }

    public function siglasMesesAno(): array
    {
        $siglas = array_map(function ($mes) {
            return $mes;
        }, array_keys($this->meses_ano));

        return $siglas;
    }

    public function qtdDiasMes(string $mes, int $ano = 0): int
    {
        if ($mes == '') {
            return 0;
        }

        if ($mes == 'fev') {
            $bissexto = false;

            if ($ano > 0) {
                $bissexto = (($ano % 4) == 0);
            }

            return ($bissexto) ? $this->dias_mes['fev2'] : $this->dias_mes['fev'];
        }

        return $this->dias_mes[$mes] ?? 0;
    }

    public function diasSemana(string $sigla = '', bool $prefixoFeira = true): array|string
    {
        if ($prefixoFeira) {
            return ($sigla == '') ? $this->dias_semana : $this->dias_semana[$sigla];
        }

        $arrayRet = array();

        foreach ($this->dias_semana as $key => $value) {
            $aDia = explode('-', $value);
            $arrayRet[$key] = $aDia[0];
        }

        return ($sigla == '') ? $arrayRet : $arrayRet[$sigla];
    }

    public function mesesAno(string $mes = ''): array|string
    {
        return ($mes == '') ? $this->meses_ano : $this->meses_ano[$mes];
    }

    public function digitos($numero, $digitos = 2)
    {
        return str_pad($numero, $digitos, '0', STR_PAD_LEFT);
    }
}
