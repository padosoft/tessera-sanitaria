<?php

namespace Padosoft\TesseraSanitaria\traits;

/**
 * Class Errorable
 * @package Padosoft\TesseraSanitaria\traits
 */
trait Errorable
{
	/**
	 * @var array
	 */
	protected $arrErrors = array();

	/**
	 * @return array
	 */
	public function GetArrErrors()
	{
		if(!is_array($this->arrErrors)){
			$this->arrErrors = array();
		}
		return $this->arrErrors;
	}

	/**
	 * @param $str
	 */
	public function AddError($str)
	{
		if($str == ""){
			return;
		}
		$this->arrErrors[] = $str;
	}

	/**
	 * @param $add
	 */
	public function AddArrErrors($add)
	{
		if(!is_array($add) || count($add)<1){
			return;
		}
		
		$this->arrErrors = array_merge($this->arrErrors, $add);
		
	}

	/**
	 *
	 */
	public function ResetErrors()
	{
		$this->arrErrors = array();
	}
}