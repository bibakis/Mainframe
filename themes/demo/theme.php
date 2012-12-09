<!DOCTYPE html><html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content="Mainframe - The complete PHP framework" />
	<meta name="keywords" content="" />
	<meta name="language" content="en" />
	<title>Mainframe - The complete PHP framework</title>
	<?php view('mainframe.js.php');?>
	<?php css('/libs/css/mainframe.css/mainframe-grid.css', 'first');?>
	<?php css('/libs/css/mainframe.css/mainframe-main.css');?>
	<?php css('/themes/demo/css/styles.css', 'last');?>
	<?php css('http://yui.yahooapis.com/3.5.0/build/cssgrids/grids-min.css', 'last');?>
	
	<?php js('/libs/js/jquery-1.7.min.js', 'first');?>
	
	<?php echo $this->load->assets();?>
</head>
<body>
<div class="fixed12">
	<div class="grid_12">
		<?php echo $content; ?>
	</div>
</div>
</body></html>