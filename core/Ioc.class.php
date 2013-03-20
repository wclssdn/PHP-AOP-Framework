<?php
/**
 * 控制反转
 * @author wclssdn <ssdn@vip.qq.com>
 *
 */
class Ioc {

	/**
	 * 对象缓存
	 * @var array
	 */
	private static $objects = array();

	private function __construct() {
	}

	private function __clone() {
	}

	/**
	 * 加载代理对象
	 * @param string $classname
	 * @param array $args
	 * @param string $sign 如不想使用单例对象, 使用不同的sign区分不同实例
	 * @return object
	 */
	public static function loadObject($classname, array $args = array(), $sign = '') {
		$sign = $classname . md5(serialize($args)) . $sign;
		if (!isset(self::$objects[$sign])){
			$r = new ReflectionClass($classname);
			$constructor = $r->getConstructor();
			if ($constructor !== null && $constructor->getParameters()){
				if (empty($args) && file_exists($conFile = PATH_CONFIG . 'ioc/' . $classname . '.conf.php')){
					$args = include $conFile;
				}
				$object = $r->newInstanceArgs($args);
			}else{
				$object = $r->newInstance();
			}
			self::$objects[$sign] = self::getProxy($object);
		}
		return self::$objects[$sign];
	}

	/**
	 * 提供切面,执行对象的方法
	 * @param object $object
	 * @param string $method
	 */
	public static function call($object, $method, $args = array()){
		if (!is_object($object)){
			return false;
			
		}
		return self::getProxy($object)->$method($args);
	}

	/**
	 * 提供切面, 调用静态方法
	 * @param string $classname
	 * @param string $method
	 * @param array $args
	 */
	public static function callStatic($classname, $method, $args = array()){
		if (!is_scalar($classname)){
			return false;
		}
		$proxy = self::getProxy(new stdClass());
		$proxy::$classname = $classname;
		return call_user_func_array("ObjectProxy::{$method}", $args);
	}

	/**
	 * 获取对象的代理对象
	 * @param object $object
	 * @return ObjectProxy
	 */
	private static function getProxy($object) {
		return new ObjectProxy($object);
	}
}