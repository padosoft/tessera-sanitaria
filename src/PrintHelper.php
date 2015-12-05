<?php
namespace Padosoft\TesseraSanitaria;
use Monolog\Logger;

/**
 * Class PrintHelper
 * @package Padosoft\TesseraSanitaria
 */
class PrintHelper
{
	use traits\Errorable;

    /**
     * @param Tracciato $objTracciato
     * @param Logger    $logger
     */
    public static function printError(Tracciato $objTracciato, Logger $logger)
	{

		if(!$objTracciato->getResult()){
			echo '<div class="alert alert-danger" role="alert"><strong>ERRORE!</strong><br /><br />Elenco Errori:<br /><br />';
			$arr_errors = $objTracciato->getArrErrors();
			foreach ($arr_errors as $error)
			{
				echo($error)."<br>";
				$logger->addError(strip_tags($error));
			}
			echo "<br /><br /><strong>FILE XML NON CREATO</strong><br><br>
				</div>";
		}
	}

    /**
     * @param            $strXML
     * @param bool|false $textarea
     *
     * @throws \Exception
     */
	public static function printXML($strXML, $textarea=false)
	{
		if($textarea){
            echo 'XML Generato: <br /><textarea name="xml" cols=130 rows=25>'.$strXML.'</textarea><br /><br />';
        }else {
            echo '<div style="height:500px; overflow:auto">' . \luminous::highlight('xml', $strXML) . "</div>";
        }
	}

	/**
	 *
	 */
	public static function printHtmlHeader()
	{
		\luminous::set(array('relative_root' => '../vendor/luminous/luminous'));

		echo '<html>
				<head>
					<link href="../vendor/twbs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
					<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
					<script src="../vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>';
					echo \luminous::head_html(); // outputs CSS includes to highlight xml, intended to go in <head>
		echo '	</head>
				<body role="document">
				 <div class="container theme-showcase" role="main">
					<div class="jumbotron">
						<h1>Demo Tessera Sanitaria.</h1>
						<p>Questo &egrave; un semplice demo che mostra l\'utilizzo del Package Padosoft TesseraSanitaria.<br />
							Il package permette la creazione di file XML delle prestazioni mediche per il servizio nazionale sanit&agrave;
							secondo il formato XML della tessera sanitaria definito nel DM 31/07/2015.<br />
							Per maggiori info si veda il <a href="http://sistemats1.sanita.finanze.it/wps/portal/" target="_blank">Portale della Tessera Sanitaria</a>
						</p>
					 </div>
				';
	}

	/**
	 *
	 */
	public static function printHtmlFooter()
	{
		echo '<div class="page-header"><h1>Log:</h1></div>
				<a target="_blank" href="log/tessera_sanitaria.log">tessera_sanitaria.log</a>

			   <div class="page-header"><h1>Credits:</h1></div>
				github: <a target="_blank" href="https://github.com/Padosoft">Padosoft</a>
				 -
				website: <a target="_blank" href="https://www.padosoft.com">https://www.padosoft.com</a>
				';
		echo '<br /><br /><br />
			   </div>
			  </body>
			</html>';
	}

	/**
	 *
	 */
	public static function printButton()
	{
		echo '<br /><br />
				<div class="page-header"><h1>Opzioni:</h1></div>
				<a type="button" class="btn btn-lg btn-success" href="?do=0">Test Dati Corretti</a>&nbsp;&nbsp;
				<a type="button" class="btn btn-lg btn-warning" href="?do=1">Test Dati Errati</a>&nbsp;&nbsp;
				<a type="button" class="btn btn-lg btn-danger" href="?do=2">Ripulisci Folder output</a>&nbsp;&nbsp;
				';
	}
}
