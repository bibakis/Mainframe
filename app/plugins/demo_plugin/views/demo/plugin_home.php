<?php $this->load->css('/app/plugins/demo_plugin/assets/styles.css');?>
<h1>This is a plugin view</h1>
<p>
	This view belongs to the 'demo' theme and is here to demonstrate how
	each plugin can have it's very own theme
</p>

<p>
	This controller file is located in the path <code>app/plugins/demo_plugin/controllers/home.php</code>
	It is accessed by the <?php echo base_url().$this->uri->uri_string();?> URI and it calls the view
	located inside of the <code>app/plugins/demo_plugin/views/demo/plugin_home.php</code>
</p>

<p>
	Check a plugin controller that is located inside of a 
	<a href="<?php echo base_url().'demo_plugin/folder/test';?>">subfolder</a>
	or download <a href="https://github.com/bibakis/Mainframe">Mainframe</a>
</p>