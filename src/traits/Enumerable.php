<?php

namespace Padosoft\TesseraSanitaria\traits;

/**
 * Class Enumerable
 * @package Padosoft\TesseraSanitaria\traits
 */
trait Enumerable
{
    /**
     * @return array
     */
    public static function getCostants()
    {
        $oClass = new \ReflectionClass(__CLASS__);
        return $oClass->getConstants();
    }

    /**
     * @param $valore
     *
     * @return bool
     */
    public static function isValidValue($valore)
    {
        return in_array($valore, self::getCostants(), null);
    }

    /**
     * @param string $separator
     *
     * @return string
     */
    public static function getCostantsValues($separator = ', ')
    {
        return implode($separator, self::getCostants());
    }
}
