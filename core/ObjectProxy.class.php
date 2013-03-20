<?php
/**
 * 对象代理
 * @author wclssdn <ssdn@vip.qq.com>
 *
 */
class ObjectProxy {

	/**
	 * 被代理的对象
	 * @var object
	 */
	private $object;

	/**
	 * 执行静态方法的类名
	 * @var string
	 */
	public static $classname;

	public function __construct($object = null) {
		$this->object = $object;
	}

	/**
	 * 拦截被调用对象的方法调用
	 * @param string $method
	 * @param array $args
	 * @return mixed
	 * @throws Exception
	 */
	public function __call($method, array $args) {
		$reflectionObject = new ReflectionObject($this->object);
		try {
			$reflectionMethod = $reflectionObject->getMethod($method);
		}catch (ReflectionException $e){
			throw new Exception('method ' . $method . ' not exists in ' . get_class($this->object));
		}
		if (!$reflectionMethod->isPublic()){
			throw new Exception('method ' . $method . ' not public in ' . get_class($this->object));
		}
		//AOP执行目标调用
		$aop = new Aop(array($this->object, $method), $args);
		return $aop->execute();
	}

	/**
	 * 拦截静态方法调用
	 * @param string $method
	 * @param array $args
	 */
	public static function __callstatic($method, array $args){
		//AOP执行目标调用
		$aop = new Aop(array(self::$classname, $method), $args);
		return $aop->execute();
	}
}