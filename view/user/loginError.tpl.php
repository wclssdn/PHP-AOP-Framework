<?php $this->widget('header', array('title' => '登录失败 - 子曾约过', 'css' => array('main')));?>
<div id="x" class="hide">登录失败了... <a href="/">回首页</a>吧...
</div>
<script type="text/javascript">
$(function(){
	abscenter($("#x")).show();
});
</script>
<?php $this->widget('footer', array('js' => array('main')));?>