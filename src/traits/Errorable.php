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
	public function getArrErrors()
	{
		if(!is_array($this->arrErrors)){
			$this->arrErrors = array();
		}
		return $this->arrErrors;
	}

	/**
	 * @param $str
	 */
	public function addError($str)
	{
		if($str == ""){
			return;
		}
		$this->arrErrors[] = $str;
	}

	/**
	 * @param $add
	 */
	public function addArrErrors($add)
	{
		if(!is_array($add) || count($add)<1){
			return;
		}

		$this->arrErrors = array_merge($this->arrErrors, $add);

	}

	/**
	 *
	 */
	public function resetErrors()
	{
		$this->arrErrors = array();
	}

    /**
     * @return bool
     */
    public function hasErrors()
    {
        if(count($this->getArrErrors())>0){
            return true;
        }else{
            return false;
        }
    }
}
