<?php

if(!function_exists('asset2')){

	function asset2($pathToFile){
		return URL::to('').$pathToFile;
	}

}

if(!function_exists('link2')){

	function link2($url){
		return env('APP_URL').$url;
	}	

}



?>