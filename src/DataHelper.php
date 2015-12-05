<?php
namespace Padosoft\TesseraSanitaria;

/**
 * Class DataHelper
 * @package Padosoft\TesseraSanitaria
 */
class DataHelper
{

	/**
	 * @param $codiceRegione
	 * @param $codiceAsl
	 * @param $codiceSSA
	 * @param $cfProprietario
	 * @param $pIva
	 * @param $arrSpesa
	 * @param $arrVociSpesa
	 */
	public static function loadData(&$codiceRegione, &$codiceAsl, &$codiceSSA, &$cfProprietario, &$pIva, &$arrSpesa, &$arrVociSpesa)
	{
		self::loadDatiProprietario($codiceRegione, $codiceAsl, $codiceSSA, $cfProprietario, $pIva);
		self::loadDatiSpesa($arrSpesa);
		self::loadDatiVociSpesa($arrVociSpesa);
	}

	/**
	 * @param $codiceRegione
	 * @param $codiceAsl
	 * @param $codiceSSA
	 * @param $cfProprietario
	 * @param $pIva
	 * @param $arrSpesa
	 * @param $arrVociSpesa
	 */
	public static function loadDataErrori(&$codiceRegione, &$codiceAsl, &$codiceSSA, &$cfProprietario, &$pIva, &$arrSpesa, &$arrVociSpesa)
	{
		self::loadDatiProprietarioErrati($codiceRegione, $codiceAsl, $codiceSSA, $cfProprietario, $pIva);
		self::loadDatiSpesaErrati($arrSpesa);
		self::loadDatiVociSpesaErrori($arrVociSpesa);
	}

	/**
	 * @param $codiceRegione
	 * @param $codiceAsl
	 * @param $codiceSSA
	 * @param $cfProprietario
	 * @param $pIva
	 */
	public static function loadDatiProprietario(&$codiceRegione, &$codiceAsl, &$codiceSSA, &$cfProprietario, &$pIva)
	{
		/*
		PARAMETRI PROPRIETARIO
		Tutti obbligatori

		$codiceRegione: Alfanumerico di 3 caratteri. Codice regione della farmacia/struttura che emette il documento fiscale,
						vedi http://sistemats1.sanita.finanze.it/wps/wcm/connect/5b19c48049187d8eaa0beaf8a137072d/allegato+tecnico+TS-CNS+ex+DL+78-2010_v22-06-12.pdf?MOD=AJPERES&CACHEID=5b19c48049187d8eaa0beaf8a137072d
		$codiceAsl: 	Alfanumerico di 3 caratteri. Codice della ASL della farmacia/struttura che emette il documento fiscale,
						vedi http://www.webacma.it/asl_italiane.html
		$codiceSSA: 	Numerico di 1 carattere.
						Codice farmacia/struttura che emette il documento fiscale. Se farmacia = 5, se struttura = 6
		$cfProprietario: Codice fiscale del soggetto indicato come Titolare
		$pIva: 			Partita IVA della farmacia/struttura o medico che emette il documento fiscale.
		*/

		// DATI BASE
		$codiceRegione = CodiceRegione::TOSCANA;
		$codiceAsl = CodiceAsl::FIRENZE;
		$codiceSSA = CodiceSSA::STRUTTURA;
		$cfProprietario = "CPLDVD67S11D612V";
		$pIva = "01234567890";
	}

	/**
	 * @param $codiceRegione
	 * @param $codiceAsl
	 * @param $codiceSSA
	 * @param $cfProprietario
	 * @param $pIva
	 */
	public static function loadDatiProprietarioErrati(&$codiceRegione, &$codiceAsl, &$codiceSSA, &$cfProprietario, &$pIva)
	{
		$codiceRegione = 565656;
		$codiceAsl = "56565";
		$codiceSSA = 66689;
		$cfProprietario = "XYZZZZZL68H02D612R";
		$pIva = "XXX0500712345";
	}

