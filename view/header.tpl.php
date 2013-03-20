<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title><?php isset($title) && print(htmlspecialchars($title));?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="子曾约过">
<meta name="author" content="子曾约过">
<link href="/static/css/bootstrap.min.css" rel="stylesheet">
<script src="/static/js/jquery-1.8.2.min.js"></script>
<script src="/static/js/bootstrap.min.js"></script>
<script src="/static/js/bootstrap-popover.js"></script>
<?php if (isset($css) && is_array($css)):?>
<?php foreach ($css as $c):?>
<link href="/static/css/<?php echo $c;?>.css?<?php echo VERSION_CSS;?>" rel="stylesheet">
<?php endforeach;?>
<?php endif;?>
<?php if (isset($js) && is_array($js)):?>
<?php foreach ($js as $j):?>
<script src="/static/js/<?php echo $j;?>.js?<?php echo VERSION_JS;?>"></script>
<?php endforeach;?>
<?php endif;?>
<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
</head>
<body>
<div class="container-fluid" style="padding:0;">
