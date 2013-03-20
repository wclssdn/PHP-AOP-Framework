<?php
/**
 * 路由规则
 *
 * 键为正则匹配请求对象.匹配上后, 值中可以使用$1,$2,$3...$9引用键中匹配项(param子键不支持此功能)
 * 值中param子键可指定键中匹配到的项为请求参数
 */
return array(
	'user/login/(sina|qq)' => array(
		'controller' => 'UserController',
		'action' => 'loginAction',
		'param' => array('from')),
	'user/login/error' => array(
		'controller' => 'UserController',
		'action' => 'loginerrorAction',
	),
);