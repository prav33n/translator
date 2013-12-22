<?php 
$count = 0;
function dataext($url,$cookie,$id,$engine,$outputfile)
{ 
$cookie_jar = getcwd ()."\cookie.txt";
//$cookie_jar = "..\\cookie.txt";
if($cookie){
	$options = array(       
	CURLOPT_RETURNTRANSFER => true,     // return web page        
	CURLOPT_HEADER         => false,    // don't return headers 
	CURLINFO_HEADER_OUT => true, // trace requests 
	CURLOPT_USERAGENT      => "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5", 		// who am i
	CURLOPT_FOLLOWLOCATION => true,     // follow redirects        
	CURLOPT_ENCODING       => "",       // handle all encodings           
	CURLOPT_AUTOREFERER    => true,     // set referer on redirect 
	CURLOPT_COOKIEJAR => $cookie_jar, //set cookies
	CURLOPT_COOKIEFILE => $cookie_jar, //send cookies in file       
	CURLOPT_CONNECTTIMEOUT => 1200,      // timeout on connect        
	CURLOPT_TIMEOUT        => 1200,      // timeout on response        
	CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects   
	CURLOPT_SSL_VERIFYPEER => false,		
	CURLOPT_SSL_VERIFYHOST => false,
	CURLOPT_COOKIE => "",
	CURLOPT_HTTPAUTH=>CURLAUTH_BASIC
	); 
}
else{
$options = array(       
	CURLOPT_RETURNTRANSFER => true,     // return web page        
	CURLOPT_HEADER         => false,    // don't return headers 
	CURLINFO_HEADER_OUT => true, // trace requests 
	CURLOPT_USERAGENT      => "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5", 		// who am i
	CURLOPT_FOLLOWLOCATION => true,     // follow redirects        
	CURLOPT_ENCODING       => "",       // handle all encodings   
	CURLOPT_USERAGENT      => "", 		// who am i          
	CURLOPT_AUTOREFERER    => true,     // set referer on redirect 
	CURLOPT_COOKIEJAR => $cookie_jar, //set cookies
	CURLOPT_COOKIEFILE => $cookie_jar, //send cookies in file       
	CURLOPT_CONNECTTIMEOUT => 1200,      // timeout on connect        
	CURLOPT_TIMEOUT        => 1200,      // timeout on response        
	CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects   
	CURLOPT_SSL_VERIFYPEER => false,		
	CURLOPT_SSL_VERIFYHOST => false,
	CURLOPT_COOKIE => "",
	CURLOPT_HTTPAUTH=>CURLAUTH_BASIC,
	//CURLOPT_FILE => $fp,
	); 	
}


	$ch      = curl_init($url);   
	curl_setopt_array( $ch, $options );    
	$content = curl_exec( $ch );    
	$err     = curl_errno( $ch );    
	$errmsg  = curl_error( $ch );    
	$header  = curl_getinfo( $ch );
	/*if($engine == "bing" && $cookie){
		var_dump($content);
		$doc = new DOMDocument();
		$doc->loadHTML($content);
		var_dump($content,$doc);
	}*/
	
	if($engine == "bing"){
	//echo getcwd();
	$fp = fopen($outputfile, 'a+') or die("Could not create file!");
	//$nMatches = preg_match_all('/[\[\]].*/', $content,$matches);
	//var_dump($content);
	$replace = preg_replace('/(﻿)/', '', $content);
	$json = json_decode($replace,FALSE);
	//var_dump($json);
	$translatearray = explode("|",$json[0]->TranslatedText);
	//var_dump($translatearray,count($translatearray));
	$i =$id;
	foreach ($translatearray as $value) {
		if($value!=""){
			fwrite($fp, $i." ;");
			fwrite($fp,"\t".$value."\n");
			$i++;
		}	
	}
	//fwrite($fp,"\t".$json[0]->TranslatedText."\n");
	fclose($fp);
	}
	elseif($engine == "google"){
		$fp = fopen($outputfile, 'a+') or die("Could not create file!");
		fwrite($fp, $id." ;");
		$nMatches = preg_split('/[\[\]]/', $content);
		$matches = preg_split('/[""]/', $nMatches[3]);
		fwrite($fp,"\t".$matches[1]."\n");
		fclose($fp);
	}
	curl_close( $ch );
	//$json =    json_decode(json_encode($content));
	//var_dump($content);
}
?>
