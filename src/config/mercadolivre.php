<?php

return [

	/*
	* Para criar seu client_id e client_secret acesse 
	* http://applications.mercadolibre.com
	*/
	
	'client_id' => env('ML_APP_ID', ''), 
	
	'client_secret' => env('ML_APP_SECRET', ''), 

	'urls' => [
		'API_ROOT_URL' => 'https://api.mercadolibre.com', 
		'AUTH_URL'     => 'http://auth.mercadolivre.com.br/authorization', 
		'OAUTH_URL'    => '/oauth/token'
	], 

	'curl_opts' => [
		CURLOPT_USERAGENT => "MELI-PHP-SDK-1.0.0",
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_TIMEOUT => 60
	]
];