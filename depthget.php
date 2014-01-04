<?php
$count = 0;
function dataext ($url, $cookie, $id, $engine, $fout) {
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
		);
	}

	$ch      = curl_init($url);
	curl_setopt_array( $ch, $options );
	$content = curl_exec( $ch );
	$err     = curl_errno( $ch );
	$errmsg  = curl_error( $ch );
	$header  = curl_getinfo( $ch );
	if ($engine == "apikey"){
		$found = preg_match ('/Default\.Constants\.AjaxApiAppId\s*=\s*\'(.*?)\'\s*;/is', $content, $r);
		if ($found) {
			$sessionid = trim($r[1]);
			return ($sessionid);
		}
	}
	else if ($engine == "bing"){
		$replace = preg_replace('/(﻿)/', '', $content);
		$json = json_decode($replace,FALSE);
		$translatearray = explode("|",$json[0]->TranslatedText);
		foreach ($translatearray as $value) {
			if ($value != ""){
				fwrite ($fout, $value."\n");
			}
		}
	}
	elseif($engine == "google"){
		fwrite ($fout, $id." ;");
		$nMatches = preg_split('/[\[\]]/', $content);
		$matches = preg_split('/[""]/', $nMatches[3]);
		fwrite ($fout,"\t".$matches[1]."\n");
	}
	curl_close( $ch );
	//$json =    json_decode(json_encode($content));
	//var_dump($content);
}
?>
