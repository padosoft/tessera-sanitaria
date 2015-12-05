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
}
