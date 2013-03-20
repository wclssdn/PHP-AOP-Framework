<?php
/**
 * Memcache数据存储
 * @author wclssdn <ssdn@vip.qq.com>
 *
 */
class MemcacheNoSqlDataStore extends NoSqlDataStore {

	public function __construct($host, $port, $timeout = 1) {
		$this->memcache = new Memcache($host, $port, $timeout);
	}

	/**
	 * (non-PHPdoc)
	 * @see NoSqlDataStore::add()
	 */
	public function add($key, $val, $expire = 0) {
		return $this->memcache->add($key, $val, strlen($val) > 500 ? MEMCACHE_COMPRESSED : null, $expire);
	}

	/**
	 * (non-PHPdoc)
	 * @see NoSqlDataStore::del()
	 */
	public function del($key) {
		return $this->memcache->delete($key);
	}

	/**
	 * (non-PHPdoc)
	 * @see NoSqlDataStore::edit()
	 */
	public function edit($key, $val) {
		return $this->memcache->replace($key, $val, strlen($val) > 500 ? MEMCACHE_COMPRESSED : null);
	}

	/**
	 * (non-PHPdoc)
	 * @see NoSqlDataStore::get()
	 */
	public function get($key) {
		return $this->memcache->get($key);
	}

	/**
	 * (non-PHPdoc)
	 * @see NoSqlDataStore::gets()
	 */
	public function gets(array $keys) {
		$result = array();
		foreach ($keys as $key){
			$result[$key] = $this->memcache->get($key);
		}
		return $result;
	}
}