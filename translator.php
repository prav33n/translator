<?php
require_once('depthget.php');
require_once('depthpost.php');

function TranslateBulk ($engine, $blocksize, $inputfilename, $outputfilename, $sourcelang, $targetlang, $batchstart, $batchend) {
echo "Translating......\n";
	$fout = fopen($outputfilename, 'w') or die("Could not create output file!");
	if ($engine == "bing") {
		$fp = fopen($inputfilename, 'r') or die("Could not open input file!");
		$query = null;
		$apikey = null;
		$url = null;
		$nline = -1;
		$apikey = dataext ("http://www.bing.com/translator", true, 0, "apikey", NULL);
		echo $apikey;
		while (!feof($fp)) {
			$nline ++;
			$line = fgets($fp);
			if ($nline < $batchstart)
				continue;
			if ($nline >= $batchend)
				break;
			$line = trim(preg_replace('/；/', ';', $line));
			$found = preg_match('/(\w+)\s*;\s*;/', $line, $r);
			if (!$found) {
				$line = trim(preg_replace('/;/', ';;', $line));
				$found = preg_match('/(\w+)\s*;\s*;(.*?)$/is', $line, $r);
				if (!$found) {
					$found = preg_match('/(\w+)\s*(.*?)$/is', $line, $r);
					if ($found)
						$line = $r[1].";;".$r[2];
				}		
			}	
			if (empty($line))
				continue;
			$content = preg_split('/;/', $line);
			$id = trim($content[0]);
			$text = trim($content[2]);
			if (!empty($query))
				$query .= " | ";
			$query .= $id." ;; ".$text;
			if ($nline % $blocksize == 0 && $nline != 0){
				$url = "http://api.microsofttranslator.com/v2/ajax.svc/TranslateArray?appId=%22".$apikey."%22&texts=[%22".urlencode($query)."%22]&from=%22".$sourcelang."%22&to=%22".$targetlang."%22";
				sleep(2);
				dataext ($url, false, $nline, "bing", $fout);
				$query = "";
			}
		}
		if (!empty($query)) {
			$url = "http://api.microsofttranslator.com/v2/ajax.svc/TranslateArray?appId=%22".$apikey."%22&texts=[%22".urlencode($query)."%22]&from=%22".$sourcelang."%22&to=%22".$targetlang."%22";
			sleep(2);
			dataext ($url, false, $nline, "bing", $fout);
		}
		fclose($fp);
	}
	if ($engine == "google") {
		dataext ("http://translate.google.com/", true, 0, "cookie", NULL);
		$fp = fopen($inputfilename, 'r') or die("Could not create file!");
		$query = "";
		$url = null;
		$nline = -1;
		while (!feof($fp)){
			$nline ++;
			$line = fgets($fp);
			if ($nline < $batchstart)
				continue;
			if ($nline >= $batchend)
				break;
			$line = trim(preg_replace('/；/', ';', $line));
			$found = preg_match('/(\w+)\s*;\s*;/', $line, $r);
			if (!$found) {
				$line = trim(preg_replace('/;/', ';;', $line));
				$found = preg_match('/(\w+)\s*;\s*;(.*?)$/is', $line, $r);
				if (!$found) {
					$found = preg_match('/(\w+)\s*(.*?)$/is', $line, $r);
					if ($found)
						$line = $r[1].";;".$r[2];
				}		
			}	
			if (empty($line))
				continue;
			$content = preg_split('/;/', $line);
			$id = trim($content[0]);
			$text = trim($content[2]);
			if (!empty($query))
				$query .= " \n ";
			$query .= $id." ;; ".$text."|";
			if ($nline % $blocksize == 0 && $nline != 0){
				var_dump(count($query));
				$url = 'https://www.googleapis.com/language/translate/v2?key=AIzaSyAScG4vpDM8iAcMyhKEzWf0xfaV3pV6ZFY&q=' . rawurlencode($query) . '&source='.$sourcelang.'&target='.$targetlang;
			//	$url = "http://translate.google.com/translate_a/t?client=t&sl=".$sourcelang."&tl=".$targetlang."&hl=en&ie=UTF-8&oe=UTF-8&prev=btn&rom=1&ssel=0&tsel=0";
				var_dump($url);
				//depthnav ($url, false, 0, "google", $fout, "q=".urlencode($query));
				dataext ($url, false, $nline, "google", $fout);
//				sleep(2);
				sleep(10);
				$query = "";
				//break;
			}
		}
		if (!empty($query)) {
			$url = 'https://www.googleapis.com/language/translate/v2?key=AIzaSyAScG4vpDM8iAcMyhKEzWf0xfaV3pV6ZFY&q=' . rawurlencode($query) . '&source='.$sourcelang.'&target='.$targetlang;
			dataext ($url, false, $nline, "google", $fout);
			//$url = "http://translate.google.com/translate_a/t?client=t&sl=".$sourcelang."&tl=".$targetlang."&hl=en&ie=UTF-8&oe=UTF-8&prev=btn&rom=1&ssel=0&tsel=0";
			//depthnav ($url, false, 0, "google", $fout, "q=".urlencode($query));
		}
		fclose($fp);	
	}
	fclose ($fout);
echo "Translation Complete\n";
}

