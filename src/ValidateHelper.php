<?php
namespace Padosoft\TesseraSanitaria;

/**
 * Class ValidateHelper
 * @package Padosoft\TesseraSanitaria
 */
class ValidateHelper
{
	use traits\Errorable;

    private $objPartitaIVA = null;
    private $objCFChecker = null;

	/**+
	 * ValidateHelper constructor.
	 *
	 * @param \fdisotto\PartitaIVA   $objPartitaIVA
	 * @param \CodiceFiscale\Checker $objCFChecker
	 */
	public function __construct(\fdisotto\PartitaIVA $objPartitaIVA, \CodiceFiscale\Checker $objCFChecker)
	{
		$this->arrErrors = array();
		$this->objPartitaIVA = $objPartitaIVA;
		$this->objCFChecker = $objCFChecker;
	}

	/**
	 * @param $codiceRegione
	 */
	public function checkCodiceRegione($codiceRegione)
	{
        if (!CodiceRegione::isValidValue($codiceRegione)) {
            $this->addError("<b>".$codiceRegione."</b> - Codice regione (codiceRegione) non valido. Codici validi: ".CodiceRegione::getCostantsValues());
        }
	}

	/**
	 * @param $codiceSSA
	 */
	public function checkCodiceSSA($codiceSSA)
	{
        if (!CodiceSSA::isValidValue($codiceSSA)) {
            $this->addError("<b>".$codiceSSA."</b> - Codice SSA (codiceSSA) non valido. Codici validi: ".CodiceSSA::getCostantsValues());
        }
	}

	/**
	 * @param $dateStr
	 *
	 * @return bool
	 */
	public function isoDateValidate($dateStr)
	{
		if (preg_match('/^([\+-]?\d{4}(?!\d{2}\b))((-?)((0[1-9]|1[0-2])(\3([12]\d|0[1-9]|3[01]))?|W([0-4]\d|5[0-2])(-?[1-7])?|(00[1-9]|0[1-9]\d|[12]\d{2}|3([0-5]\d|6[1-6])))([T\s]((([01]\d|2[0-3])((:?)[0-5]\d)?|24\:?00)([\.,]\d+(?!:))?)?(\17[0-5]\d([\.,]\d+)?)?([zZ]|([\+-])([01]\d|2[0-3]):?([0-5]\d)?)?)?)?$/', $dateStr) > 0){
			return TRUE;
		}else{
			return FALSE;
		}
	}

	/**+
	 * @param $arrVociSpesa
	 */
	public function checkArrVociSpesa($arrVociSpesa)
	{
	    if(empty($arrVociSpesa)){

			$this->addError("Voci spesa mancanti");
		}else{

			foreach ($arrVociSpesa as $rigaVociSpesa){
				foreach ($rigaVociSpesa as $colonnaVociSpesa){
					foreach($colonnaVociSpesa as $campo=>$valore){

                        if ($campo == "tipoSpesa"){

                            $this->checkTipoSpesa($valore);
                        }elseif ($campo == "importo") {

                            $this->checkImporto($valore);
                        }
                    }
				}
			}
		}
	}

	/**
	 * @param $arrSpesa
	 */
	public function checkArrSpesa($arrSpesa)
	{
		if(empty($arrSpesa)){
			$this->addError("Dati spesa mancanti");
		}else{

			// Controllo interno array spesa
			foreach($arrSpesa as $rigaSpesa){

                $this->checkRigaSpesa($rigaSpesa);
			}
		}
	}

	/**
	 * @param $pIva
	 */
	public function checkPIva($pIva)
	{
		if(empty($pIva)){
			$this->addError("Partita IVA mancante");
		}else{
			// Verifica formale della partita IVA
			if(!$this->objPartitaIVA->check($pIva)){
				$this->addError("<b>".$pIva."</b> - Partita IVA formalmente non corretta");
			}
		}
	}

	/**
	 * @param $cfProprietario
	 */
	public function checkCfProprietario($cfProprietario)
	{
		if(empty($cfProprietario)){
			$this->addError("Codice fiscale proprietario (cfProprietario) mancante");
		}else{
			// Verifica formale del codice fiscale
			if (!$this->objCFChecker->isFormallyCorrect($cfProprietario)){
				$this->addError("<b>".$cfProprietario."</b> - Codice fiscale proprietario (cfProprietario) formalmente non corretto");
			}
		}
	}

	/**
	 * @param $codiceAsl
	 */
	public function checkCodiceAsl($codiceAsl)
	{
        if (!CodiceAsl::isValidValue($codiceAsl)) {
            $this->addError("<b>".$codiceAsl."</b> - Codice ASL (codiceAsl) non valido. Codici validi: ".CodiceAsl::getCostantsValues());
        }
	}

