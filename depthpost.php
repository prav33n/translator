<?php
function depthnav($url,$cookie,$id,$engine,$outputfile,$postdata)
{
	$cookie_jar = getcwd ()."\cookie.txt";
    $options = array(       
	CURLOPT_RETURNTRANSFER => TRUE,     // return web page        
	CURLOPT_HEADER         => false,    // don't return headers 
	CURLINFO_HEADER_OUT => true, // trace requests 
	CURLOPT_FOLLOWLOCATION => true,     // follow redirects        
	CURLOPT_ENCODING       => "",       // handle all encodings 
	CURLOPT_COOKIEJAR => $cookie_jar, //set cookies
	CURLOPT_COOKIEFILE => $cookie_jar, //send cookies in file       
	CURLOPT_USERAGENT      => "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5", 		// who am i        
	CURLOPT_AUTOREFERER    => true,     // set referer on redirect        
	CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect        
	CURLOPT_TIMEOUT        => 120,      // timeout on response        
	CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects   
	CURLOPT_SSL_VERIFYPEER => false,		
	CURLOPT_SSL_VERIFYHOST => false,
	CURLOPT_POST => true,  			//indicate to use post method
	CURLOPT_POSTFIELDS =>$postdata, 
	CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded;charset=UTF-8')
	);    
	$ch      = curl_init($url);   
	curl_setopt_array( $ch, $options );    
	$content = curl_exec( $ch );  
	$err     = curl_errno( $ch );    
	$errmsg  = curl_error( $ch );    
	$header  = curl_getinfo( $ch );
	$header['errno']   = $err;    
	$header['errmsg']  = $errmsg;    
	$header['content'] = $content;
	$fp = fopen($outputfile, 'a+') or die("Could not create file!");
	$found = preg_match ('/(\[\[).*(\]\])/', $content, $r);
	$found = preg_split('/^[\s\S]{0,1}/', $r[0]);
	$json = json_decode($found[1]);
	foreach ($json as $value){
		var_dump($value);
			if($value[0]!=""){
				fwrite($fp, $value[0]);
			}
	}
	fclose($fp);
	//$matches = preg_split('/(\]\])/', $nMatches[1]);
	//var_dump($matches);
	//fwrite($fp,"\t".$matches[1]."\n");
	//fclose($fp);
	curl_close( $ch ); 
}



?>