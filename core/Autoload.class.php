<?php
/**
 * 自动加载
 * @author wclssdn <ssdn@vip.qq.com>
 *
 */
class Autoload {

	private static $rootPath;

	static $subPath = array();

	static $included = array();

	static $otherPath = array();

	public static function setRootPath($rootPath) {
		self::$rootPath = $rootPath;
	}

	/**
	 * 添加相对路径包含目录,相对于rootPath
	 * @param string $path
	 */
	public static function addSubPath($path) {
		array_push(self::$subPath, $path);
		self::$subPath = array_unique(self::$subPath);
	}

	/**
	 * 添加绝对路径包含目录
	 * @param string $path
	 */
	public static function addOtherPath($path) {
		if (file_exists($path)){
			array_push(self::$otherPath, $path);
			self::$otherPath = array_unique(self::$otherPath);
		}
	}

	private static function auto($className) {
		if (isset(self::$included[$className])){
			return true;
		}
		$filename = '';
		$match = null;
		if (preg_match('#^[a-z0-9]+(Controller|Model|Filter)$#is', $className, $match) !== 0){
			$filename = self::$rootPath . strtolower($match[1]) . '/' . $className . '.class.php';
		}
		if (!$filename){
			$filename = self::$rootPath . 'core/' . $className . '.class.php';
		}
		if (self::includeFile($filename, $className)){
			return true;
		}
		if (!empty(self::$subPath)){
			foreach (self::$subPath as $path){
				$filename = self::$rootPath . $path . '/' . $className . '.class.php';
				if (self::includeFile($filename, $className)){
					return true;
				}
			}
		}
		if (!empty(self::$otherPath)){
			foreach (self::$otherPath as $path){
				$filename = $path . '/' . $className . '.class.php';
				if (self::includeFile($filename, $className)){
					return true;
				}
				$filename = $path . '/' . $className . '.php';
				if (self::includeFile($filename, $className)){
					return true;
				}
			}
		}
		return false;
	}

	private static function includeFile($filename, $className) {
		if (strpos($filename, '/') === false){
			include $filename;
			return true;
		}
		if (is_file($filename)){
			include $filename;
			if (class_exists($className, false)){
				self::$included[$className] = true;
				return true;
			}
		}
		return false;
	}

	/**
	 * 开始自动加载
	 */
	public static function start() {
		spl_autoload_register(array('Autoload', 'auto'));
		if (function_exists('__autoload')){
			spl_autoload_register('__autoload');
		}
	}
}