    /**
     * @param $campo
     * @param $valore
     */
    private function checkDataValida($campo, $valore)
    {
        if (!$this->isoDateValidate($valore)) {
            $this->addError("<b>" . $valore . "</b> - $campo non valida. La data deve essere nel formato ISO Es.: 2015-08-01");
        }

        if ($valore < "2015-01-01") {
            $this->addError("<b>" . $valore . "</b> - $campo deve essere successiva al 01/01/2015");
        }
    }

    /**
     * @param $valore
     */
    public function checkTipoSpesa($valore)
    {
        if (!TipoSpesa::isValidValue($valore)) {
            $this->addError("<b>" . $valore . "</b> - Codice tipo spesa (tipoSpesa) non valido. Codici validi: " . TipoSpesa::getCostantsValues());
        }
    }

    /**
     * @param $valore
     */
    private function checkImporto($valore)
    {
        if (!is_numeric($valore)) {
            $this->addError("<b>" . $valore . "</b> - Importo (importo) non numerico");
        }
    }

    /**
     * @param $valore
     * @param $campo
     */
    private function checkRequiredField($valore, $campo)
    {
        if ($valore == "") {
            $this->addError("Dato spesa mancante campo: " . $campo);
        }
    }

    /**
     * @param $campo
     * @param $valore
     */
    private function checkDataEmissione($campo, $valore)
    {
        $this->checkDataValida($campo, $valore);
    }

    /**
     * @param $valore
     */
    public function checkFlagOperazione($valore)
    {
        if (!FlagOperazione::isValidValue($valore)) {
            $this->addError("<b>" . $valore . "</b> - Flag Operazione (flagOperazione) non valido. Codici validi: " . FlagOperazione::getCostantsValues());
        }
    }

    /**
     * @param $valore
     */
    private function checkCfCittadino($valore)
    {
        if (!$this->objCFChecker->isFormallyCorrect($valore)) {
            $this->addError("<b>" . $valore . "</b> - Codice fiscale (cfCittadino) cittadino non valido");
        }
    }

    /**
     * @param $valore
     */
    public function checkDispositivo($valore)
    {
        if (!$this->checkNumericField($valore, 3, true)) {
            $this->addError("<b>" . $valore . "</b> - Codice dispositivo (dispositivo) non valido: deve essere numerico, al massimo di 3 cifre");
        }
    }

    /**
     * @param $valore
     */
    public function checkNumDocumento($valore)
    {
        if (!$this->checkNumericField($valore, 20)) {
            $this->addError("<b>" . $valore . "</b> - Numero documento (numDocumento) non valido: deve essere numerico, al massimo di 20 cifre");
        }
    }

    /**
     * @param            $valore
     * @param int        $maxLen
     * @param bool|false $zeroFilled
     *
     * @return bool
     */
    public function checkNumericField($valore, $maxLen=0, $zeroFilled=false)
    {
        if($zeroFilled && $valore!=''){
            $valore = ltrim(trim($valore), '0');
        }
        if(!is_numeric($valore)){
            return false;
        }
        if(strlen($valore)>1 && substr($valore, 0,2)=='00'){ //because '00123' passed!
            return false;
        }
        if(is_numeric($maxLen) && $maxLen>0){
            $maxNumber = pow(10, $maxLen);
            return !( $valore>=$maxNumber );
        }else{
            return true;
        }
    }

    /**
     * @param $rigaSpesa
     */
    private function checkRigaSpesa($rigaSpesa)
    {
        if(count($rigaSpesa)<6){
            $this->addError("Dati spesa incompleti");
        }

        foreach($rigaSpesa as $campo => $valore) {

            $this->checkDatiSpesa($campo, $valore);
        }
    }
    
    /**
     * @param $campo
     * @param $valore
     */
    private function checkDatiSpesa($campo, $valore)
    {
        if ($campo != "flagPagamentoAnticipato") { // flagPagamentoAnticipato e' facoltativo

            $this->checkRequiredField($valore, $campo);
        }

        switch ($campo) {

            case 'dataEmissione':
            case 'dataPagamento':
                $this->checkDataEmissione($campo, $valore);
                break;

            case 'flagOperazione':
                $this->checkFlagOperazione($valore);
                break;

            case 'cfCittadino':
                $this->checkCfCittadino($valore);
                break;

            case 'dispositivo':
                $this->checkDispositivo($valore);
                break;

            case 'numDocumento':
                $this->checkNumDocumento($valore);
                break;

        }
    }
}
