<?php
namespace Padosoft\TesseraSanitaria;

/**
 * Class Tracciato
 * @package Padosoft\TesseraSanitaria
 */
class Tracciato
{
	use traits\Errorable;

	private $strXML = "";
	private $codiceRegione = "";
	private $codiceAsl = "";
	private $codiceSSA = "";
	private $cfProprietario = "";
	private $pIva = "";
	private $arrSpesa = array();
	private $arrVociSpesa = array();
	private $objValidateHelper = null;
	private $objCleanHelper = null;
	private $objCryptoHelper = null;

	/**
	 * Tracciato constructor.
	 *
	 * @param ValidateHelper $objValidateHelper
	 * @param CleanHelper    $objCleanHelper
	 * @param CryptoHelper   $objCryptoHelper
	 */
	public function __construct(ValidateHelper $objValidateHelper, CleanHelper $objCleanHelper, CryptoHelper $objCryptoHelper)
	{
		$this->objValidateHelper = $objValidateHelper;
		$this->objCleanHelper = $objCleanHelper;
		$this->objCryptoHelper = $objCryptoHelper;
	}

	/**
	 * @return bool
	 */
	public function getResult()
	{
		if(!is_array($this->getArrErrors()))
		{
			return TRUE;
		}
		return (count($this->getArrErrors())<1);
	}

