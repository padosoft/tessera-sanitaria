<?php
namespace Padosoft\TesseraSanitaria;

/**
 * Class CryptoHelper
 * @package Padosoft\TesseraSanitaria
 */
class CryptoHelper
{
	use traits\Errorable;
	
	protected $cert_file="";
	protected $tmp_path="";
	protected $openssl_exe_path="";
	public $output="";
	protected $returned_val="";

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
		if(!file_exists($this->tmp_path)){
			$this->AddError('Il percorso della path temporanea non &egrave; valido: '.$this->tmp_path);
			return '';
		}
		if(!file_exists($this->cert_file)){
			$this->AddError('Il percorso del file del certificato non &egrave; valido: '.$this->cert_file);
			return '';
		}
		if($this->openssl_exe_path!='' && !file_exists($this->openssl_exe_path)){
			$this->AddError('Il percorso di OpenSSL non &egrave; valido: '.$this->openssl_exe_path);
			return '';
		}
		// Path e nomi dei file temporanei
		$rand_name = md5(time().rand(1,99999));
		$file_source = $this->tmp_path.$rand_name.".txt";
		$file_dest = $this->tmp_path.$rand_name.".enc";
		
		// Scrive file temporaneo sorgente
		file_put_contents($file_source, $str);
		
		// Definisce istruzione openssl
		$exec = $this->openssl_exe_path."openssl rsautl -encrypt -in ".$file_source." -out ".$file_dest." -inkey ".$this->cert_file." -certin -pkcs";

		// Esegue istruzione openssl, creando file temporaneo con testo criptato
		$this->output = "";
		exec($exec." 2>&1", $this->output, $this->returned_val);
		
		if($this->returned_val == 1) // errore
		{
			$a = $this->output;
			$this->AddError($a[0]);
		}

		// Ricava il testo criptato dal file appena creato
		$encrypted_txt = "";
		if(file_exists($file_dest))
		{
			$encrypted_txt = file_get_contents($file_dest);

			// Cancella i file di appoggio
			unlink($file_dest);
		}
		else
		{
			$this->AddError("Criptazione fallita (file destinazione)");
		}
		
		if(file_exists($file_source))
		{
			unlink($file_source);
		}
		else
		{
			$this->AddError("Criptazione fallita (file sorgente)");
		}

		return $encrypted_txt;
	}

	/**
	 * @return bool
	 */
	public function getError()
	{
		$result = FALSE;
		if($this->returned_val == 1){
			$result = TRUE;
		}
		return $result;
	}
}