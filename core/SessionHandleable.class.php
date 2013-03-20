<?php
/**
 * Session handle接口
 * @author wclssdn<ssdn@vip.qq.com>
 *
 */
interface SessionHandleable {

	/**
	 * 开启session
	 * @param string $savePath
	 * @param string $sessionName
	 * @return boolean
	 */
	public function open($savePath, $sessionName);

	/**
	 * 关闭session
	 * @return boolean
	 */
	public function close();

	/**
	 * 读取
	 * @param string $id
	 * @return mixed boolean
	 */
	public function read($id);

	/**
	 * 写入
	 * @param string $id
	 * @param mixed $data
	 * @return boolean
	 */
	public function write($id, $data);

	/**
	 * 销毁
	 * @param string $id
	 * @return boolean
	 */
	public function destroy($id);

	/**
	 * 垃圾回收
	 * @param number $maxLifeTime
	 * @return boolean
	 */
	public function gc($maxLifeTime);

	/**
	 * 设置会话过期时间
	 * @param number $maxLifeTime
	 * @return boolean
	 */
	public function setMaxLifeTime($maxLifeTime);
}