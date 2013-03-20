PHP-AOP-Framework
=================

A mvc framework support aop for php.

<pre>
#Rewrite for nginx
RewriteEngine On
RewriteRule !^/htdocs/.*?(index.php(.*)|.*?\.(css|js|jpg|jpeg|gif|png|swf))$ /index.php/%{QUERY_STRING} [L]

#Rewrite for sae
- rewrite: if( path !~ "(index.php(.*)|.*?\.(css|js|jpg|jpeg|gif|png|swf))$" ) goto "/index.php/%{QUERY_STRING}"
</pre>

Use
- load a object
  $object = Ioc::loadObject('classname', array('arg1', 'arg2'));
- call method
  $object->method('arg3', 'arg4');
- call static method
  $result = Ioc::callStatic('classname', 'method');
- add config to config/aop.conf.php
<pre>
    'classname::method' => new AopConfig(
  		array(
  			new Filter('LogFilter', array(), 'start', array(), true),
  		), 
  		array(
  			new Filter('LogFilter', array(), 'end', array('___METHOD___'), true)
  		)
	), 
</pre>

Process
  - ioc::loadOjbect('classname', array('arg1', 'arg2'));
  - $classobject = new classname('arg1', 'arg2');
  - $object = new ObjectProxy($classObject);
  - $object->__call(array($object, 'method', array('arg3', 'arg4')));
  - $aop = new Aop(array($object, 'method', array('arg3', 'arg4')));
  - $aop->execute();
  - $aopConfig = include 'config/aop.conf.php';
  - preg_match('#^classname$#i', 'classname') && preg_match('#^method$#i', 'method');
  - LogFilter::start();
  - $reslt = call_user_func_array($object, 'method', array('arg3', 'arg4'));
  - LogFilter::end('method');
  - return $result;
  
