<?php
class Debug{
	
	public static function dump($var){
		static $header = false;
		$header === false && $header = true && header("Content-type:text/html; charset=utf-8", true, 200);
		echo '<pre>', PHP_EOL;
		foreach (func_get_args() as $arg){
			var_dump($arg);
		}
		echo '</pre>', PHP_EOL;
	}
}