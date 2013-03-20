<?php
/**
 * 数据库工厂
 * 数据库配置文件位置: PATH_CONFIG/db/$sign.conf.php
 * @author wclssdn <ssdn@vip.qq.com>
 *
 */
class DbDaoFactory {

	private function __construct() {
	}

	private function __clone() {
	}

	public static function getDbDao($sign) {
		static $cache = array();
		if (!isset($cache[$sign])){
			$config = Config::loadFile($sign, 'db');
			if (!is_array($config)){
				throw new Exception('Db config is not a array!');
			}
			$cache[$sign] = new MysqlDao($config);
		}
		return $cache[$sign];
	}
}