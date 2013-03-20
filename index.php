<?php
require './init.php';
ini_set('display_errors', 0);
$response = Response::getInstance(); //开启输出缓存
$request = Request::getInstance();
$dispatcher = Dispatcher::getInstance();
$controller = $dispatcher->getController();
$action = $dispatcher->getAction();
Config::loadFile('define');
try {
	$controller = Ioc::loadObject($controller);
	$controller->$action();
}catch (QuitException $qe){
	if ($qe->getCode() !== 0){
		$logModel = Ioc::loadObject('LogModel');
		$logModel->log('QuitException:' . $qe->getMessage() . $qe->getTraceAsString(), KEY_LOG_EXCEPTION_QUIT);
	}
}catch (Exception $e){
	$logModel = Ioc::loadObject('LogModel');
	$logModel->log($e->getMessage() . $e->getTraceAsString(), KEY_LOG_EXCEPTION);
}