<!DOCTYPE html><html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content="Mainframe - The complete PHP framework" />
	<meta name="keywords" content="" />
	<meta name="language" content="en" />
	<title>Mainframe - The complete PHP framework</title>
	<script type="text/javascript" src="/libs/js/jquery-1.7.min.js"></script>
	<?php $this->load->view('mainframe.js.php');?>
	<?php $this->load->css('/libs/css/mainframe.css/mainframe-grid.css');?>
	<?php $this->load->css('/libs/css/mainframe.css/mainframe-main.css');?>
	<?php $this->load->less('/themes/mainframe/less/styles.less');?>
	<?php $this->load->css('/libs/css/bootstrap/css/bootstrap.min.css');?>
	<?php $this->load->js('/libs/css/bootstrap/js/bootstrap.min.js');?>
	<?php echo $this->load->assets();?>
</head>
<body>
<div class="fixed12">
	<div class="grid_12">
		<ul class="nav nav-tabs">
			<li>
				<a href="<?php echo base_url();?>mainframe/dev_tools/unit_testing">Unit testing</a>
			</li>
			<li>
				<a href="javascript:;">Config vars</a>
			</li>
			<li>
				<a href="javascript:;">Log files</a>
			</li>
		</ul>
		<?php echo $content; ?>
	</div>
</div>
</body></html>