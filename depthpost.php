<?php
function depthnav ($url, $cookie, $id, $engine, $fout, $postdata)
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
	CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded;charset=UTF-8'),
	//CURLOPT_PROXY => "103.249.240.53:8080",
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
	//var_dump($postdata);
	//var_dump($content);	
//preg_match('/└ (.*?)┘/u', $content, $temp);
//$result = '[["'.$temp[0].'"]]';
	//$json = json_decode($content) or die("not valid json format");
	$found = preg_match ('/(\[\[).*(\]\]\,\,)/', $content, $r);
	$found = preg_match ('/(\[\[).*(\]\])/', $r[0],$new);
	$found = preg_split('/^[\s\S]{0,1}/', $new[0]);
	$json = json_decode($found[1]) or die("not valid json format");
	var_dump($found[1]);
	foreach ($json as $value){
		if ($value[0] != "") {
			fwrite ($fout, $value[0]);
		}
	}
	fwrite ($fout,"\n");
	curl_close( $ch ); 
}
?>