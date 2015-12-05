<?php

namespace Padosoft\TesseraSanitaria;

/**
 * Class CleanHelper
 * @package Padosoft\TesseraSanitaria
 */
class CleanHelper
{
	/**
	 * CleanHelper constructor.
	 */
	public function __construct()
	{
	}

	/**
	 * @param $value
	 *
	 * @return mixed
	 */
	public function clean($value)
	{
		$value = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', trim(html_entity_decode($value)));
		return $value;
	}
}