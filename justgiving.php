<?php

$url = 'https://api.justgiving.com/8f717b90/v1/fundraising/pages/tweetsforballs';


$jg_data = cache_file_get_contents($url);

var_dump($jg_data);



// this is a cache map for several general functions
chdir( dirname ( __FILE__ ) );
//echo '<!-- Current working directory: ' . getcwd() . ' -->';

function file_get_contents_utf8($fn) {
     $content = file_get_contents($fn);
      return mb_convert_encoding($content, 'UTF-8',
          mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true));
} 

function cache_file_get_contents($url,$timeout = '3600'){
	$str = '';
	
	// check freshness
	if (file_exists('.cache/'.md5($url).'.tmp')){
		$fileTime = @filemtime($_SERVER['DOCUMENT_ROOT'].'dev/.cache/'.md5($url).'.tmp');	
		$timedif = @(time() - $fileTime);		
	} else {
		$timedif = 0;
		$timeout = -1;
	}
	
	if ($timedif < $timeout){
  		$str = @file_get_contents($_SERVER['DOCUMENT_ROOT'].'dev/.cache/'.md5($url).'.tmp');
	} else {
		
		// refetch the file
			$c = curl_init();
        	curl_setopt($c, CURLOPT_RETURNTRANSFER,true);
        	curl_setopt($c, CURLOPT_URL, str_replace('&amp;','&',$url));
        	curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 2);
        	curl_setopt($c, CURLOPT_TIMEOUT, 4);
        	curl_setopt($c, CURLOPT_USERAGENT, "curl from http://uefacalendar.com/ ");
        	curl_setopt($c, CURLOPT_FOLLOWLOCATION,1);
        	$str = curl_exec($c);
        	curl_close($c);
                
    		/*
		
		
		$temp = @fopen($url, "r");
		while (!@feof($temp)) {
			$str .= @fread($temp, 8192);
		}
		fclose($temp);
		*/
		
		// save it to the cache
		if ($f = @fopen($_SERVER['DOCUMENT_ROOT'].'dev/.cache/'.md5($url).'.tmp', 'w+')) {
			if (trim($str) != ''){
				fwrite ($f, 
				$str, 
				strlen($str));
			}
			fclose($f);
		}

	}
	// return the data
//	var_dump($str);
	return $str;
}

