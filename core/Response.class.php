<?php
/**
 * 响应
 * @author wclssdn <ssdn@vip.qq.com>
 *
 */
class Response {

	/**
	 * 响应状态码
	 * @var number
	 */
	private $code = 200;

	/**
	 * 内容编码
	 * @var string
	 */
	private $charset = 'utf-8';

	/**
	 * mine类型
	 * @var string
	 */
	private $mine = 'text/html';

	/**
	 * 头信息数组
	 * @var array
	 */
	private $header = array();

	private function __construct() {
		ob_start();
	}

	private function __clone() {
	}

	/**
	 * 获取Response实例
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
	 * 设置头信息
	 * @param string $name
	 * @param string $value
	 */
	public function setHeader($name, $value) {
		$this->header[$name] = $value;
	}

	public function setCharset($charset) {
		$this->charset = $charset;
	}

	public function setMine($mine) {
		$this->mine = $mine;
	}

	/**
	 * 设置缓存时间
	 * @param number $seconds 缓存秒数     0:禁止缓存 
	 */
	public function setExpireTime($seconds) {
		if ($seconds === 0){ //禁止缓存
			$this->header['Pragma'] = 'no-cache'; //HTTP 1.0
			$this->header['Expires'] = 1;
			$this->header['Cache-Control'] = 'no-cache,no-store'; //HTTP 1.1; 防止浏览器缓存
		}elseif ($seconds > 0){ //设置缓存时间
			$this->header['Expires'] = time() + $seconds * 1000; //HTTP 1.0
			$this->header['Cache-Control'] = 'max-age=' . $seconds; //HTTP 1.1
		}
	}

	/**
	 * 跳转
	 * @param string $url
	 * @param number $timeout
	 */
	public function redirect($url, $timeout = 0) {
		header("Refresh: {$timeout}; url={$url}");
		ob_end_flush();
	}

	/**
	 * 发送响应到客户端
	 */
	public function send() {
		header("Content-type:{$this->mine}; charset={$this->charset}", true, $this->code);
		foreach ($this->header as $k => $v){
			header("{$k}: {$v}");
		}
		ob_end_flush();
	}
}