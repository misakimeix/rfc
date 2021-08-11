<?php

/**
 * Created on Mar 6, 2020
 * Updata on Mar 6, 2020
 * @author Gabriel Assuero
 * @email gabrielassuerors@gmail.com
 * Version 1.0.0
 */

abstract Class MainController{


	private $content;
	private $idField;
	private $table;

	//Filter

	public static function Filter(string $filter_type, string $value){

		switch ($filter_type) {
			case 'email':
				return filter_var($value, FILTER_SANITIZE_EMAIL);
				break;

			case 'int':
				return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
				break;

			case 'float':
				return filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT);
				break;

			case 'all':
				return filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				break;

			case 'url':
				return filter_var($value, FILTER_SANITIZE_URL);
				break;

			default:
				return filter_var($value, FILTER_SANITIZE_STRING);
				break;
		}
	} 

	
	/**
	* CPU function 
	* author Rafael Neri
	* author URL https://about.me/rafaelneri
	*/

	public static function CPF($varlue){

	    $cpf = preg_replace( '/[^0-9]/is', '', $cpf );

	    if (strlen($cpf) != 11) {
	        return false;
	    }

	    if (preg_match('/(\d)\1{10}/', $cpf)) {
	        return false;
	    }

	    for ($t = 9; $t < 11; $t++) {
	        for ($d = 0, $c = 0; $c < $t; $c++) {
	            $d += $cpf{$c} * (($t + 1) - $c);
	        }
	        $d = ((10 * $d) % 11) % 10;
	        if ($cpf{$c} != $d) {
	            return false;
	        }
	    }
	    return true;

	}

	public static function Password(int $pars = 1, string $method = 'sha1', string $value){

		for ($i=0; $i < $pars; $i++) { 
			$value = hash($method, $value);
		}
		return $value;

	}

	public static function getUserIP(){

	    $client  = @$_SERVER['HTTP_CLIENT_IP'];
	    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
	    $remote  = $_SERVER['REMOTE_ADDR'];

	    if(filter_var($client, FILTER_VALIDATE_IP)){

	        $ip = $client;
	    }
	    elseif(filter_var($forward, FILTER_VALIDATE_IP)){

	        $ip = $forward;
	    }
	    else{

	        $ip = $remote;
	    }

	    return $ip;
	}


	public static function AccessLog(){

		$ip = self::getUserIP();
 		$date = date('d-m-Y');
		$time = date('H:i:s');

		$log = "IP: {$ip} - Date: {$date} - Hour: {$time}";

	}

	



}