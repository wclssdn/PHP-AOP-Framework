<?php
/**
 * AOP
 * @author wclssdn <ssdn@vip.qq.com>
 *
 */
class Aop {

	private $callable;

	private $args;

	/**
	 * aop配置
	 * @var array instanceof AopConfig
	 */
	private $config;

	public function __construct(array $callable, array $args = array()) {
		$this->callable = $callable;
		$this->args = $args;
		$this->config = $this->loadConfig();
	}

	/**
	 * 执行aop调用
	 * @return mixed
	 */
	public function execute() {
		$keys = array_keys($this->config);
		$object = is_object($this->callable[0]) ? $this->callable[0] : null;
		$classname = $object === null ? $this->callable[0] : get_class($object);
		$method = $this->callable[1];
		$aopConfig = array();
		foreach ($keys as $key){
			list($classGrep, $methodGrep) = explode('::', $key);
			if (preg_match("#^{$classGrep}$#i", $classname) && preg_match("#^{$methodGrep}$#i", $method)){ // 同时匹配类和方法
				$aopConfig[] = $this->config[$key];
			}
		}
		$result = null;
		$quit = false;
		foreach ($aopConfig as $config){
			$beforeFilters = $config->getBeforeFilter();
			foreach ($beforeFilters as $bf){
				$bf->replaceParameters($object, $classname, $method, $this->args, null, $result);
				if ($bf->must()){ // 必须执行的过滤器
					$tmp = $bf->execute();
				} elseif (!$quit){ // 没有提前退出,所有的都执行
					$tmp = $bf->execute();
				}
				if (!$quit && $bf->quit()){
					$quit = true;
					$result = $result ? $result : $tmp;
				}
			}
		}
		if (!$quit){ // 没提前退出
			try {
				$result = call_user_func_array($this->callable, $this->args);
			}catch (QuitException $qe){
				//do nothing...
			}
		}
		foreach ($aopConfig as $config){
			$afterFilters = $config->getAfterFilter();
			foreach ($afterFilters as $af){
				$af->replaceParameters($object, $classname, $method, $this->args, $result, $result);
				if ($af->must()){ // 必须执行的过滤器
					$tmp = $af->execute();
				} elseif (!$quit){ // 没有提前退出,所有的都执行
					$tmp = $af->execute();
				} else{
					continue;
				}
			}
		}
		return $result;
	}

	/**
	 * 加载AOP配置
	 * @return array
	 */
	private function loadConfig() {
		try{
			$config = Config::loadFile('aop');
		} catch (Exception $e){
			return array();
		}
		if (!is_array($config)){
			return array();
		}
		foreach ($config as $k => $v){
			if ($v instanceof AopConfig){
			} else{
				unset($config[$k]);
			}
		}
		return $config;
	}
}