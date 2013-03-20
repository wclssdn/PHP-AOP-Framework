<?php
/**
 * Mysql数据存储
 * @author wclssdn <ssdn@vip.qq.com>
 *
 */
class MysqlDao implements DbDao {

	/**
	 * DB实例缓存数组
	 * @var array
	 */
	private static $dbCache = array();
 
	/**
	 * 读写模式
	 * @var string
	 */
	private $mode = 'r';

	/**
	 * 数据库配置
	 * @var array
	 */
	private $config;

	/**
	 * 表名
	 * @var string
	 */
	private $table;

	/**
	 * 数据库实例, 可能为读写两个实例
	 * @var array resource
	 */
	private $db = array();

	/**
	 * 最后一次执行的SQL语句
	 * @var string
	 */
	private $sql;

	/**
	 * 执行过的SQL
	 * @var array
	 */
	private $sqls = array();

	public function __construct(array $config) {
		$this->config = $config;
	}

	/**
	 * 设置表名
	 * @param string $table 表名 如果分表,有分隔符,用此变量传递 如table_
	 * @return null
	 */
	public function setTable($table) {
		$this->table = $table;
	}

	/**
	 * 连接数据库
	 * @throws Exception
	 */
	public function connect() {
		$cacheKey = md5(serialize($this->config[$this->mode]));
		if (!isset(self::$dbCache[$cacheKey])){
			$this->db[$this->mode] = mysql_connect($this->config[$this->mode]['host'], $this->config[$this->mode]['user'], $this->config[$this->mode]['password'], $this->config[$this->mode]['dbname']);
			if ($this->db[$this->mode] === false){
				throw new Exception('Db connect failed!');
			}
			if (mysql_select_db($this->config[$this->mode]['dbname'], $this->db[$this->mode]) === false){
				throw new Exception(mysql_error($this->db[$this->mode]));
			}
			$this->query("set names '{$this->config[$this->mode]['charset']}'");
			self::$dbCache[$cacheKey] = $this->db[$this->mode];
		}else{
			$this->db[$this->mode] = self::$dbCache[$cacheKey];
		}
	}

	/*
	 * (non-PHPdoc) @see DbDao::query()
	 */
	public function query($sql) {
		if (preg_match('#\s*select(?![\s.]*?last_insert_id\(\))#i', $sql)){
			$this->switchMode('r');
		}
		$this->sql = $sql;
		$this->sqls[] = $sql;
		isset($_GET['debugsql']) && Debug::dump($this->sql);
		$result = mysql_query($sql, $this->db[$this->mode]);
		if ($result === false && mysql_error($this->db[$this->mode])){
			throw new Exception(mysql_error($this->db[$this->mode]));
		}
		return $result;
	}
	/*
	 * (non-PHPdoc) @see DbDao::fetch()
	 */
	public function fetch($result, $type = MYSQL_ASSOC) {
		return mysql_fetch_array($result, $type);
	}
	/*
	 * (non-PHPdoc) @see DbDao::insert()
	 */
	public function insert(array $fields, array $values) {
		$this->switchMode('w');
		$fields = $this->parseField($fields);
		$values = $this->parseValues($values);
		$sql = "insert into {$this->table}({$fields}) values({$values})";
		return $this->query($sql);
	}
	/*
	 * (non-PHPdoc) @see DbDao::update()
	 *  @param array|string $condition  数组方式,默认使用and方式. 使用字符串方式不要带上where
	 */
	public function update(array $fields, array $values, $condition) {
		$this->switchMode('w');
		$set = array();
		foreach ($fields as $f){
			$set[] = $this->parseField($f) . '=' . $this->parseValues(array_shift($values));
		}
		$condition = $this->parseCondition($condition);
		$condition = $condition ? " where {$condition}" : '';
		$set = 'set ' . implode(',', $set);
		$sql = "update {$this->table} {$set} {$condition}";
		return $this->query($sql);
	}
	/*
	 * (non-PHPdoc) @see DbDao::delete()
	 */
	public function delete($condition) {
		$this->switchMode('w');
		$condition = $this->parseCondition($condition);
		$condition = $condition ? " where {$condition}" : '';
		$sql = "delete from {$this->table} {$condition}";
		return $this->query($sql);
	}
	/*
	 * (non-PHPdoc) @see DbDao::get()
	 */
	public function get($fields, $condition, $order = '', $limit = 1) {
		$this->switchMode('r');
		$fields = $this->parseField($fields);
		$condition = $this->parseCondition($condition);
		$condition = $condition ? " where {$condition}" : '';
		$order = $order ? " order by {$order}" : '';
		$returnArray = $limit === 1 || $limit === array(1) ? false : true;
		$limit = $this->parseLimit($limit);
		$sql = "select {$fields} from {$this->table}{$condition}{$order}{$limit}";
		$result = $this->query($sql);
		if ($returnArray){
			$return = array();
			while(($t = mysql_fetch_assoc($result)) !== false){
				$return[] = $t;
			}
		} else{
			$return = mysql_fetch_assoc($result);
		}
		return $return;
	}

