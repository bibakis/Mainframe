<div id="container">
	<h1>Welcome to <a href="https://github.com/bibakis/Mainframe">MainframePHP</a></h1>

	<div id="body">
		<p>This is the demo application using the <em>demo</em> theme. <br>
		It is here to help you get started with the basics of Mainframe.</p>
		<br>
		<ul class="main_menu">
			<li><a href="https://github.com/bibakis/Mainframe">View source code on GitHub</a></li>
			<li>Documentation is on it's way</li>
		</ul>
		
		<?php if (session('user_id')):?>
			<br>
			user_id: <?php echo session('user_id');?>
			<br><a href="<?php echo base_url()?>user/logout/">Logout</a>
		<?php endif;?>
	
	</div>

	
	
	<p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds</p>
</div>