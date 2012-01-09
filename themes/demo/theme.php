<!DOCTYPE html><html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content="Mainframe - The complete PHP framework" />
	<meta name="keywords" content="" />
	<meta name="language" content="en" />
	<title>Mainframe - The complete PHP framework</title>
	<?php $this->load->view('mainframe.js.php');?>
	
	<?php  
		   /*
			* Leading slashes make sure that the resource is not accidentally loaded from within the active
			* module; If you wish to load a css or js file from within your active module, don't use
			* a / slash in the beggining of the path
			* 
			*/	
	?>
	
	<?php $this->load->css('/libs/css/mainframe.css/mainframe-grid.css');?>
	<?php $this->load->css('/libs/css/mainframe.css/mainframe-main.css');?>
	<?php $this->load->css('/themes/demo/css/styles.css');?>
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