	/**
	 * @param $codiceRegione
	 * @param $codiceAsl
	 * @param $codiceSSA
	 * @param $cfProprietario
	 * @param $pIva
	 * @param $arrSpesa
	 * @param $arrVociSpesa
	 *
	 * @return bool
	 */
	public function doTracciato($codiceRegione, $codiceAsl, $codiceSSA, $cfProprietario, $pIva, $arrSpesa, $arrVociSpesa)
	{
		$this->resetVarTracciato();

		$this->codiceRegione = $codiceRegione;
		$this->codiceAsl = $codiceAsl;
		$this->codiceSSA = $codiceSSA;
		$this->cfProprietario = $cfProprietario;
		$this->pIva = $pIva;
		$this->arrSpesa = $arrSpesa;
		$this->arrVociSpesa = $arrVociSpesa;

		$this->validateParamTracciato();
		if(!$this->getResult()){
			return FALSE;
		}

		$this->strXML = $this->generateXML();
		if(!$this->getResult()){
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * @return string
	 */
	public function getXml()
	{
		if(empty($this->strXML)){
			$this->strXML = "";
		}

		return $this->strXML;
	}

	/**
	 * @param int $numTab
	 *
	 * @return string
	 */
	private function addTab($numTab=1)
	{
		$numTab = (int) $numTab;
		$str="";
		for($i=0;$i<$numTab;$i++){
			$str.="    ";
		}
		return $str;
	}

	/**
	 *
	 */
	private function resetVarTracciato()
	{
		$this->strXML = "";
		$this->resetErrors();
		$this->objValidateHelper->resetErrors();
		$this->objCryptoHelper->resetErrors();

		$this->codiceRegione = "";
		$this->codiceAsl = "";
		$this->codiceSSA = "";
		$this->cfProprietario = "";
		$this->pIva = "";
		$this->arrSpesa = array();
		$this->arrVociSpesa = array();
	}

	/**
	 *
	 */
	private function validateParamTracciato()
	{
		$this->objValidateHelper->checkCodiceRegione($this->codiceRegione);
		$this->objValidateHelper->checkCodiceAsl($this->codiceAsl);
		$this->objValidateHelper->checkCodiceSSA($this->codiceSSA);
		$this->objValidateHelper->checkCfProprietario($this->cfProprietario);
		$this->objValidateHelper->checkPIva($this->pIva);
		$this->objValidateHelper->checkArrSpesa($this->arrSpesa);
		$this->objValidateHelper->checkArrVociSpesa($this->arrVociSpesa);

		$this->addArrErrors($this->objValidateHelper->getArrErrors());
	}

	/**
	 * @return string
	 */
	private function generateXML()
	{
		// Cifra e rende il risultato in base64 per essere mostrato nell'XML
        $cfencrypted = $this->encrypt($this->cfProprietario, 'cfProprietario');
        if($cfencrypted==''){
            return '';
        }

		// Testata: dati proprietario (prima di <proprietario>, rimossi campi opzionali di esempio <opzionale1>text</opzionale1><opzionale2>text</opzionale2><opzionale3>text</opzionale3>)
		$xml = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
		$xml .= '<precompilata xsi:noNamespaceSchemaLocation="730_precompilata.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'.PHP_EOL;

 		$xml .= $this->addTab(1).'<proprietario>'.PHP_EOL;
		$xml .= $this->addTab(2).'<codiceRegione>'.$this->objCleanHelper->clean($this->codiceRegione).'</codiceRegione>'.PHP_EOL;
		$xml .= $this->addTab(2).'<codiceAsl>'.$this->objCleanHelper->clean($this->codiceAsl).'</codiceAsl>'.PHP_EOL;
		$xml .= $this->addTab(2).'<codiceSSA>'.$this->objCleanHelper->clean($this->codiceSSA).'</codiceSSA>'.PHP_EOL;
		$xml .= $this->addTab(2).'<cfProprietario>'.$cfencrypted.'</cfProprietario>'.PHP_EOL;
		$xml .= $this->addTab(1).'</proprietario>'.PHP_EOL;

		// Documento fiscale: dati identificativi della ricevuta/scontrino
		foreach($this->arrSpesa as $key => $rigaSpesa)
		{
			$xml .= $this->addTab(1).'<documentoSpesa>'.PHP_EOL;

			$flagOperazione = $rigaSpesa['flagOperazione'];

			// Rimborso
			if($flagOperazione == FlagOperazione::RIMBORSO)
			{
				$xml .= $this->addTab(2).'<idRimborso>'.PHP_EOL;
				$xml .= $this->addTab(3).'<pIva>'.$this->objCleanHelper->clean($this->pIva).'</pIva>'.PHP_EOL;
				$xml .= $this->addTab(3).'<dataEmissione>'.$this->objCleanHelper->clean($rigaSpesa['dataEmissione']).'</dataEmissione>'.PHP_EOL;
				$xml .= $this->addTab(3).'<numDocumentoFiscale>'.PHP_EOL;
				$xml .= $this->addTab(4).'<dispositivo>'.$this->objCleanHelper->clean($rigaSpesa['dispositivo']).'</dispositivo>'.PHP_EOL;
				$xml .= $this->addTab(4).'<numDocumento>'.$this->objCleanHelper->clean($rigaSpesa['numDocumento']).'</numDocumento>'.PHP_EOL;
				$xml .= $this->addTab(3).'</numDocumentoFiscale>'.PHP_EOL;
				$xml .= $this->addTab(2).'</idRimborso>'.PHP_EOL;
			}

			// Dati ricevuta/scontrino
			$cfCittadinoEncrypted = $this->encrypt($rigaSpesa['cfCittadino'], 'cfCittadino');

			$xml .= $this->addTab(2).'<idSpesa>'.PHP_EOL;
			$xml .= $this->addTab(3).'<pIva>'.$this->objCleanHelper->clean($this->pIva).'</pIva>'.PHP_EOL;
			$xml .= $this->addTab(3).'<dataEmissione>'.$this->objCleanHelper->clean($rigaSpesa['dataEmissione']).'</dataEmissione>'.PHP_EOL;
			$xml .= $this->addTab(3).'<numDocumentoFiscale>'.PHP_EOL;
			$xml .= $this->addTab(4).'<dispositivo>'.$this->objCleanHelper->clean($rigaSpesa['dispositivo']).'</dispositivo>'.PHP_EOL;
			$xml .= $this->addTab(4).'<numDocumento>'.$this->objCleanHelper->clean($rigaSpesa['numDocumento']).'</numDocumento>'.PHP_EOL;
			$xml .= $this->addTab(3).'</numDocumentoFiscale>'.PHP_EOL;
			$xml .= $this->addTab(2).'</idSpesa>'.PHP_EOL;

			$xml .= $this->addTab(2).'<dataPagamento>'.$this->objCleanHelper->clean($rigaSpesa['dataPagamento']).'</dataPagamento>'.PHP_EOL;
			$xml .= $this->addTab(2).'<flagPagamentoAnticipato>'.$this->objCleanHelper->clean($rigaSpesa['flagPagamentoAnticipato']).'</flagPagamentoAnticipato>'.PHP_EOL;
			$xml .= $this->addTab(2).'<flagOperazione>'.$this->objCleanHelper->clean($rigaSpesa['flagOperazione']).'</flagOperazione>'.PHP_EOL;
			$xml .= $this->addTab(2).'<cfCittadino>'.$cfCittadinoEncrypted.'</cfCittadino>'.PHP_EOL;

			// Singole voci della ricevuta/scontrino
			foreach($this->arrVociSpesa as $rigaVociSpesa)
			{
				if(!empty($rigaVociSpesa[$key]['tipoSpesa']))
				{
					$xml .= $this->addTab(2).'<voceSpesa>'.PHP_EOL;
					$xml .= $this->addTab(3).'<tipoSpesa>'.$this->objCleanHelper->clean($rigaVociSpesa[$key]['tipoSpesa']).'</tipoSpesa>'.PHP_EOL;
					$xml .= $this->addTab(3).'<flagTipoSpesa>'.$this->objCleanHelper->clean($rigaVociSpesa[$key]['flagTipoSpesa']).'</flagTipoSpesa>'.PHP_EOL;
					$xml .= $this->addTab(3).'<importo>'.$this->objCleanHelper->clean($rigaVociSpesa[$key]['importo']).'</importo>'.PHP_EOL;
					$xml .= $this->addTab(2).'</voceSpesa>'.PHP_EOL;
				}
			}

			$xml .= $this->addTab(1).'</documentoSpesa>'.PHP_EOL;
		}
		$xml .= '</precompilata>'.PHP_EOL;

		if(!$this->getResult()){
			$xml='';
		}

		return $xml;
	}

    /**
     * @param $cfProprietario
     * @param $cfProprietarioName
     *
     * @return string
     */
    private function encrypt($cfProprietario, $cfProprietarioName)
    {
        $cfencrypted = base64_encode($this->objCryptoHelper->rsaEncrypt($cfProprietario));
        if (count($this->objCryptoHelper->getArrErrors()) > 1) {
            $this->addError("Errore di criptazione openssl sul $cfProprietarioName: " . $this->cfProprietario);
            $this->addArrErrors($this->objCryptoHelper->getArrErrors());
        }
        return $cfencrypted;
    }
}
