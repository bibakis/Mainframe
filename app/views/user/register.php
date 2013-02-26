<h2>Register</h2><br>
<p>
	Already a member ? <a href="<?php echo base_url();?>user/login/">Login here</a>
</p>
<br>

<?php if (isset($error)): ?>
	<div class="error_box">
		User already exists or invalid username or password. Please try again.
	</div>
<?php endif;?>

<div id="user_box">
	<form id="registration_form" method="post" action="">
		<label for="username">Username or email</label><br>
		<input type="text" name="username" id="username">
		<br><br>
		<label for="password">Password</label><br>
		<input type="password" name="password" id="password">
		<br><br>
		<input type="submit" value="Register">
	</form>
</div>