
</div>
<?php if (isset($showfooter) && $showfooter):?>
<footer>
	<p><a href="http://weibo.com/178880888" target="_blank" title="开发者微博, 欢迎提出意见或建议, 谢谢!">@原来微博的昵称真的可以好长好长</a></p>
	<small title="例如chrome,firefox的最新版啦">推荐使用支持html5的高级浏览器</small>
</footer>
<?php endif;?>
<?php if (isset($js) && is_array($js)):?>
<?php foreach ($js as $j):?>
<script src="/static/js/<?php echo $j;?>.js?<?php echo VERSION_JS;?>"></script>
<?php endforeach;?>
<?php endif;?>
</body>
</html>