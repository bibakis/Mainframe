<h2>Login</h2><br>
<p>
	Not a member ? <a href="<?php echo base_url();?>user/register/">Create a new account</a>
</p>
<br>

<?php if (isset($error)): ?>
	<div class="error_box">
		Wrong username or password. Please try again.
	</div>
<?php endif;?>

<div id="user_box">
	<form id="login_form" method="post" action="">
		<label for="username">Username or email</label><br>
		<input type="text" name="username" id="username">
		<br><br>
		<label for="password">Password</label><br>
		<input type="password" name="password" id="password">
		<br><br>
		<input type="submit" value="Login">
	</form>
</div>