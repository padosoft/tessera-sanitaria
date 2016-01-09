<?php
set_time_limit(0);

// Caricamento delle dipendenze
require_once "../vendor/autoload.php";

// Classi per il log
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
// Classi tessera sanitaria
use Padosoft\TesseraSanitaria\Tracciato;
use Padosoft\TesseraSanitaria\ValidateHelper;
use Padosoft\TesseraSanitaria\IOHelper;
use Padosoft\TesseraSanitaria\DataHelper;
use Padosoft\TesseraSanitaria\PrintHelper;
use Padosoft\TesseraSanitaria\CryptoHelper;
use Padosoft\TesseraSanitaria\CleanHelper;

// Richiama le costanti dal file .env e crea automaicamente le variabili globali di tipo $_ENV['']
$dotenv = new Dotenv\Dotenv(__DIR__ . '/..');
$dotenv->load();

// Crea log
$logger = new Logger("tessera_sanitaria");
$logger->pushHandler(new StreamHandler(__DIR__ . "/log/tessera_sanitaria.log", Logger::DEBUG));

if (!empty($_GET['do']) && $_GET['do'] == 1) {
    // Carica dati di esempio errati
    DataHelper::loadDataErrori($codiceRegione, $codiceAsl, $codiceSSA, $cfProprietario, $pIva, $arrSpesa,
        $arrVociSpesa);
} else {
    // Carica dati di esempio corretti
    DataHelper::loadData($codiceRegione, $codiceAsl, $codiceSSA, $cfProprietario, $pIva, $arrSpesa, $arrVociSpesa);
}

// Istanzia la classe per il tracciato e relative validazioni
$objPartitaIVA = new \fdisotto\PartitaIVA;
$objCFChecker = new \CodiceFiscale\Checker;
$objValidateHelper = new ValidateHelper($objPartitaIVA, $objCFChecker);

$objCleanHelper = new CleanHelper();
$objCryptoHelper = new CryptoHelper($_ENV['CERT_FILE'], $_ENV['TMP_PATH'], $_ENV['OPENSSL_EXE_PATH']);
$objTracciato = new Tracciato($objValidateHelper, $objCleanHelper, $objCryptoHelper);

// Crea XML
$rispostaTracciato = $objTracciato->doTracciato($codiceRegione, $codiceAsl, $codiceSSA, $cfProprietario, $pIva,
    $arrSpesa, $arrVociSpesa);

// Recupera l'XML creato
$strXML = $objTracciato->getXml();

// Stampa header template html
PrintHelper::printHtmlHeader();

// Recupera l'esito e gli eventuali errori
PrintHelper::printError($objTracciato, $logger);

if ($rispostaTracciato == true) {

    echo '<div class="alert alert-success" role="alert"><strong>Well done!</strong><br />FILE XML CREATO CON SUCCESSO</div>';

    // Stampa l'XML formattato
    PrintHelper::printXML($strXML);

    $basePath = __DIR__ . "/output/";
    $pathOutput = $basePath . date("Ymd-His") . "-" . md5($cfProprietario) . ".xml";
    $destinationZip = str_replace(".xml", ".zip", $pathOutput);

    // Salva XML su file
    IOHelper::outputFile($strXML, $pathOutput, $basePath);

    // Crea lo zip al volo e salva su $destinationZip
    IOHelper::zipFileOntheFly($pathOutput, $destinationZip, $strXML);

    // Crea zip a partire da un file su disco
    #zipFile($pathOutput, $destinationZip);
}

// Elimina file XML da tutta la cartella di output
if (!empty($_GET['do']) && $_GET['do'] == 2) {
    IOHelper::cleandir();
}

// Stampa comandi
PrintHelper::printButton();

// Stampa l'elenco dei file XML creati in /output
IOHelper::printDir();

// Stampa html footer
PrintHelper::printHtmlFooter();

// Pulisce directory file temporanei
IOHelper::cleandir($_ENV['TMP_PATH']);