	/**
	 * @param $arrSpesa
	 */
	public static function loadDatiSpesa(&$arrSpesa)
	{
		/*
		PARAMETRI SCONTRINO/RICEVUTA ($arrSpesa)
		Tutti obbligatori tranne $flagPagamentoAnticipato (richiesto solo se la data di pagamento e' antecedente alla data di emissione)

		$dataEmissione: Data di emissione del documento fiscale relativo alla spesa sostenuta dal cittadino. Non puo' essere minore di 2015-01-01
		$dispositivo: Numerico di 3 caratteri. Numero progressivo del dispositivo che genera il documento. Per dispositivo si intende il registratore di cassa utilizzato dalla farmacie; per l’emissione di fatture o ricevute fiscali il campo assume il valore 1.
		$numDocumento: Numerico di max 20 cifre. Univoco nell’ambito della data. Solitamente e' univoco per giornata (scontrini) o per anno (fattura)
		$dataPagamento: Data di pagamento afferente al documento fiscale emesso. Deve essere coincidente o maggiore rispetto alla data di emissione. Puo' essere minore rispetto alla data di emissione solo se il flag pagamento anticipato e' valorizzato a 1 (e comunque non minore di 2015-01-01)
		$flagPagamentoAnticipato=0: Numerico di 1 carattere. Il campo deve essere valorizzato a "1" per indicare il pagamento della spesa sostenuta dal cittadino in data antecedente alla data di emissione del documento fiscale
		$flagOperazione: Alfanumerico di 1 carattere. Indica il tipo di operazione da eseguire sul record. Valori ammessi: "I" = inserimento ovvero nuovo record; "V" = Variazione; "R" = Rimborso; "C" = Cancellazione
		cfCittadino: Alfanumerico di 256 caratteri. Codice fiscale del cittadino rilevato dalla Tessera Sanitaria. DOVREBBE ESSERE CRITTOGRAFATO, MA NON E' INDICATO COME (forse dalla tessera sanitaria arriva gia' crittografato?)
		*/

		$arrSpesa[0] = array("dataEmissione" 	=> "2015-10-08"
							,"dispositivo" 		=> "001","numDocumento" => "1234567"
							,"dataPagamento" 	=> "2015-10-08"
							,"flagPagamentoAnticipato" => "0"
							,"flagOperazione" 	=> FlagOperazione::INSERIMENTO
							,"cfCittadino" 		=> "RSSMRA85T10A562S"
							);
		$arrSpesa[1] = array("dataEmissione" 	=> "2015-09-07"
							,"dispositivo" 		=> "003"
							,"numDocumento" 	=> "7654321"
							,"dataPagamento" 	=> "2015-09-07"
							,"flagPagamentoAnticipato" => "0"
							,"flagOperazione" 	=> FlagOperazione::RIMBORSO
							,"cfCittadino" 		=> "RSSFLV95C12H118C"
							);
		$arrSpesa[2] = array("dataEmissione" 	=> "2015-08-30"
							,"dispositivo" 		=> "003"
							,"numDocumento" 	=> "3336667"
							,"dataPagamento" 	=> "2015-07-15"
							,"flagPagamentoAnticipato" => "1"
							,"flagOperazione" 	=> FlagOperazione::INSERIMENTO
							,"cfCittadino" 		=> "GNRGNI77S64F051C"
							);
	}

	/**
	 * @param $arrSpesa
	 */
	public static function loadDatiSpesaErrati(&$arrSpesa)
	{
		$arrSpesa[0] = array("dataEmissione" 	=> "2015-10-08"
							,"dataPagamento" 	=> "2015-10-08"
							,"flagPagamentoAnticipato" => "0"
							,"flagOperazione" 	=> FlagOperazione::INSERIMENTO
							,"cfCittadino" 		=> "RSSMRA85T10A562S"
							);
		$arrSpesa[1] = array("dataEmissione" 	=> "09-07"
							,"dispositivo" 		=> "AAAA"
							,"numDocumento" 	=> ""
							,"dataPagamento" 	=> ""
							,"flagPagamentoAnticipato" => "sdsfs"
							,"flagOperazione" 	=> 684645
							,"cfCittadino" 		=> "XXXFLV95C12H118C"
							);
		$arrSpesa[2] = array();
	}

