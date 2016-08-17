<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8"/>
		<title><?php echo $title; ?></title>
		
		<!-- CSS @ Customs -->
		<link type="text/css" rel="stylesheet" media="screen" href="<?php echo base_url('css/backend/style.css');?>"/>
		<link type="text/css" rel="stylesheet" media="screen" href="<?php echo base_url('css/backend/content.css');?>"/>
		<link type="text/css" rel="stylesheet" media="screen" href="<?php echo base_url('css/backend/form.css');?>"/>
		<link type="text/css" rel="stylesheet" media="screen" href="<?php echo base_url('css/backend/icons.css');?>"/>
		
		<!-- CSS @ jQuery -->
		<link type="text/css" rel="stylesheet" media="screen" href="<?php echo base_url('css/jquery/ui/jquery-ui.min.css');?>"/>
		<link type="text/css" rel="stylesheet" media="screen" href="<?php echo base_url('css/jquery/jqgrid/ui.jqgrid.css');?>"/>
		<link type="text/css" rel="stylesheet" media="screen" href="<?php echo base_url('css/backend/jqgrid.css');?>"/>
		<link type="text/css" rel="stylesheet" media="screen" href="<?php echo base_url('css/jquery/select2/select2.css');?>"/>
		<link type="text/css" rel="stylesheet" media="screen" href="<?php echo base_url('css/jquery/menunavigator/jquery.navgoco.css');?>"/>
		<link type="text/css" rel="stylesheet" media="screen" href="<?php echo base_url('css/jquery/scroll/jquery.mCustomScrollbar.min.css');?>"/>
		
		<!-- Javascript @ Global Configuration -->
		<script type="text/javascript" src="<?php echo base_url('js/helper/date.converter.js');?>"></script> <!-- Date Converter -->
		<script type="text/javascript">
			var client_picker_date_format = "<?php echo $this->config->item('client_picker_date_format');?>";
			var client_picker_time_format = "<?php echo $this->config->item('client_picker_time_format');?>";
			var client_picker_time_nosecond_format = "<?php echo $this->config->item('client_picker_time_nosecond_format');?>";
			
			var client_validate_date_format = "<?php echo $this->config->item('client_validate_date_format');?>";
			var client_validate_datetime_format = "<?php echo $this->config->item('client_validate_datetime_format');?>";
			var client_validate_datetime_nosecond_format = "<?php echo $this->config->item('client_validate_datetime_nosecond_format');?>";
			var client_validate_time_format = "<?php echo $this->config->item('client_validate_time_format');?>";
			var client_validate_time_nosecond_format = "<?php echo $this->config->item('client_validate_time_nosecond_format');?>";
			
			var server_date_format = "<?php echo $this->config->item('server_date_format');?>";
			var server_datetime_format = "<?php echo $this->config->item('server_datetime_format');?>";
			var server_datetime_nosecond_format = "<?php echo $this->config->item('server_datetime_nosecond_format');?>";
			var server_time_format = "<?php echo $this->config->item('server_time_format');?>";
			var server_time_nosecond_format = "<?php echo $this->config->item('server_time_nosecond_format');?>";
			
			var server_client_parse_validate_date_format = "<?php echo $this->config->item('server_client_parse_validate_date_format');?>";
			var server_client_parse_validate_datetime_format = "<?php echo $this->config->item('server_client_parse_validate_datetime_format');?>";
			var server_client_parse_validate_datetime_nosecond_format = "<?php echo $this->config->item('server_client_parse_validate_datetime_nosecond_format');?>";
			var server_client_parse_validate_time_format = "<?php echo $this->config->item('server_client_parse_validate_time_format');?>";
			var server_client_parse_validate_time_nosecond_format = "<?php echo $this->config->item('server_client_parse_validate_time_nosecond_format');?>";
			
			var client_jqgrid_date_format = "<?php echo $this->config->item('client_jqgrid_date_format');?>";
			var client_jqgrid_datetime_format = "<?php echo $this->config->item('client_jqgrid_datetime_format');?>";
			var client_jqgrid_datetime_nosecond_format = "<?php echo $this->config->item('client_jqgrid_datetime_nosecond_format');?>";
			var client_jqgrid_time_format = "<?php echo $this->config->item('client_jqgrid_time_format');?>";
			var client_jqgrid_time_nosecond_format = "<?php echo $this->config->item('client_jqgrid_time_nosecond_format');?>";
			
			var boolean_selectors = <?php echo json_encode($this->config->item('boolean'));?>;
			var language_selectors = <?php echo json_encode($this->config->item('languages'));?>;
		</script>
		
		<!-- Javascript @ jQuery -->
		<script type="text/javascript" src="<?php echo base_url('js/jquery/jquery-2.1.1.min.js');?>"></script> <!-- Core -->
		<script type="text/javascript" src="<?php echo base_url('js/jquery/ui/jquery-ui.min.js');?>"></script> <!-- User Interface -->
		<script type="text/javascript" src="<?php echo base_url('js/jquery/plugin/jquery.blockUI.js');?>"></script> <!-- UI Block/Freeze -->
		<script type="text/javascript" src="<?php echo base_url('js/jquery/ui/jquery-ui.timePicker.js');?>"></script> <!-- DateTime Picker -->
		<script type="text/javascript" src="<?php echo base_url('js/jquery/jqgrid/i18n/grid.locale-en.js');?>"></script> <!-- jqGrid Localization -->
		<script type="text/javascript" src="<?php echo base_url('js/jquery/jqgrid/jquery.jqGrid.min.js');?>"></script> <!-- jqGrid Core -->
		<script type="text/javascript" src="<?php echo base_url('js/jquery/jqgrid/jquery.jqGrid.extend.js');?>"></script> <!-- jqGrid Extend -->
		<script type="text/javascript" src="<?php echo base_url('js/jquery/jqgrid/jquery.jqGrid.extend-sorting.js');?>"></script> <!-- jqGrid Extend -->
		<script type="text/javascript" src="<?php echo base_url('js/jquery/validation/jquery.validate.min.js');?>"></script> <!-- Validator -->
		<script type="text/javascript" src="<?php echo base_url('js/jquery/cookie/jquery.cookie.min.js');?>"></script> <!-- Cookie -->
		<script type="text/javascript" src="<?php echo base_url('js/jquery/menunavigator/jquery.navgoco.min.js');?>"></script> <!-- Menu Navigator -->
		<script type="text/javascript" src="<?php echo base_url('js/jquery/plugin/jquery.form.js');?>"></script> <!-- Ajax Form -->
		<script type="text/javascript" src="<?php echo base_url('js/jquery/plugin/jquery.serializeJSON.js');?>"></script> <!-- JSON Serializer -->
		<script type="text/javascript" src="<?php echo base_url('js/jquery/plugin/jquery.helper.js');?>"></script> <!-- Helper -->
		<script type="text/javascript" src="<?php echo base_url('js/jquery/scroll/jquery.mCustomScrollbar.concat.min.js');?>"></script> <!-- Scroll -->
		<script type="text/javascript">
			jQuery.datepicker.setDefaults({
				changeMonth: true,
				changeYear: true,
				dateFormat: client_picker_date_format
			});
			
			jQuery.validator.addMethod("date", function(value, element) {
				var check = isDate(value, client_validate_date_format);
				return this.optional(element) || check;
			}, "Please enter a valid date");
			
			jQuery(document).ready(function(){
				left_menu_fixed_height();
			});
			
			jQuery(window).resize(function(){
				left_menu_fixed_height();
			});
			
			function left_menu_fixed_height(){
				jQuery('#left-menu').css('height', jQuery(window).height() - 95);
			}
		</script>
		<script type="text/javascript">
			$(document).ready(function() {
				// Initialize navgoco with default options
				$("#left-menu-navigator").navgoco({
					caretHtml: '',
					accordion: false,
					openClass: 'open',
					save: true,
					cookie: {
						name: 'navgoco',
						expires: false,
						path: '/'
					},
					slide: {
						duration: 0,
						easing: 'swing'
					},
					onClickAfter: function(e, submenu) {
						/*e.preventDefault();
						$('#left-menu-navigator').find('li').removeClass('active');
						var li =  $(this).parent();
						var lis = li.parents('li');
						li.addClass('active');
						lis.addClass('active');*/
					},
				});
				
				left_menu_fixed_height();
				
				$('#left-menu').mCustomScrollbar({
					  scrollInertia: 0
					, advanced : {
						  updateOnSelectorChange : true
						, updateOnContentResize : true
						, autoExpandHorizontalScroll : true
						, contentTouchScroll : true
					  }
				});
				
				function home_menu_icon_mark_selected(jQuerySelector){
					if (jQuerySelector.length > 0)
					{
						var parent = jQuerySelector.parent().parent();
						if (parent.length > 0)
						{
							$(parent).addClass('current');
							home_menu_icon_mark_selected(parent);
						}
					}
				}
				
				home_menu_icon_mark_selected($('.menu-icon > ul li.current'));
				
				var icon_background = $('li.current-page a').css('background-image');
				if (icon_background)
				{
					$('.content-right-header')
						.prepend(
							$("<div>")
								.css('background-image', icon_background)
								.css('background-repeat', 'no-repeat')
								.css('background-color', '#ffffff')
								.css('background-position', 'center')
								.css('-webkit-border-radius', '25px')
								.css('-moz-border-radius', '25px')
								.css('border-radius', '25px')
								.css('float', 'left')
								.css('margin-right', '5px')
								.width(20)
								.height(20)
								.html("&nbsp;")
						);
				}
				var menu_title = $('li.current-page > a').text();
				if (menu_title)
				{
					$('.content-right-header-title').text(menu_title);
				}
			});
		</script>
	</head>
	<body>
		<div class="content-left">
			<div class="content-left-layout ui-widget ui-widget-content ui-corner-all ui-helper-clearfix">
				<div class="content-left-header ui-widget-header ui-corner-top ui-helper-clearfix">
					<?php echo $this->config->item('application_title');?>
				</div>
				<div>
					<div>
						Welcome <?php echo $this->session->userdata('name');?>
					</div>
					<div>
						<a href="<?php echo site_url('system/user/profile');?>">Profile</a> | 
						<a href="<?php echo site_url('system/login/logout');?>">Logout</a>
					</div>
				</div>
			</div>
			<div class="content-left-layout ui-widget ui-widget-content ui-corner-all ui-helper-clearfix">
				<div class="content-left-header ui-widget-header ui-corner-top ui-helper-clearfix">
					Main Menu
				</div>
				<div id="left-menu">
					<ul id="left-menu-navigator" class="navgoco">
						<?php _build_menu_view($menus); ?>
					</ul>
				</div>
			</div>
		</div>
		<div class="content-right ui-widget ui-widget-content ui-corner-all">
			<div class="content-right-layout">
				<div class="content-right-header ui-widget-header ui-corner-top ui-helper-clearfix" style="height:15px;">
					<div class="content-right-header-title" style="margin-top:4px;"><?php echo $title; ?></div>
				</div>
				<div>
					<?php echo $content; ?>
				</div>
			</div>
			<div class="content-right-footer">
				<div class="content-right-footer-layout">
					<?php echo $this->config->item('application_copy_right');?>
				</div>
			</div>
		</div>
		<div id="dialog_alert_container">
			<p>
				<span id="dialog_alert_icon" style="float: left; margin: 0 7px 50px 0;"></span>
				<div id="dialog_alert_message"></div>
			</p>
		</div>
		<div id="dialog_confirm_container">
			<p>
				<span id="dialog_confirm_icon" style="float: left; margin: 0 7px 50px 0;"></span>
				<div id="dialog_confirm_message"></div>
			</p>
		</div>
	</body>
