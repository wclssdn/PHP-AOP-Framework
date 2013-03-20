<?php $this->widget('header', array('title' => '子曾约过', 'css' => array('main')));?>
<div class="row-fluid" id="list">
<?php foreach ($friends as $f):?>
	<div class="a">
		<img src="<?php echo $f['avatar'];?>" style="width:180px;height:180px" />
		<div class="b heart hide">
		<?php echo String::len($f['nick']) > 11 ? '<i title="' . $f['nick'] . '">' . String::cut($f['nick'], 0, 11) . '</i>' : $f['nick'];?><br />
		<?php if (isset($f['name'])):?>帐号: <?php echo $f['name'];?><br /><?php endif;?>
		<?php if ($f['location']):?>所在地: <?php echo $f['location'];?><br /><?php endif;?>
		<?php echo htmlspecialchars(String::cut($f['description'], 0, 30, '...'));?><br />
		<div class="c">
			<?php if ($signs[$f['uniqid']]):?>
			<a class="btn cancel" data-uniqid="<?php echo $f['uniqid'];?>" data-nick="<?php echo $f['nick'];?>"<?php if (isset($f['name'])):?> data-name="<?php echo $f['name'];?>"<?php endif;?> data-token="<?php echo Encryption::token($f['uniqid'], $f['nick'], isset($f['name']) ? $f['name'] : '');?>">✘</a>
			<?php else:?>
			<a class="btn btn-primary sign" data-uniqid="<?php echo $f['uniqid'];?>" data-nick="<?php echo $f['nick'];?>"<?php if (isset($f['name'])):?> data-name="<?php echo $f['name'];?>"<?php endif;?> data-token="<?php echo Encryption::token($f['uniqid'], $f['nick'], isset($f['name']) ? $f['name'] : '');?>">✔</a>
			<?php endif;?>
		</div>
		</div>
	</div>
<?php endforeach;?>
</div>
<div id="morefriend" class="c hide"><a href="#" id="nextpagebtn" class="btn">显示更多好友</a></div>
<div id="home" class="d r s"><i class="icon-home"></i></div>
<div id="male" class="d r s"><p><a href="/m">男</a></p></div>
<div id="female" class="d r s"><p><a href="/f">女</a></p></div>
<div id="messagebox" class="d r s"><p id="unread"><?php if ($unread):?><?php echo $unread;?><?php else:?><i class="icon-envelope"></i><?php endif;?></p></div>
<div id="helper" class="d r s b1"><i class="icon-question-sign" style="margin:0"></i></div>
<div id="bg" class="bg hide"></div>
<div id="message" class="s pop hide">
	<div class="h"><i class="icon-envelope"></i> 消息列表<span class="close">✖</span></div>
	<ul id="messagelist" class="hide">
	</ul>
	<div id="messagenone" class="none hide">空空如也</div>
	<div id="messageloading" class="hide"></div>
	<div id="messagepage" class="page">
		<a id="prev" class="btn hide">上一页</a>
		<a id="next" class="btn hide">下一页</a>
	</div>
</div>
<div id="alert" class="s pop hide">
	<div class="h"><i class="icon-info-sign"></i> 提示<span class="close">✖</span></div>
	<p id="alert_content"></p>
	<div class="c">
		<a class="btn btn-primary" id="alert_btn_ok">确定</a> <a class="btn close" style="float:none" id="alert_btn_cancel">取消</a>
	</div>
</div>
<div id="newmessage" class="r s hide"><i class="icon-envelope icon-white" style="margin:-1px 0 0 1px"></i></div>
<div id="invite_box" class="s pop hide">
	<div class="h"><i class="icon-info-sign"></i> 提示<span class="close">✖</span></div>
	<p>
		你标记的人还没有使用过本应用，对于你的标记Ta一无所知。是否邀请Ta使用本应用？<br>
		（邀请微博使用应用官方帐号发送，不会提及您，请放心点确定！）
	</p>
	<div class="c">
		<a class="btn btn-primary" id="invite_btn">确定</a> <a class="btn close" style="float:none">取消</a>
	</div>
</div>
<div id="helperbox" class="s pop hide">
	<div class="h"><i class="icon-question-sign"></i> 帮助<span id="helper_close" class="close">✖</span></div>
	<p>
		本应用致力于私密交友，你的<b>所有操作</b>都将是<b>保密</b>的。<br>
		首先在你喜欢的人的头像上点标记按钮（<a class="btn btn-primary btn-mini">✔</a>）。<br>
		然后看系统提示对方是否在使用本应用，如果没有就邀请Ta。<br>
		Ta收到邀请后，如果来使用本应用，并标记了你。<br>
		你们双方会各自收到一条应用内消息提醒。<br>
		这个时候你们就私下联系吧。。。<br>
		但是要注意安全哦～～～<br>
		喜欢Ta就大胆的<a class="btn btn-primary btn-mini">✔</a>Ta吧！！！又没人会知道！！！
	</p>
</div>
<script type="text/javascript">
var newmessage = <?php echo $unread ? 'true' : 'false';?>;
<?php if (isset($newuser) && $newuser):?>
$(function(){
	setTimeout(function(){
		$("#bg").show();
		newalert("新用户请先花一分钟看下说明，以帮助你使用本应用。\n确定后显示帮助信息", true, false, function(){
			$("#helper").click();
			$("#helper_close").click(function(){
				newalert("你也可以点击右下角的HOME按钮来查看帮助信息(按钮右边的小问号)\n同时，菜单中也可以查看不同性别的好友以及应用内消息。", true, false, function(){
					$("#bg").show();
					setTimeout(function(){
						$("#home").click();
					}, 1000);
					setTimeout(function(){
						newalert("不再显示新手引导？", function(){
							$.post('/iamnotnew', function(data){}, 'json');
						}, true);
						$("#bg").hide();
					}, 3000);
					$("#helper_close").unbind("click").click(function(){
						$(".pop").hide();
						$("#bg").hide();
					});
				});
			});
		});
	}, 3000);
});
<?php endif;?>
</script>
<?php $this->widget('footer', array('showfooter' => true, 'js' => array('main')));?>