	/**
	 * @param $arrVociSpesa
	 */
	public static function loadDatiVociSpesa(&$arrVociSpesa)
	{
		/* VOCI DELLO SCONTRINO/RICEVUTA ($arrVociSpesa)
		Tutti obbligatori tranne $flagTipoSpesa

		$tipoSpesa: Alfanumerico di 2 caratteri. Il Campo assume i seguenti valori: TK= Ticket (Quota fissa e/o Differenza con il prezzo di riferimento. Franchigia. Pronto Soccorso e accesso diretto); FC= Farmaco, anche omeopatico. Dispositivi medici CE; FV = Farmaco per uso veterinario; AD= Acquisto o affitto di dispositivo medico CE; AS= Spese sanitarie relative ad ECG, spirometria, Holter pressorio e cardiaco, test per glicemia, colesterolo e trigliceridi o misurazione della pressione sanguigna, prestazione previste dalla farmacia dei servizi e simili); SR= Spese prestazioni assistenza specialistica ambulatoriale esclusi interventi di chirurgia estetica. Visita medica generica e specialistica o prestazioni diagnostiche e strumentali. Prestazione chirurgica ad esclusione della chirurgia estetica. Ricoveri ospedalieri, al netto del comfort. Certificazione medica; CT= Cure Termali; PI= protesica e integrativa; IC= Intervento di chirurgia estetica ambulatoriale o ospedaliero; AA= Altre spese
		$flagTipoSpesa=0: Numerico di 1 carattere. Il campo vale "1" con tipo TK (ticket di pronto soccorso), "2" con tipo SR (visita in intromoenia)
		$importo: Float 5,2. Importo di ogni singola spesa sostenuta dal cittadino / rimborso riconosciuto al cittadino a fronte di una spesa sostenuta. Il campo deve assumere sempre valori positivi, anche in caso di rimborso
		*/

		$arrVociSpesa[0][0] = array("tipoSpesa" => TipoSpesa::TICKET,"flagTipoSpesa" => "1","importo" => "11.40");
		$arrVociSpesa[1][0] = array("tipoSpesa" => TipoSpesa::FARMACO,"flagTipoSpesa" => "0","importo" => "9.60");
		$arrVociSpesa[2][0] = array("tipoSpesa" => TipoSpesa::FARMACO_VETERINARIO,"flagTipoSpesa" => "0","importo" => "80");

		$arrVociSpesa[0][1] = array("tipoSpesa" => TipoSpesa::DISPOSITIVO_MEDICO,"flagTipoSpesa" => "0","importo" => "2.10");
		$arrVociSpesa[1][1] = array("tipoSpesa" => TipoSpesa::ANALISI_STRUMENTALI,"flagTipoSpesa" => "0","importo" => "19.85");

		$arrVociSpesa[0][2] = array("tipoSpesa" => TipoSpesa::VISITE_INTROMOENIA,"flagTipoSpesa" => "2","importo" => "60");
		$arrVociSpesa[1][2] = array("tipoSpesa" => TipoSpesa::CURE_TERMALI,"flagTipoSpesa" => "0","importo" => "5.20");
		$arrVociSpesa[2][2] = array("tipoSpesa" => TipoSpesa::PROTESI,"flagTipoSpesa" => "2","importo" => "30.05");
		$arrVociSpesa[3][2] = array("tipoSpesa" => TipoSpesa::CHIRURGIA_ESTETICA,"flagTipoSpesa" => "0","importo" => "85");
	}

	/**
	 * @param $arrVociSpesa
	 */
	public static function loadDatiVociSpesaErrori(&$arrVociSpesa)
	{
		$arrVociSpesa[0][0] = array("tipoSpesa" => TipoSpesa::TICKET,"flagTipoSpesa" => "1","importo" => "xxxx");
		$arrVociSpesa[1][0] = array("tipoSpesa" => TipoSpesa::FARMACO,"flagTipoSpesa" => "0","importo" => "sdfsfsdf");
		$arrVociSpesa[2][0] = array("tipoSpesa" => TipoSpesa::FARMACO_VETERINARIO,"flagTipoSpesa" => "fdsf","importo" => "zzzzz");

		$arrVociSpesa[0][1] = array("tipoSpesa" => TipoSpesa::DISPOSITIVO_MEDICO,"flagTipoSpesa" => "0","importo" => "asdsdsf");
	}
}
