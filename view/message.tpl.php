<?php $this->widget('header', array('title' => '子曾约过'));?>
<div class="container-fluid">
	<div class="row-fluid">
		<div class="span4 offset4 alert alert-block<?php if (isset($success)):?> alert-success<?php else:?> alert-error<?php endif;?>">
			<h4 style="text-align: center"><?php echo htmlspecialchars($message);?></h4>
		</div>
	</div>
</div>
<?php $this->widget('footer');?>