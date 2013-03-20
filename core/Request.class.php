<?php
/**
 * 请求
 * @author wclssdn <ssdn@vip.qq.com>
 *
 */
class Request {

	/**
	 * 请求方式 get/post/put/delete/option 等等
	 * @var string
	 */
	private $method;

	private $param;

	private function __construct() {
		$this->method = strtolower($_SERVER['REQUEST_METHOD']);
		$this->param = $this->method === 'get' ? $_GET : $_POST;
	}

	private function __clone() {
	}

	/**
	 * 获取Request实例
	 * @return Request
	 */
	public static function getInstance() {
		static $instance = null;
		if ($instance === null){
			$instance = new self();
		}
		return $instance;
	}

	/**
	 * 设置是否对请求方法敏感
	 * @param boolean $boolean
	 */
	public function setMethodSensitive($boolean = true){
		if ($boolean){
			$this->param = $this->method === 'get' ? $_GET : $_POST;
		}else{
			$this->param = array_merge($_GET, $_POST);
		}
	}

	public function setParam($name, $value) {
		$this->param[$name] = $value;
	}

	/**
	 * 获取请求参数
	 * @param string $name
	 * @return string
	 */
	public function getParam($name, $default = null) {
		return isset($this->param[$name]) ? $this->param[$name] : $default;
	}

	/**
	 * 获取URL参数
	 * @param string $name
	 * @param string $default
	 * @return string
	 */
	public function getUrlParam($name, $default = null){
		return isset($_GET[$name]) ? $_GET[$name] : $default;
	}

	public function getServer($name, $default = null) {
		return isset($_SERVER[$name]) ? $_SERVER[$name] : $default;
	}

	public function isGet() {
		return $this->method === 'get';
	}

	public function isPost() {
		return $this->method === 'post';
	}

	public function isPut() {
		return $this->method === 'put';
	}

	public function isDelete() {
		return $this->method === 'delete';
	}

	public function isAjax(){
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
	}
}