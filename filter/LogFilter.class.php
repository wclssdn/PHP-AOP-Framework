<?php
class LogFilter {

	private $start;

	public function start() {
		$this->start = microtime(1);
	}

	public function end() {
		$request = Request::getInstance();
		if (!$request->isAjax()){
			echo '<!-- process cost:', sprintf('%.3f', microtime(1) - $this->start), 's. powered by aop framework -->', PHP_EOL;
		}
	}
	
	public function signCancel($args, $result){
		$uniqid = $args[0];
		$loginUserInfo = Ioc::callStatic('UserController', 'getLoginUserInfo');
		$logModel = Ioc::loadObject('LogModel');
		$logModel->log("sign cancel:uid:{$loginUserInfo['uid']} from:{$loginUserInfo['from']} nick:{$loginUserInfo['nickname']} be canceled uniqid:{$uniqid} result:" . var_export($result, true), KEY_LOG_SIGN_CANCEL . $loginUserInfo['uid']);
	}
	
	public function login($args, $result){
		$userInfo = $args[0];
		$from = $args[1];
		$logModel = Ioc::loadObject('LogModel');
		$logModel->log('', KEY_LOG_LOGIN . $from . '_' . $userInfo['uid']);
	}
}