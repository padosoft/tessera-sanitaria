<?php
namespace Padosoft\TesseraSanitaria;

use League\Flysystem\Filesystem;
use League\Flysystem\ZipArchive\ZipArchiveAdapter as AdapterZip;
//local file
use League\Flysystem\Adapter\Local as AdapterLocal;

/**
 * Class IOHelper
 * @package Padosoft\TesseraSanitaria
 */
class IOHelper
{
	/**
	 *
	 */
	public static function printDir()
	{
		echo '<br /><br />
		<div class="page-header"><h1>Elenco File Generati:</h1></div>
		';

		self::getDir();
	}

	/**
	 * @param string $dir
	 */
	public static function getDir($dir = "output/")
	{
		if(empty($dir)){
			$dir = "output";
		}

		$arr = array();
		
		if ($handle = opendir($dir))
		{
			while (false !== ($entry = readdir($handle)))
			{
				if($entry != "." && $entry != "..")
				{					
					$zipnome = str_replace(".xml",".zip",$entry);
					$zip="";
					if(file_exists($dir.$zipnome))
					{
						$zip = " | <a href=\"".$dir.$zipnome."\">ZIP</a>";
					}
					if(file_exists($dir.$entry) && strpos($entry,".xml"))
					{
						$arr[] = "<a target=\"_blank\" href=\"".$dir.$entry."\">".$entry."</a>".$zip."<br>";
					}
				}
			}
		closedir($handle);
		}
		if(is_array($arr))
		{
			$arr = array_reverse($arr);
			foreach ($arr as $a)
			{
				echo $a;	
			}
		}
	}

	/**
	 * @param string $dir
	 */
	public static function cleandir($dir = "output")
	{ 
		if(empty($dir)){
			$dir = "output";
		}

		# $logger->addInfo("Deleting $dir ...");
		$files = glob($dir."/*");
		if(is_array($files))
			{
			foreach($files as $file)
				{ 
					if(is_file($file))
					{					
						unlink($file);
						#$logger->addInfo("$file Deleted.");
					}
				}
			}
	}

	/**
	 * @param $str
	 * @param $pathOutput
	 * @param $basePath
	 */
	public static function outputFile($str, $pathOutput, $basePath)
	{		
		$filesystem = new Filesystem(new AdapterLocal($basePath));	
		$filesystem->put(basename($pathOutput), $str);
	}

	/**
	 * @param $source
	 * @param $destination
	 */
	public static function zipFile($source, $destination)
	{		
		$filesystem = new Filesystem(new AdapterZip($destination));	
		$filesystem->put(basename($source), file_get_contents($source));
		$filesystem->getAdapter()->getArchive()->close();	
	}

	/**
	 * @param $source
	 * @param $destination
	 * @param $str
	 */
	public static function zipFileOntheFly($source, $destination, $str)
	{		
		$filesystem = new Filesystem(new AdapterZip($destination));	
		$filesystem->put(basename($source), $str);
		$filesystem->getAdapter()->getArchive()->close();	
	}
}