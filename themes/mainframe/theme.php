<!DOCTYPE html><html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content="Mainframe - The complete PHP framework" />
	<meta name="keywords" content="" />
	<meta name="language" content="en" />
	<title>Mainframe - The complete PHP framework</title>
	<?php $this->load->view('mainframe.js.php');?>
	<?php $this->load->css('/libs/css/mainframe.css/mainframe-grid.css');?>
	<?php $this->load->css('/libs/css/mainframe.css/mainframe-main.css');?>
	<?php $this->load->css('/themes/mainframe/css/styles.css');?>
	<?php $this->load->js('/libs/js/jquery-1.7.min.js');?>
	<?php echo $this->load->assets();?>
</head>
<body>
<div class="fixed12">
	<div class="grid_12">
		<?php echo $content; ?>
	</div>
</div>
</body></html>