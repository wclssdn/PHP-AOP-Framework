<?php
//文件物理绝对路径
define('PATH_ROOT', dirname(__FILE__) . '/');
define('PATH_CORE', PATH_ROOT . 'core/');
define('PATH_CONTROLLER', PATH_ROOT . 'controller/');
define('PATH_VIEW', PATH_ROOT . 'view/');
define('PATH_MOVEL', PATH_ROOT . 'model/');
define('PATH_CONFIG', PATH_ROOT . 'config/');
define('PATH_FILTER', PATH_ROOT . 'filter/');
define('PATH_TOOL', PATH_ROOT . 'tool/');

//网址
define('URL_ROOT', 'http://' . $_SERVER['HTTP_HOST'] . '/');

//自动加载
require_once PATH_CORE . 'Autoload.class.php';
Autoload::setRootPath(PATH_ROOT);
Autoload::addSubPath('lib');
Autoload::addSubPath('filter');
Autoload::addSubPath('tool');
Autoload::start();

Session::setHandle(new MemcacheSession());
Session::start();
