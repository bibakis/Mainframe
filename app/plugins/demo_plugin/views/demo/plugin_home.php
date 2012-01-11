<?php $this->load->css('/app/plugins/demo_plugin/assets/styles.css');?>
<h1>This is a plugin view</h1>
<p>
	This view uses the 'demo' theme.
</p>

<p>
	<br>
	Theme location: <code>themes/demo/theme.php</code>
	This controller file location: <code>app/plugins/demo_plugin/controllers/home.php</code>
	It is accessed by the following URI: <code><?php echo base_url().$this->uri->uri_string();?></code>
	View location: <code>app/plugins/demo_plugin/views/demo/plugin_home.php</code>
</p>

<p>
	<h4>Testing</h4>
	<table class="styled">
		<thead>
			<tr>
				<td>Plugin Helper</td>
				<td>Plugin Library</td>
				<td>Plugin Model</td>
				<td>Plugin langugage</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?php echo $helper; ?></td>
				<td><?php echo $library; ?></td>
				<td><?php echo $model; ?></td>
				<td><?php echo $language; ?></td>
			</tr>
		</tbody>
	</table>
</p>

<p>
	Check a plugin controller that is located inside of a 
	<a href="<?php echo base_url().'demo_plugin/folder/test';?>">subfolder</a>
	or download <a href="https://github.com/bibakis/Mainframe">Mainframe</a>
</p>

