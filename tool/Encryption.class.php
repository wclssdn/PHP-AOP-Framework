<?php

class Encryption{

	private static $secret = 'this is a secret key';

	public static function setSecret($secret){
		self::$secret = $secret;
	}

	/**
	 * 生成token
	 * @param string $arg1
	 * @param string $arg2
	 * @param string $argN
	 * @return string
	 */
	public static function token($arg1, $arg2){
		$tmp = '';
		foreach (func_get_args() as $arg){
			$tmp .= $arg;
		}
		return md5($tmp . self::$secret);
	}
}