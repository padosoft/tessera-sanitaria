<?php
namespace Padosoft\TesseraSanitaria;

/**
 * Class CryptoHelper
 * @package Padosoft\TesseraSanitaria
 */
class CryptoHelper
{
    use traits\Errorable;

    protected $cert_file = "";
    protected $tmp_path = "";
    protected $openssl_exe_path = "";
    public $output = "";
    protected $returned_val = "";

    /**
     * CryptoHelper constructor.
     *
     * @param $cert_file
     * @param $tmp_path
     * @param $openssl_exe_path
     */
    public function __construct($cert_file, $tmp_path, $openssl_exe_path)
    {
        $this->cert_file = $cert_file;
        $this->tmp_path = $tmp_path;
        $this->openssl_exe_path = $openssl_exe_path;
    }

    /**
     * @param $str
     *
     * @return string
     */
    public function rsaEncrypt($str)
    {
        if (!$this->checkPath()) {
            return '';
        }
        // Path e nomi dei file temporanei
        $rand_name = $this->getRandName();
        $file_source = $this->tmp_path . $rand_name . ".txt";
        $file_dest = $this->tmp_path . $rand_name . ".enc";

        // Scrive file temporaneo sorgente
        file_put_contents($file_source, $str);

        // creo il comando openssl
        $exec = $this->getCommand($file_source, $file_dest);

        // Esegue istruzione openssl, creando file temporaneo con testo criptato
        $this->excecuteCommand($exec);

        // Ricava il testo criptato dal file appena creato
        $encrypted_txt = $this->getEncryptedString($file_dest);

        //clean
        $this->deleteSourceFile($file_source);

        return $encrypted_txt;
    }

    /**
     * @return bool
     */
    public function getError()
    {
        $result = false;
        if ($this->returned_val == 1) {
            $result = true;
        }
        return $result;
    }

    /**
     * @return bool
     */
    private function checkPath()
    {
        if (!file_exists($this->tmp_path)) {
            $this->addError('Il percorso della path temporanea non &egrave; valido: ' . $this->tmp_path);
            return false;
        }
        if (!file_exists($this->cert_file)) {
            $this->addError('Il percorso del file del certificato non &egrave; valido: ' . $this->cert_file);
            return false;
        }
        if ($this->openssl_exe_path != '' && !file_exists($this->openssl_exe_path)) {
            $this->addError('Il percorso di OpenSSL non &egrave; valido: ' . $this->openssl_exe_path);
            return false;
        }

        return true;
    }

    /**
     * @param $file_source
     * @param $file_dest
     *
     * @return string
     */
    private function getCommand($file_source, $file_dest)
    {
        return $this->openssl_exe_path . "openssl rsautl -encrypt -in " . $file_source . " -out " . $file_dest . " -inkey " . $this->cert_file . " -certin -pkcs";
    }

    /**
     * @param $exec
     */
    private function excecuteCommand($exec)
    {
        $this->output = "";
        exec($exec . " 2>&1", $this->output, $this->returned_val);

        if ($this->returned_val == 1) // errore
        {
            $a = $this->output;
            $this->addError($a[0]);
        }
    }

    /**
     * @param $file_dest
     *
     * @return string
     */
    private function getEncryptedString($file_dest)
    {
        $encrypted_txt = "";
        if (file_exists($file_dest)) {
            $encrypted_txt = file_get_contents($file_dest);

            // Cancella i file di appoggio
            unlink($file_dest);
        } else {
            $this->addError("Criptazione fallita (file destinazione non esistente)");
        }

        return $encrypted_txt;
    }

    /**
     * @param $file_source
     */
    private function deleteSourceFile($file_source)
    {
        if (file_exists($file_source)) {
            unlink($file_source);
        } else {
            $this->addError("Criptazione fallita (file sorgente non esistente)");
        }
    }

    /**
     * @return string
     */
    private function getRandName()
    {
        return md5(time() . mt_rand(1, 99999));
    }
}
