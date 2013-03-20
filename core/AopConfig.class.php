<?php
/**
 * Aop配置
 * @author wclssdn <ssdn@vip.qq.com>
 *
 */
class AopConfig {

	private $beforeFilter = array();

	private $afterFilter = array();

	/**
	 * 构造方法
	 * @param array instanceof Filter $beforeFilters
	 * @param array instanceof Filter $afterFilters
	 */
	public function __construct(array $beforeFilters, array $afterFilters) {
		foreach ($beforeFilters as $filter){
			if ($filter instanceof Filter){
				$this->beforeFilter[] = $filter;
			}
		}
		foreach ($afterFilters as $filter){
			if ($filter instanceof Filter){
				$this->afterFilter[] = $filter;
			}
		}
	}

	public function getBeforeFilter() {
		return $this->beforeFilter;
	}

	public function getAfterFilter() {
		return $this->afterFilter;
	}
}