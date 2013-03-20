<?php
/**
 * 调度器
 * @author wclssdn <ssdn@vip.qq.com>
 *
 */
class Dispatcher {

	/**
	 * 控制器名称
	 * @var string
	 */
	private $controller;

	/**
	 * 操作名称
	 * @var string
	 */
	private $action;

	private function __construct() {
		$this->parseRequest();
	}

	/**
	 * 获取调度器实例
	 * @return Dispatcher
	 */
	public static function getInstance(){
		static $instance = null;
		if ($instance === null){
			$instance = new self();
		}
		return $instance;
	}
	/**
	 * 获取控制器
	 * @return string
	 */
	public function getController() {
		return $this->controller;
	}

	/**
	 * 获取名称
	 * @return string
	 */
	public function getAction() {
		return $this->action;
	}

	/**
	 * 分析请求信息
	 */
	private function parseRequest() {
		$request = Request::getInstance();
		if (($requesturi = $request->getServer('REQUEST_URI')) !== null){
			// 去掉问号后边的部分
			$requesturi = ($pos = strpos($requesturi, '?')) ? substr($requesturi, 0, $pos) : $requesturi;
			$config = Config::loadFile('router');
			if (!is_array($config)){
				throw new Exception('Router config file wrong!');
			}
			// 完全匹配优先
			foreach ($config as $k => $v){
				if ($requesturi === $k){
					$this->controller = $v['controller'];
					$this->action = $v['action'];
					if(isset($_GET['debug'])){
						Debug::dump($k);
					}
				}
			}
			
			// 正则匹配其次
			if (!$this->controller && !$this->action){
				foreach ($config as $k => $v){
					$matches = array();
					if (preg_match("#^(?:/index.php)?/{$k}/?$#i", $requesturi, $matches)){
						$this->controller = $v['controller'];
						array_shift($matches);
						$this->action = strpos($v['action'], '$') === false ? $v['action'] : str_replace(array('$1', '$2', '$3', '$4', '$5', '$6', '$7', '$8', '$9'), $matches, $v['action']);
						if(isset($_GET['debug'])){
							Debug::dump($k);
						}
						if (isset($v['param']) && is_array($v['param'])){
							$tmp = array_combine($v['param'], $matches);
							if(isset($_GET['debug'])){
								Debug::dump($tmp);
							}
							foreach ($tmp as $key => $val){
								$request->setParam($key, $val);
							}
							unset($tmp);
						}
					}
				}
			}
			//符合直接访问规则
			if (!$this->controller && !$this->action){
				$matches = array();
				if (preg_match('#^(?:/index.php)?/([a-z][a-z0-9]{0,30})/?([a-z][a-z0-9]{0,30})?/?$#i', $requesturi, $matches)){
					$this->controller = ucfirst($matches[1]) . 'Controller';
					$this->action = isset($matches[2]) ? $matches[2] . 'Action' : 'indexAction';
				}
			}
		} else{
			$this->controller = $request->getParam('c');
			$this->action = $request->getParam('a');
		}
		$this->controller = $this->controller ? $this->controller : 'IndexController';
		$this->action = $this->action ? $this->action : 'indexAction';
	}
}