</html>

<?php 
/**
	Building the Menus
*/

function _build_menu_url_get($menu)
{
	$url = NULL;
	if (empty($menu->url))
	{
		if ($menu->control && $menu->action)
			$url = site_url($menu->control.'/'.$menu->action);
	}
	else
	{
		$url = $menu->url;
		$url = str_replace('{host}', site_url(), $url);
		$url = str_replace('{control}', $menu->control, $url);
		$url = str_replace('{action}', $menu->action, $url);
		$url = str_replace('{host_control_action}', site_url($menu->control.'/'.$menu->action), $url);
		$url = str_replace('{host_control}', site_url($menu->control), $url);
		$url = str_replace('{host_action}', site_url($menu->action), $url);
	}
	
	return $url;
}

function _build_menu_view($menus)
{
	foreach ($menus as $menu)
	{
		$is_current_menu = FALSE;
		$url = _build_menu_url_get($menu);
		if ($url == current_url())
			$is_current_menu = TRUE;?>
	<li class="<?php echo $menu->css;?><?php echo ($is_current_menu ? ' current current-page' : '')?>">
<?php	if (isset($menu->childs) && count($menu->childs) > 0)
		{
			_build_menu_url($menu);?>						
<ul>
<?php 		_build_menu_view($menu->childs);?>						
</ul>
<?php	}
		else
		{
			_build_menu_url($menu);
		}?>
	</li>
<?php
	}
}

function _build_menu_url($menu)
{
	$url = _build_menu_url_get($menu);
	if (!empty($url))
	{?>
<a href="<?php echo $url;?>"><?php echo $menu->name;?></a>
<?php
	}
	else
	{?>
<a href=""><?php echo $menu->name;?></a>
<?php
	}
}?>
