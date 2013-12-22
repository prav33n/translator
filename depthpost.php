<?php
depthpost($_POST);
function depthpost($_POST)
{
	$filename = "C:\\xampp\\htdocs\\CompanyInfo\\Regulatory\\Results\\".$_POST['File'].'.html';
	$fp = fopen($filename, 'w');
	$cookie_jar = "C:\\xampp\\htdocs\\CompanyInfo\\cookie.txt";
    $options = array(       
	CURLOPT_RETURNTRANSFER => true,     // return web page        
	CURLOPT_HEADER         => false,    // don't return headers 
	CURLINFO_HEADER_OUT => true, // trace requests 
	CURLOPT_FOLLOWLOCATION => true,     // follow redirects 
	CURLOPT_USERAGENT      => "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5", 		// who am i 
	CURLOPT_COOKIEJAR => $cookie_jar, //set cookies
	CURLOPT_COOKIEFILE => $cookie_jar, //send cookies in file      
	CURLOPT_ENCODING       => "",       // handle all encodings        
	CURLOPT_USERAGENT      => "", 		// who am i        
	CURLOPT_AUTOREFERER    => true,     // set referer on redirect        
	CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect        
	CURLOPT_TIMEOUT        => 120,      // timeout on response        
	CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects   
	CURLOPT_SSL_VERIFYPEER => false,		
	CURLOPT_SSL_VERIFYHOST => false,
	CURLOPT_SSL_VERIFYHOST => false,
	CURLOPT_POST => false,  			//indicate to use post method
	);
	
	$ch      = curl_init( $_POST['INPUT'] );   
	curl_setopt_array( $ch, $options );    
	$content = curl_exec( $ch );    
	$err     = curl_errno( $ch );    
	$errmsg  = curl_error( $ch );    
	$header  = curl_getinfo( $ch );
	$header['errno']   = $err;    
	$header['errmsg']  = $errmsg;    
	$header['content'] = $content;
	fwrite($fp,str_replace("<head>","<head>\n<meta name=\"url\" content=\"".$_POST['INPUT']."\"></meta>", $content));
	fclose($fp);
	curl_close( $ch ); 
}


?>