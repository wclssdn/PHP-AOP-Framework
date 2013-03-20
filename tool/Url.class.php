<?php
class Url {

	public static function href($url, array $params, $append = true) {
		$query = array();
		$append && $_SERVER['QUERY_STRING'] && parse_str($_SERVER['QUERY_STRING'], $query);
		foreach ($params as $k => $v){
			$query[$k] = $v;
		}
		if (!empty($query)){
			$url .= '?' . http_build_query($query);
		}
		return $url;
	}
}