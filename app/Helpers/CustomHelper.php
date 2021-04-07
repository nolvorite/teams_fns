<?php

if(!function_exists('asset2')){

	function asset2($pathToFile){
		return 'https://sitetests.com:81/'.$pathToFile;
	}

}

if(!function_exists('link2')){

	function link2($url){
		return 'https://sitetests.com:81/'.$url;
	}	

}



?>