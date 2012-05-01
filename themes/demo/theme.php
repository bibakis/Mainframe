<!DOCTYPE html><html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content="Mainframe - The complete PHP framework" />
	<meta name="keywords" content="" />
	<meta name="language" content="en" />
	<title>Mainframe - The complete PHP framework</title>
	<?php $this->load->view('mainframe.js.php');?>
	<?php $this->load->css('/libs/css/mainframe.css/mainframe-grid.css', 'first');?>
	<?php $this->load->css('/libs/css/mainframe.css/mainframe-main.css');?>
	<?php $this->load->css('/themes/demo/css/styles.css', 'last');?>
	<?php $this->load->css('http://yui.yahooapis.com/3.5.0/build/cssgrids/grids-min.css', 'last');?>
	
	<?php $this->load->less('/themes/demo/less/styles.less');?>
	
	<?php $this->load->less('/libs/temp/twitter-bootstrap-v2.0.2-3-g6506ede/less/accordion.less', 'first');?>
	<?php $this->load->less('/libs/temp/twitter-bootstrap-v2.0.2-3-g6506ede/less/sprites.less', 'first');?>
	<?php $this->load->less('/libs/temp/twitter-bootstrap-v2.0.2-3-g6506ede/less/layouts.less');?>
	<?php $this->load->less('/libs/temp/twitter-bootstrap-v2.0.2-3-g6506ede/less/alerts.less', 'last');?>
	<?php $this->load->less('/libs/temp/twitter-bootstrap-v2.0.2-3-g6506ede/less/close.less', 'last');?>
	
	<?php $this->load->js('/libs/js/jquery-1.7.min.js','first');?>
	<?php echo $this->load->assets();?>
</head>
<body>
<div class="fixed12">
	<div class="grid_12">
		<?php echo $content; ?>
	</div>
</div>
</body></html>