<?php $this->load->css('/app/plugins/demo_plugin/assets/styles.css');?>
<h1>This is a plugin view</h1>
<p>
	This view belongs to the 'demo' theme of this Mainframe installation 
	and is here to demonstrate how each plugin can be theme indepedent, as long as it's views are 
	organised by theme
</p>

<p>
	<br>
	Theme location: <code>themes/demo/theme.php</code>
	This controller file location: <code>app/plugins/demo_plugin/controllers/home.php</code>
	It is accessed by the following URI: <code><?php echo base_url().$this->uri->uri_string();?></code>
	View location: <code>app/plugins/demo_plugin/views/demo/plugin_home.php</code>
</p>

<p>
	Check a plugin controller that is located inside of a 
	<a href="<?php echo base_url().'demo_plugin/folder/test';?>">subfolder</a>
	or download <a href="https://github.com/bibakis/Mainframe">Mainframe</a>
</p>