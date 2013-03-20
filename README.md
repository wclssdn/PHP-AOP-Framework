PHP-AOP-Framework
=================

A mvc framework support aop for php.

<pre>
#Rewrite for nginx
RewriteEngine On
RewriteRule !^/data1/www/htdocs/983/mydate/2/.*?(index.php(.*)|.*?\.(css|js|jpg|jpeg|gif|png|swf))$ /index.php/%{QUERY_STRING} [L]

#Rewrite for sae
- rewrite: if( path !~ "(index.php(.*)|.*?\.(css|js|jpg|jpeg|gif|png|swf))$" ) goto "/index.php/%{QUERY_STRING}"
</pre>