function ExtractBT ($infilename, $resfilename) {
	$content = file_get_contents ($infilename);
	$fr = fopen ($resfilename, 'w');
	$nmatches = preg_match_all ('/\"TranslatedText\"\:\"([^\"]*?)\"\s*,\s*\"TranslatedTextSentenceLengths\"/uis', $content, $matches, PREG_SET_ORDER);
	foreach ($matches as $m) {
		$scope = $m[1]."|";
		$nmatches1 = preg_match_all ('/([^\"]*?)\|/uis', $scope, $matches1, PREG_SET_ORDER);
		foreach ($matches1 as $m1) {
			$line = $m1[1];
			$line = trim(preg_replace('/；/', ';', $line));
			$found = preg_match('/;\s*;/', $line, $r);
			if (!$found)
				$line = trim(preg_replace('/;/', ';;', $line));
			if (empty($line))
				continue;
			fwrite ($fr, $line."\n");	
		}
	}
	fclose ($fr);
}

function ExtractGT ($infilename, $resfilename) {
	$content = file_get_contents ($infilename);
	$fr = fopen ($resfilename, 'w');
	$nmatches = preg_match_all ('/\[\"([^\]]*?)\]\s*,/uis', $content, $matches, PREG_SET_ORDER);
	foreach ($matches as $m) {
		$found = preg_match('/^(.*?)\\\n/uis', $m[1], $r);
		if ($found) {
			$line = $r[1];
			$line = trim(preg_replace('/；/', ';', $line));
			$found = preg_match('/;\s*;/', $line, $r);
			if (!$found)
				$line = trim(preg_replace('/;/', ';;', $line));
			if (empty($line))
				continue;
			fwrite ($fr, $line."\n");	
		}
	}
	fclose ($fr);
}

function main() {
// main function
	
echo ("came to main\n");
//die();	

set_time_limit (0);

	global $cfg;
	$action = $cfg['action'];
	if ($action == "translate_bulk")
		TranslateBulk ($cfg['engine'], $cfg['bulksize'], $cfg['infilename'], $cfg['resfilename'], $cfg['langfrom'], $cfg['langto'], $cfg['batchstart'], $cfg['batchend']);
	if ($action == "extract_bt")
		ExtractBT ($cfg['infilename'], $cfg['resfilename']);
	if ($action == "extract_gt")
		ExtractGT ($cfg['infilename'], $cfg['resfilename']);
}

$cfg = array();
$cfg['action'] = $argv[1];

if ($cfg['action'] == "translate_bulk") {
	$cfg['engine'] 		= $argv[2];
	$cfg['bulksize'] 	= $argv[3];
	$cfg['infilename'] 	= $argv[4];
	$cfg['resfilename'] = $argv[5];
	$cfg['langfrom'] 	= $argv[6];
	$cfg['langto'] 		= $argv[7];
	$cfg['batchstart'] 	= $argv[8];
	$cfg['batchend'] 	= $argv[9];
	if ($argc != 10) {
		print ("Translate\n");
		die ("Usage: php -f Translate.php translate_bulk <engine> <bulksize> <infilename> <resfilename> <langfrom> <langto> <batchstart> <batchend>");
	}
}
if ($cfg['action'] == "extract_bt") {
	$cfg['infilename'] 	= $argv[2];
	$cfg['resfilename'] = $argv[3];
	if ($argc != 4) {
		print ("Translate\n");
		die ("Usage: php -f Translate.php extract_bt <infilename> <resfilename>");
	}
}
if ($cfg['action'] == "extract_gt") {
	$cfg['infilename'] 	= $argv[2];
	$cfg['resfilename'] = $argv[3];
	if ($argc != 4) {
		print ("Translate\n");
		die ("Usage: php -f Translate.php extract_gt <infilename> <resfilename>");
	}
}

main();

?>