	/**
	 * 计算总行数
	 * @param array|string $fields
	 * @param array|string $condition
	 * @return boolean|number
	 */
	public function count($fields = '*', $condition = array()){
		$this->switchMode('r');
		$condition = $this->parseCondition($condition);
		$condition = $condition ? " where {$condition}" : '';
		$fields = $this->parseField($fields);
		$sql = "select count({$fields}) as cnt from {$this->table}{$condition}";
		$result = $this->query($sql);
		if ($result === false){
			return false;
		}
		$result = mysql_fetch_assoc($result);
		return $result['cnt'];
	}

	/**
	 * 自增减运算
	 * @param array $fields
	 * @param array $increment
	 * @param array|string $condition
	 * @return resource
	 */
	public function increment(array $fields, array $increment, $condition){
		$this->switchMode('w');
		$condition = $this->parseCondition($condition);
		$condition = $condition ? " where {$condition}" : '';
		$set = array();
		foreach ($fields as $field){
			$inc = !empty($increment) ? array_shift($increment) : 0;
			$set[] = $this->parseField($field) . '=' . $this->parseField($field) . ($inc > 0 ? "+{$inc}" : $inc); 
		}
		$set = ' set ' . implode(',', $set);
		$sql = "update {$this->table}{$set}{$condition}";
		return $this->query($sql);
	}

	/**
	 * 转义字符串
	 * @param string $value
	 * @return string
	 */
	public function escape($value) {
		return mysql_real_escape_string($value, $this->db[$this->mode]);
	}

	/**
	 * 切换数据连接状态
	 * @param string $mode 可选 w/r (读/写)
	 * @throws Exception
	 */
	public function switchMode($mode) {
		if ($mode !== 'w' && $mode !== 'r'){
			throw new Exception('Db mode wrong!');
		}
		$this->mode = $mode;
		if (!isset($this->db[$this->mode])){
			$this->connect();
		}
	}

	/**
	 * Or条件
	 * @param array|string $condition1
	 * @param array|string $condition2
	 * @param array|string $conditionN
	 * @return string
	 */
	public function conditionOr($condition1, $condition2){
		$tmp = array();
		foreach (func_get_args() as $condition){
			$tmp[] = '(' . $this->parseCondition($condition) . ')';
		}
		return '(' . implode(' or ', $tmp) . ')';
	}

	/**
	 * 获取查询SQL记录
	 * @param boolean $all
	 * @return string|array
	 */
	public function getSql($all = false){
		return $all === false ? $this->sql : $this->sqls;
	}

	/**
	 * 解析列名
	 * @param string|array $fields
	 * @return string
	 */
	private function parseField($fields) {
		if (is_string($fields) && strpos($fields, ',')){
			$fields = explode(',', $fields);
		}
		if (is_array($fields)){
			foreach ($fields as &$field){
				if (is_scalar($field)){
					$field = $this->parseField($field);
				}
			}
			return implode(',', $fields);
		} else{
			if ($fields === '*'){
				return '*';
			}
			if (strpos($fields, '(')){
				return $fields;
			}
			return "`{$fields}`";
		}
	}

	/**
	 * 解析值
	 * @param string|array $values
	 * @return string
	 */
	private function parseValues($values) {
		if (is_array($values)){
			return '"' . implode('","', array_map(array($this, 'escape'), $values)) . '"';
		} else{
			if (preg_match('#^\d+$#', $values)){
				return $values;
			} else{
				return '"' . $this->escape($values) . '"';
			}
		}
	}

	/**
	 * 解析条件语句
	 * @param string|array $condition
	 * @return string
	 */
	private function parseCondition($condition) {
		if (is_array($condition)){
			$tmp = array();
			foreach ($condition as $key => $val){
				if (is_array($val)){
					$tmp[] = $this->parseField($key) . 'in(' . $this->parseValues($val) . ')';
				}elseif (is_numeric($key)){ //用户传递的字符串条件,用户使用conditionOr生成的字符串条件.都标记为可信
					$tmp[] = $val;
				}else{
					$tmp[] = $this->parseField($key) . '=' . $this->parseValues($val);
				}
			}
			return implode(' and ', $tmp);
		}
		return $condition;
	}

	/**
	 * 解析limit语句
	 * @param string|array $limit
	 * @return string
	 */
	private function parseLimit($limit) {
		if (is_array($limit)){
			if (isset($limit[1])){
				return ' limit ' . intval($limit[0]) . ',' . intval($limit[1]);
			}
			return ' limit ' . intval($limit[0]);
		}
		if (strpos(',', $limit)){
			return $this->parseLimit(implode(',', $limit));
		}
		return ' limit ' . intval($limit);
	}

	public function __destruct() {
		foreach ($this->db as $db){
			is_resource($db) && mysql_close($db);
		}
	}
}