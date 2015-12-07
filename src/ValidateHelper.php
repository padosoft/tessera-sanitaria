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
     * @param       $codice
     * @param       $codice_nome
     * @param array $arrCodiciValidi
     * @param       $codice_len
     */
    public function checkCodice($codice, $codice_nome, array $arrCodiciValidi, $codice_len)
    {
        if($codice==''){
            $this->addError("$codice_nome mancante");
        }else{
            if( is_int($codice_len) && ($codice_len>0) && strlen($codice) != $codice_len){
                $this->addError("<b>".$codice."</b> - Il $codice_nome deve essere lungo $codice_len caratteri");
            }

            if(!in_array($codice, $arrCodiciValidi, null)){
                $this->addError("<b>".$codice."</b> - $codice_nome non valido. Codici validi: ".implode(", ",$arrCodiciValidi));
            }
        }
    }

	/**
	 * @param $codiceRegione
	 */
	public function checkCodiceRegione($codiceRegione)
	{
        $this->checkCodice($codiceRegione, 'Codice regione (codiceRegione)', CodiceRegione::getCostants(), 3);
	}

	/**
	 * @param $codiceSSA
	 */
	public function checkCodiceSSA($codiceSSA)
	{
        $this->checkCodice($codiceSSA, 'Codice SSA (codiceSSA)', CodiceSSA::getCostants(), 0);
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

			$arrTipiSpesaPermessi = TipoSpesa::getCostants();

			foreach ($arrVociSpesa as $rigaVociSpesa){
				foreach ($rigaVociSpesa as $colonnaVociSpesa){
					foreach($colonnaVociSpesa as $campo=>$valore){

                        if ($campo == "tipoSpesa"){

                            $this->checkTipoSpesa($valore, $arrTipiSpesaPermessi);
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

			$arrFlagOperazione = FlagOperazione::getCostants();

			// Controllo interno array spesa
			foreach($arrSpesa as $rigaSpesa){

				if(count($rigaSpesa)<6){
					$this->addError("Dati spesa incompleti");
				}

                $this->checkRigaSPesa($rigaSpesa, $arrFlagOperazione);
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
        $this->checkCodice($codiceAsl, 'Codice ASL (codiceAsl)', CodiceAsl::getCostants(), 3);
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
     * @param $arrTipiSpesaPermessi
     */
    private function checkTipoSpesa($valore, $arrTipiSpesaPermessi)
    {
        if (!in_array($valore, $arrTipiSpesaPermessi, null)) {
            $this->addError("<b>" . $valore . "</b> - Codice tipo spesa (tipoSpesa) non valido. Codici validi: " . implode(", ", $arrTipiSpesaPermessi));
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
        if ($valore == "" && $campo != "flagPagamentoAnticipato") { // flagPagamentoAnticipato e' facoltativo
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
     * @param $arrFlagOperazione
     */
    private function checkFlagOperazione($valore, $arrFlagOperazione)
    {
        if (!in_array($valore, $arrFlagOperazione, null)) {
            $this->addError("<b>" . $valore . "</b> - Flag Operazione (flagOperazione) non valido. Codici validi: " . implode(", ", $arrFlagOperazione));
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
    private function checkDispositivo($valore)
    {
        if (!is_numeric($valore) || strlen($valore) > 3) {
            $this->addError("<b>" . $valore . "</b> - Codice dispositivo (dispositivo) non valido: deve essere numerico, al massimo di 3 cifre");
        }
    }

    /**
     * @param $valore
     */
    private function checkNumDocumento($valore)
    {
        if (!is_numeric($valore) || strlen($valore) > 20) {
            $this->addError("<b>" . $valore . "</b> - Numero documento (numDocumento) non valido: deve essere numerico, al massimo di 20 cifre");
        }
    }

    /**
     * @param $rigaSpesa
     * @param $arrFlagOperazione
     */
    private function checkRigaSPesa($rigaSpesa, $arrFlagOperazione)
    {
        foreach($rigaSpesa as $campo => $valore) {

            $this->checkRequiredField($valore, $campo);

            if ($campo == "dataEmissione" || $campo == "dataPagamento") {

                $this->checkDataEmissione($campo, $valore);
            }elseif ($campo == "flagOperazione") {

                $this->checkFlagOperazione($valore, $arrFlagOperazione);
            }elseif ($campo == "cfCittadino") {

                $this->checkCfCittadino($valore);
            }elseif ($campo == "dispositivo") {

                $this->checkDispositivo($valore);
            }elseif ($campo == "numDocumento") {

                $this->checkNumDocumento($valore);
            }
        }
    }

}
