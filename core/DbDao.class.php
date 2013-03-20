<?php
/**
 * 关系型数据库数据操作层接口
 * @author Wclssdn
 *
 */
interface DbDao extends Dao{

	/**
	 * 执行Sql查询
	 * @param string $sql
	 * @return resource|boolean
	 */
	public function query($sql);

	/**
	 * 读取结果集
	 * @param resource $result
	 * @return array|boolean
	 */
	public function fetch($result);

	/**
	 * 插入数据
	 * @param array $fields
	 * @param array $values
	 * @return boolean|number
	 */
	public function insert(array $fields, array $values);

	/**
	 * 修改数据
	 * @param array $fields
	 * @param array $values
	 * @param array|string $condition
	 * @return boolean
	 */
	public function update(array $fields, array $values, $condition);

	/**
	 * 删除数据
	 * @param string|array $condition
	 * @return boolean
	 */
	public function delete($condition);

	/**
	 * 获取数据
	 * @param array|string $fields
	 * @param array|string $condition
	 * @param string $order
	 * @param string|array $limit
	 */
	public function get($fields, $condition, $order = '', $limit = 1);
}