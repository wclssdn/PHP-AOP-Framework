<?php
/**
 * Memcache session handle
 * @author wclssdn<ssdn@vip.qq.com>
 *
 */
class MemcacheSession implements SessionHandleable {

	private $mc;

	/**
	 * 会话过期时间(秒)
	 * @var number
	 */
	protected $maxLifeTime = 1200;
	/*
	 * (non-PHPdoc) @see SessionHandleable::open()
	 */
	public function open($savePath, $sessionName) {
		return $this->mc = memcache_init();
	}
	/*
	 * (non-PHPdoc) @see SessionHandleable::close()
	 */
	public function close() {
		memcache_close($this->mc);
	}
	/*
	 * (non-PHPdoc) @see SessionHandleable::read()
	 */
	public function read($id) {
		return memcache_get($this->mc, $id);
	}
	/*
	 * (non-PHPdoc) @see SessionHandleable::write()
	 */
	public function write($id, $data) {
		return memcache_set($this->mc, $id, $data, MEMCACHE_COMPRESSED, $this->maxLifeTime);
	}
	/*
	 * (non-PHPdoc) @see SessionHandleable::destroy()
	 */
	public function destroy($id) {
		return memcache_delete($this->$mc, $id);
	}
	/*
	 * (non-PHPdoc) @see SessionHandleable::gc()
	 */
	public function gc($maxLifeTime) {
		return true;
	}
	/*
	 * (non-PHPdoc) @see SessionHandleable::setMaxLifeTime()
	 */
	public function setMaxLifeTime($maxLifeTime) {
		$this->maxLifeTime = max(0, min($maxLifeTime, 2592000));
		return true;
	}
}