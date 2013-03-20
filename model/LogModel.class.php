<?php

/**
 * 日志模型
 * @author wclssdn<ssdn@vip.qq.com>
 *
 */
class LogModel extends Model{

	/**
	 *
	 * @var KvdbDao
	 */
	private $dao;


	public function __construct(){
		$this->dao = Ioc::loadObject('KvdbDao');
	}

	public function log($message, $type = KEY_LOG){
		$key = $type . '_' . uniqid();
		return $this->dao->add($key, array('message' => $message, 'time' => date('Y-m-d H:i:s')));
	}
}