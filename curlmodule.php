<?php
require 'depthget.php';
set_time_limit (0);
if(count($argv)==5){
$engine = $argv[1] ;
$input= $argv[2];
$output = $argv[3];
$sourcelang = $argv[4];
$targetlang = $argv[5];
}
else {
$input= $argv[1];
$output = $argv[2];
$sourcelang = $argv[3];
$targetlang = $argv[4];
}
echo $engine,"\n",$input,"\n",$output,"\n",$sourcelang,"\n",$targetlang;
echo "Translating......";
//getfilelist();
echo "Translation Complete";

function getfilelist(){
	//echo chdir("input");
	//echo getcwd();
	
	foreach (glob(getcwd()."\\input\\*.txt") as $filename) {
		//echo $filename;
		//echo basename($filename, ".txt");
		readinputfile($filename);
		//rename($filename, $filename.".".time());
  		//$fp = fopen("$filename", 'r') or die("Could not create file!");
  		//unlink($filename);
  		//break;
  		//fclose($fp);
  		
	}
	
	//chdir("..");
	//echo getcwd();
	//$dir = opendir(getcwd());
	//$files = scandir(getcwd());
	//echo readdir($dir);
	//var_dump($files);
}

function readinputfile($filename)
{
//bing translation
$fp = fopen($filename, 'r') or die("Could not create file!");
$i =0;
$query = "";
dataext("http://www.bing.com/translator",true,0,"cookie",NULL);
$outputfile = getcwd()."\\result\\".basename($filename, ".txt")."-result-bing-".time().".txt";
while (!feof($fp)){
	$content = fgetcsv($fp,null,";");
	$id = $content[0];
	$text = $content[1];
	$query = $query. " | ".trim($text);
	if($id != "" && $text != ""){
	if($id%50 == 0 && $i !==0){
		$url = "http://api.microsofttranslator.com/v2/ajax.svc/TranslateArray2?appId=%22TjWqVfutJR1Zf3gXGmMzh7u-Zj9ZXmSZvvXOvGIaJRHl4O0T4UFqp9zFLhrywZxXj%22&texts=[%22".urlencode(trim($query))."%22]&from=%22en%22&to=%22zh-chs%22";
		//echo $url."<br>";
		dataext($url,false,$id-49,"bing",$outputfile);
		$query = "";
		//break;
		}
	$i++;
	}
//	echo urlencode (trim($text)),"<br>";
}
fclose($fp);
//googel translation
$fp = fopen($filename, 'r') or die("Could not create file!");
dataext("http://translate.google.com/",true,0,"cookie",NULL);
$i = 0;
$outputfile = getcwd()."\\result\\".basename($filename, ".txt")."-result-google-".time().".txt";
while (!feof($fp)){
	$content = fgetcsv($fp,null,";");
	$id = $content[0];
	$text = $content[1];
	$query = $query ." | ".trim($text);
	if($id != "" && $text != ""){
		$url ="http://translate.google.com/translate_a/t?client=t&sl=en&tl=zh-CN&hl=en&sc=2&ie=UTF-8&oe=UTF-8&pc=1&oc=1&otf=1&ssel=0&tsel=0&q=".urlencode($text);
		dataext($url,false,$id,"google",$outputfile);
	/*if($i%5 == 0 && $i !==0){
		$url ="http://translate.google.com/translate_a/t?client=t&sl=en&tl=zh-CN&hl=en&sc=2&ie=UTF-8&oe=UTF-8&pc=1&oc=1&otf=1&ssel=0&tsel=0&q=".urlencode($query);
		echo $url."<br>";
		dataext($url,false,$id,"google");
		$query = "";
		}*/
	//$i++;
	}
	//break;
}
fclose($fp);
}
?>