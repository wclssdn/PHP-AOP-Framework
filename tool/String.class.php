<?php
/**
 * 字符串操作类
 * @author wclssdn <ssdn@vip.qq.com>
 *
 */
class String {

	/**
	 * 截取字符串
	 * @param string $string
	 * @param number $offset
	 * @param number $length
	 * @param string $suffix
	 * @param string $encoding
	 * @return string
	 */
	public static function cut($string, $offset, $length, $suffix = '', $encoding = 'utf-8') {
		if (mb_strlen($string, $encoding) <= $length + $offset){
			return $string;
		}
		$substring = mb_substr($string, $offset, $length, $encoding);
		$suffix && $substring .= $suffix;
		return $substring;
	}

	/**
	 * 计算字符串字符长度
	 * @param string $string
	 * @param string $encoding
	 * @return number
	 */
	public static function len($string, $encoding = 'utf-8') {
		return mb_strlen($string, $encoding);
	}
	
	/**
	 * 是否以指定字符串结尾
	 * @param string $string
	 * @param string $needle
	 * @return boolean
	 */
	public static function endWith($string, $needle){
		return $needle === substr($string, -strlen($needle));
	}
}