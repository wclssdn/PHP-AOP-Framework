<?php
/**
 * 过滤器
 * @author wclssdn <ssdn@vip.qq.com>
 *
 */
class Filter {

	const QUIT_TRUE = 1;

	const QUIT_FALSE = 2;

	const QUIT_EMPTY = 4;

	const QUIT_NOT_EMPTY = 8;

	const QUIT_ARRAY = 16;

	const QUIT_ALL = 32;

	private $classname;

	private $classArgs;

	private $method;

	private $methodArgs;

	/**
	 * 是否使用同一对象
	 * @var boolean
	 */
	private $singleton;

	/**
	 * 必须执行
	 * @var boolean
	 */
	private $mustExecute;

	/**
	 * 提前返回
	 * @var boolean
	 */
	private $quit = false;

	/**
	 * 提前返回条件
	 * @var mixed
	 */
	private $quitCondition;

	/**
	 * aop单例对象缓存
	 * @var array
	 */
	static $singletons = array();

	public function __construct($classname, $classArgs, $method, $methodArgs, $singleton = true, $mustExecute = false, $quitCondition = '') {
		$this->classname = $classname;
		$this->classArgs = $classArgs;
		$this->method = $method;
		$this->methodArgs = $methodArgs;
		$this->singleton = $singleton;
		$this->mustExecute = $mustExecute;
		$this->quitCondition = $quitCondition;
	}

	public function replaceParameters($object, $class, $method, $args, $result, $lastResult) {
		if ($this->classArgs instanceof AopArguments){
			$this->classArgs = $this->classArgs->getArgs($object, $class, $method, $args, $result, $lastResult);
		}
		if ($this->methodArgs instanceof AopArguments){
			$this->methodArgs = $this->methodArgs->getArgs($object, $class, $method, $args, $result, $lastResult);
		}
	}

	public function execute() {
		isset($_GET['debug']) && print('<br>execute '. $this->classname. ':'. $this->method. '<br>'. PHP_EOL);
		$reflectionClass = new ReflectionClass($this->classname);
		try{
			$method = $reflectionClass->getMethod($this->method);
		} catch (ReflectionException $re){
			return false;
		}
		if ($method->isPrivate()){
			return false;
		}
		if ($method->isStatic()){
			$result = $method->invokeArgs(null, $this->methodArgs);
		}else{
			$obj = $this->getObj();
			if ($obj === false){
				return false;
			}
			$result = $method->invokeArgs($obj, $this->methodArgs);
		}
		if ($this->isQuit($result)){
			$this->quit = true;
		}
		return $result;
	}

	public function must() {
		return $this->mustExecute;
	}

	public function quit() {
		return $this->quit;
	}

	private function isQuit($result) {
		switch ($this->quitCondition){
			case self::QUIT_ALL:
				return true;
			case self::QUIT_ARRAY:
				if (is_array($result)){
					return true;
				}
				break;
			case self::QUIT_EMPTY:
				if (empty($result)){
					return true;
				}
				break;
			case self::QUIT_NOT_EMPTY:
				if (!empty($result)){
					return true;
				}
				break;
			case self::QUIT_TRUE:
				if ($result === true){
					return true;
				}
				break;
			case self::QUIT_FALSE:
				if ($result === false){
					return true;
				}
				break;
		}
		return false;
	}

	private function getObj() {
		if ($this->singleton){
			if (!isset(self::$singletons[$this->classname])){
				self::$singletons[$this->classname] = $this->newobj();
			}
			return self::$singletons[$this->classname];
		} else{
			return $this->newobj();
		}
	}

	private function newobj() {
		$reflectionClass = new ReflectionClass($this->classname);
		$constructor = $reflectionClass->getConstructor();
		if ($constructor === null){
			$cache[$this->classname] = $reflectionClass->newInstanceArgs();
			return $cache[$this->classname];
		}elseif ($constructor !== null && $constructor->isPublic()){
			$cache[$this->classname] = $reflectionClass->newInstanceArgs($this->classArgs);
			return $cache[$this->classname];
		}
		if ($reflectionClass->hasMethod('getInstance')){ // 约定单例存在此方法
			$getInstance = $reflectionClass->getMethod('getInstance');
			if ($getInstance->isPublic() && $getInstance->isStatic()){
				$cache[$this->classname] = $getInstance->invoke(null);
				return $cache[$this->classname];
			}
		}
		return false;
	}
}