<?php
/**
 * sae KVDB操作类
 * @author wclssdn<ssdn@vip.qq.com>
 *
 */
class KvdbDao implements NoSqlDao {

	private $kv;

	public function __construct() {
		$this->kv = new SaeKV();
		$ret = $this->kv->init();
		if ($ret !== true){
			throw new Exception('Kvdb init failed');
		}
		$ret = $this->kv->set_options(array('encodekey' => 0));
		if ($ret !== true){
			throw new Exception('Kvdb set options failed');
		}
	}
	/*
	 * (non-PHPdoc) @see NoSqlDao::add()
	 */
	public function add($key, $val, $expire = 0) {
		is_scalar($val) || $val = serialize($val);
		return $this->kv->add($key, $val);
	}
	/*
	 * (non-PHPdoc) @see NoSqlDao::update()
	 */
	public function update($key, $val) {
		is_scalar($val) || $val = serialize($val);
		return $this->kv->set($key, $val);
	}
	/*
	 * (non-PHPdoc) @see NoSqlDao::replace()
	 */
	public function replace($key, $val) {
		is_scalar($val) || $val = serialize($val);
		return $this->kv->replace($key, $val);
	}
	/*
	 * (non-PHPdoc) @see NoSqlDao::delete()
	 */
	public function delete($key) {
		return $this->kv->delete($key);
	}
	/*
	 * (non-PHPdoc) @see NoSqlDao::get()
	 */
	public function get($key) {
		$result = $this->kv->get($key);
		$tmp = @unserialize($result);
		return $tmp === false ? $result : $tmp;
	}
	/*
	 * (non-PHPdoc) @see NoSqlDao::mget()
	 */
	public function getMulti(array $keys) {
		$result = $this->kv->mget($keys);
		if (is_array($result)){
			foreach ($result as &$r){
				$tmp = @unserialize($r);
				$tmp === false || $r = $tmp;
			}
		}
		return $result;
	}
	
	/* (non-PHPdoc)
	 * @see NoSqlDao::getPrefix()
	 */
	public function getPrefix($prefix, $startkey, $count){
		$result =  $this->kv->pkrget($prefix, $count, $startkey);
		if (is_array($result)){
			foreach ($result as &$r){
				$tmp = @unserialize($r);
				$tmp === false || $r = $tmp;
			}
		}
		return $result;
	}
}