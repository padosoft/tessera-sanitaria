<?php
namespace Padosoft\TesseraSanitaria;

/**
 * Class TipoSpesa
 * @package Padosoft\TesseraSanitaria
 */
class TipoSpesa
{
    use traits\Enumerable;

    const TICKET = "TK";
    const FARMACO = "FC";
    const FARMACO_VETERINARIO = "FV";
    const DISPOSITIVO_MEDICO = "AD";
    const ANALISI_STRUMENTALI = "AS";
    const VISITE_INTROMOENIA = "SR";
    const CURE_TERMALI = "CT";
    const PROTESI = "PI";
    const CHIRURGIA_ESTETICA = "IC";
    const ALTRE_SPESE = "AA";
}