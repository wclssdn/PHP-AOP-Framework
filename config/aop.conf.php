<?php
/**
 * AOP配置
 * 
 * 格式:
 * 'regex' => AopConfig(array(beforeFilters), array(afterFilters));
 * Filter 参数: 类名,类初始化参数,方法名,调用方法参数,是否单例,是否必须执行,退出条件
 * 参数可使用AopArguments对象, 此对象可替换指定字符串为特定值.
 * search: array('___OBJECT___', '___CLASS___', '___METHOD___', '___ARGS[\d+]___', '___ARGS___', '___RESULT___', '___LAST_RESULT___') 
 * replace: 要执行的对象,要执行的类名,要执行的类方法,要执行方法的参数,方法执行后的结果,上一个过滤器执行的结果
 */
return array(
	'.*?Controller::.*?Action' => new AopConfig(
		array(
			new Filter('LogFilter', array(), 'start', array(), true),
		), 
		array(
			new Filter('LogFilter', array(), 'end', array(), true)
		)
	), 
);