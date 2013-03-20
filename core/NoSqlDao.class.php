<?php
/**
 * 非关系型数据库数据层接口
 * @author wclssdn<ssdn@vip.qq.com>
 *
 */
interface NoSqlDao extends Dao {

	/**
	 * 插入数据
	 * @param string $key
	 * @param mixed $val
	 * @param number $expire
	 * @return boolean
	 */
	public function add($key, $val, $expire = 0);

	/**
	 * 更新数据
	 * @param string $key
	 * @param mixed $val
	 * @return boolean
	 */
	public function update($key, $val);

	/**
	 * 替换数据
	 * @param string $key
	 * @param mixed $val
	 * @return boolean
	 */
	public function replace($key, $val);

	/**
	 * 删除数据
	 * @param string $key
	 * @return boolean
	 */
	public function delete($key);

	/**
	 * 读取数据
	 * @param string $key
	 * @return mixed
	 */
	public function get($key);

	/**
	 * 批量读取
	 * @param array $keys
	 * @return array boolean
	 */
	public function getMulti(array $keys);

	/**
	 * 前缀匹配批量读取
	 * @param string $prefix
	 * @param number $start
	 * @param number $count
	 */
	public function getPrefix($prefix, $start, $count);
}