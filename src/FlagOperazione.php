<?php
namespace Padosoft\TesseraSanitaria;

/**
 * Class FlagOperazione
 * @package Padosoft\TesseraSanitaria
 */
class FlagOperazione
{
    use traits\Enumerable;

    const INSERIMENTO = "I";
    const VARIAZIONE = "V";
    const RIMBORSO = "R";
    const CANCELLAZIONE = "C";
}