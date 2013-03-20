<?php
/**
 * 配置
 * 支持一级子目录. 默认目录:PATH_CONFIG
 * @author wclssdn <ssdn@vip.qq.com>
 *
 */
class Config {

	private static $list = array();

	private function __construct() {
	}

	private function __clone() {
	}

	/**
	 * 加载配置
	 * @param string $file 配置名
	 * @param string $type 类型 子目录名称(区分配置文件用)
	 * @throws Exception
	 */
	public static function loadFile($file, $type = '') {
		if (!isset(self::$list[$type . $file])){
			if (strpos($file, '/') || strpos($file, '\\')){
				throw new Exception('Config File Name Wrong!');
			}
			if (strpos($type, '/') || strpos($type, '\\')){
				throw new Exception('Config File Type Wrong!');
			}
			$type = $type ? "{$type}/" : '';
			$filename = PATH_CONFIG . $type . $file . '.conf.php';
			if (!is_file($filename)){
				throw new Exception('Config File Not Exists');
			}
			self::$list[$type . $file] = include $filename;
		}
		return self::$list[$type . $file];
	}
}