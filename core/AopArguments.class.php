<?php
/**
 * Aop参数解析
 * @author Wclssdn
 *
 */
class AopArguments {

	private $keywords = array('___OBJECT___', '___CLASS___', '___METHOD___', '___ARGS___', '___RESULT___', '___LAST_RESULT___');

	private $args;

	public function __construct(array $args) {
		$this->args = $args;
	}

	public function getArgs($object, $class, $method, $args, $result, $lastResult) {
		$uniqid = uniqid();
		$tmp = array();
		foreach ($this->args as &$arg){
			$index = null;
			if (preg_match('#___ARGS\[(\d+)\]___#', $arg, $index)){
				if (isset($args[$index[1]])){
					$arg = $args[$index[1]];
				}else{
					$arg = null;
				}
			}
		}
		unset($arg);
		foreach ($this->args as &$arg){
			$arg = str_replace($this->keywords, array('$object', '$class', '$method', '$args', '$result', '$lastResult'), $arg);
			$arg = eval("return {$arg};");
		}
		unset($arg);
		return $this->args;
	}
}