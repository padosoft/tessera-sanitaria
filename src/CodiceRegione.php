<?php
namespace Padosoft\TesseraSanitaria;

/**
 * Class CodiceRegione
 * @package Padosoft\TesseraSanitaria
 *
 * @link http://sistemats1.sanita.finanze.it/wps/wcm/connect/5b19c48049187d8eaa0beaf8a137072d/allegato+tecnico+TS-CNS+ex+DL+78-2010_v22-06-12.pdf?MOD=AJPERES&CACHEID=5b19c48049187d8eaa0beaf8a137072d
 */
class CodiceRegione
{
	use traits\Enumerable;

	const PIEMONTE = '010';
	const VAL_D_AOSTA = '020';
	const LOMBARDIA = '030';
	const PROVINCIA_AUTONOMA_DI_BOLZANO = '041';
	const PROVINCIA_AUTONOMA_DI_TRENTO = '042';
	const VENETO = '050';
	const FRIULI_VENEZIA_GIULIA = '060';
	const LIGURIA = '070';
	const EMILIA_ROMAGNA = '080';
	const TOSCANA = '090';
	const UMBRIA = '100';
	const MARCHE = '110';
	const LAZIO = '120';
	const ABRUZZO = '130';
	const MOLISE = '140';
	const CAMPANIA = '150';
	const PUGLIA = '160';
	const BASILICATA = '170';
	const CALABRIA = '180';
	const SICILIA = '190';
	const SARDEGNA = '200';
	const SASN_GENOVA = '001';
	const SASN_NAPOLI = '002';
	const UFFICIO_RAPPORTI_INTERNAZIONALI_PER_GLI_ISCRITTI_AIRE = '003';
}

