<?php

/**
* @author Balaji
* @name AtoZ SEO Tools
* @copyright 2025 ProThemes.Biz
*
*/

function curlGET_TextG($url){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER,0); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_AUTOREFERER,1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')){
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    }
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36');
	curl_setopt($ch, CURLOPT_HTTPHEADER,array ("Accept: text/plain"));
	$html=curl_exec($ch);
    curl_close($ch);
    return $html;
}

function googleIndex($site) {
    $searchQuery = "site:$site";
    $con = $GLOBALS['con'];
    $apiKey = getGoogleCSEKey($con);
    $output = googleCSESearch($searchQuery, $apiKey);
    return $output['indexCount'];
}