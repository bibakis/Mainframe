<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="description"
	content="Mainframe - The complete PHP framework" />
<meta name="keywords" content="" />
<meta name="language" content="en" />
<title>Mainframe - The complete PHP framework</title>




	<?php $this->load->view('mainframe.js.php');?>
	<?php $this->load->css('/libs/css/mainframe.css/mainframe-grid.css');?>
	<?php $this->load->css('/libs/css/mainframe.css/mainframe-main.css');?>
	<?php $this->load->css('/themes/demo/css/styles.css');?>
	<?php $this->load->js('/libs/js/jquery-1.7.min.js');?>
	<?php echo $this->load->assets();?>
</head>
<body>
	<div class="fixed12">
		<div class="grid_12">
			<h1>This view is called by a plugin controller located in a subfolder</h1>
			<p>
				It is here to show you that you can still organise your plugin
				controllers in the same manner you used to in plain CodeIgniter
			</p>

			<br>
						
			<p>
				Controller location: <code>app/plugins/demo_plugin/controllers/folder/test.php</code>
				It is accessed by the following URI: <code><?php echo base_url().$this->uri->uri_string();?></code>
				View location: <code>app/plugins/demo_plugin/views/subfolder_view.php</code>
			</p>
			
			<p>
				Check a <a href="<?php echo base_url().'demo_plugin/';?>">themed</a>
				plugin, or download <a href="https://github.com/bibakis/Mainframe">Mainframe</a>
			</p>
		</div>
	</div>
</body>
</html>

