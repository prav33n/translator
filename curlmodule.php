<?php
require 'depthget.php';
require 'depthpost.php';
set_time_limit (0);
if(count($argv)==6){
	$engine = $argv[1] ;
	$input= $argv[2];
	$output = $argv[3];
	$sourcelang = $argv[4];
	$targetlang = $argv[5];
}
else {
	$engine = "all";
	$input= $argv[1];
	$output = $argv[2];
	$sourcelang = $argv[3];
	$targetlang = $argv[4];
}

/*$engine = "google";
 $input= "c:\\xampp\\htdocs\\translator\\input\\input.txt";
 $output = "c:\\xampp\\htdocs\\translator\\result\\result.txt";
 $sourcelang = "en";
 $targetlang = "sch";*/

echo "Translating......";
readinputfile($engine,$input,$output,$sourcelang,$targetlang);
echo "Translation Complete";

function readinputfile($engine,$input,$output,$sourcelang,$targetlang)
{
	//echo $engine,$input,$output,$sourcelang,$targetlang;

	if($engine == "bing" || $engine == "all"){
		$fp = fopen($input, 'r') or die("Could not create file!");
		$query = null;
		$apikey = null;
		$url = null;
		$i=0;
		$apikey = dataext("http://www.bing.com/translator",true,0,"apikey",NULL);
		echo $apikey;
		while (!feof($fp)){
			$content = fgets($fp);
			if($content != ""){
				$query = $query. " | ".trim($content);
				$i++;
				if($i%50 == 0 && $i!=0){
					$url = "http://api.microsofttranslator.com/v2/ajax.svc/TranslateArray?appId=%22".$apikey."%22&texts=[%22".urlencode($query)."%22]&from=%22en%22&to=%22zh-chs%22";
					dataext($url,false,$i,"bing",$output);
					$query = "";
				}
			}
		}
		if($i%50 !=0){
			dataext($url,false,$i,"bing",$output);
		}
		fclose($fp);
	}
	if($engine == "google" || $engine == "all"){
		dataext("http://translate.google.com/",true,0,"cookie",NULL);
		$fp = fopen($input, 'r') or die("Could not create file!");
		$query = "";
		$url = null;
		$i=0;
		while (!feof($fp)){
			$content = fgets($fp);
			if($content != ""){
				$query = $query. " \n ".trim($content);
				$url = "http://translate.google.com/translate_a/t?client=t&sl=en&tl=zh-CN&hl=en&ie=UTF-8&oe=UTF-8&prev=btn&rom=1&ssel=0&tsel=0";
				$i++;
				if($i%50 == 0 && $i!=0){
					depthnav($url, false,0,"google", $output, "q=".urlencode($query));
					$query = "";
				}
			}
		}
		if($i%50 !=0){
			depthnav($url, false,0,"google", $output, "q=".urlencode($query));
		}
		fclose($fp);	
	}
}
?>