<div id="login">
	<div class="title">Login</div>
<?php 
	echo form_open('system/login/authentication');?>
	<table>
	<?php if(isset($message) && !empty($message)){ ?>
		<tr><td colspan="2"><div class="message"><?php echo $message; ?></div></td></tr>
	<?php } ?>
		<tr><td><label for="username">Username</label></td>
			<td>
				<?php 
					echo form_input(
						array(
							'name'        => 'username',
							'id'          => 'username',
							'value'       => set_value('username'),
							'maxlength'   => '32'
						)
					);?>
			</td>
		</tr>
		<tr><td><label for="password">Password</label></td>
			<td>
				<?php 
					echo form_password(
						array(
							'name'        => 'password',
							'id'          => 'password',
							'maxlength'   => '15'
						)
					);?>
			</td>
		</tr>
		<tr><td colspan="2" align="right">
				<?php echo form_submit('login', 'Login'); ?>
			</td>
		</tr>
		<tr><td colspan="2">
				<a href="<?php echo site_url('welcome/forget_password');?>">Forget Password</a>
			</td>
		</tr>
	</table>
	<?php 
	echo form_hidden('return', $this->input->get_post('return'));
	echo form_close();?>
</div>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery('#username').focus();
});
</script>