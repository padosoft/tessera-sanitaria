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
            $this->AddError("$codice_nome mancante");
        }else{
            if( is_int($codice_len) && ($codice_len>0) && strlen($codice) != $codice_len){
                $this->AddError("<b>".$codice."</b> - Il $codice_nome deve essere lungo $codice_len caratteri");
            }

            if(!in_array($codice, $arrCodiciValidi)){
                $this->AddError("<b>".$codice."</b> - $codice_nome non valido. Codici validi: ".implode(", ",$arrCodiciValidi));
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
	private function IsoDateValidate($dateStr)
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

			$this->AddError("Voci spesa mancanti");
		}else{

			$arrTipiSpesaPermessi = TipoSpesa::getCostants();

			foreach ($arrVociSpesa as $rigaVociSpesa){
				foreach ($rigaVociSpesa as $colonnaVociSpesa){
					foreach($colonnaVociSpesa as $campo=>$valore){

						if($campo == "tipoSpesa" && !in_array($valore,$arrTipiSpesaPermessi)){
								$this->AddError("<b>".$valore."</b> - Codice tipo spesa (tipoSpesa) non valido. Codici validi: ".implode(", ",$arrTipiSpesaPermessi));
						}
						if($campo == "importo" && !is_numeric($valore)){
								$this->AddError("<b>".$valore."</b> - Importo (importo) non numerico");
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
			$this->AddError("Dati spesa mancanti");
		}else{

			$arrFlagOperazione = FlagOperazione::getCostants();

			// Controllo interno array spesa
			foreach($arrSpesa as $rigaSpesa){

				if(count($rigaSpesa)<6){
					$this->AddError("Dati spesa incompleti");
				}

				foreach($rigaSpesa as $campo => $valore){

					// generico per campo mancante obbligatorio
					if($valore == "" && $campo != "flagPagamentoAnticipato"){ // flagPagamentoAnticipato e' facoltativo
						$this->AddError("Dato spesa mancante campo: ".$campo);
					}

                    if ($campo == "dataEmissione" || $campo == "dataPagamento") {
                        $this->checkDataValida($campo, $valore);
                    }

					if($campo == "flagOperazione" && (!in_array($valore, $arrFlagOperazione)) ){
						$this->AddError("<b>".$valore."</b> - Flag Operazione (flagOperazione) non valido. Codici validi: ".implode(", ",$arrFlagOperazione));
					}

					if($campo == "cfCittadino" && !$this->objCFChecker->isFormallyCorrect($valore)){
						$this->AddError("<b>".$valore."</b> - Codice fiscale (cfCittadino) cittadino non valido");
					}

					if($campo == "dispositivo" && (!is_numeric($valore) || strlen($valore) > 3) ){
						$this->AddError("<b>".$valore."</b> - Codice dispositivo (dispositivo) non valido: deve essere numerico, al massimo di 3 cifre");
					}

					if($campo == "numDocumento" && (!is_numeric($valore) || strlen($valore) > 20) ){
						$this->AddError("<b>".$valore."</b> - Numero documento (numDocumento) non valido: deve essere numerico, al massimo di 20 cifre");
					}
				}
			}
		}
	}

	/**
	 * @param $pIva
	 */
	public function checkPIva($pIva)
	{
		if(empty($pIva)){
			$this->AddError("Partita IVA mancante");
		}else{
			// Verifica formale della partita IVA
			if(!$this->objPartitaIVA->check($pIva)){
				$this->AddError("<b>".$pIva."</b> - Partita IVA formalmente non corretta");
			}
		}
	}

	/**
	 * @param $cfProprietario
	 */
	public function checkCfProprietario($cfProprietario)
	{
		if(empty($cfProprietario)){
			$this->AddError("Codice fiscale proprietario (cfProprietario) mancante");
		}else{
			// Verifica formale del codice fiscale
			if (!$this->objCFChecker->isFormallyCorrect($cfProprietario)){
				$this->AddError("<b>".$cfProprietario."</b> - Codice fiscale proprietario (cfProprietario) formalmente non corretto");
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
        if (!$this->IsoDateValidate($valore)) {
            $this->AddError("<b>" . $valore . "</b> - $campo non valida. La data deve essere nel formato ISO Es.: 2015-08-01");
        }

        if ($valore < "2015-01-01") {
            $this->AddError("<b>" . $valore . "</b> - $campo deve essere successiva al 01/01/2015");
        }
    }

}
