<div id="login">
	<div class="title">Forget Password</div>
<?php 
	echo form_open('system/login/forget_password');?>
	<table>
	<?php if(isset($message) && !empty($message)){ ?>
		<tr><td colspan="2"><div class="message"><?php echo $message; ?></div></td></tr>
	<?php } ?>
		<tr><td><label for="email">Email</label></td>
			<td>
				<?php 
					echo form_input(
						array(
							'name'        => 'email',
							'id'          => 'email',
							'value'       => set_value('email'),
							'maxlength'   => '100'
						)
					); ?>
			</td>
		</tr>
		<tr><td colspan="2" align="right">
			<?php 
				echo form_submit('request', 'Request New Password');?>
			</td>
		</tr>
		<tr><td colspan="2">
				<a href="<?php echo site_url('welcome/index');?>">Login</a>
			</td>
		</tr>
	</table>
	<?php 
	echo form_close();?>
</div>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery('#email').focus();
});
</script>