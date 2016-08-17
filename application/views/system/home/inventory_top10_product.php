<div class="content-right-header-toolbar toolbar-one-line ui-state-default ui-corner-all ui-helper-clearfix">
<?php 
echo form_open('system/home/inventory_top10_product',
	array(
		'name'	=> 'system_home_inventory_top10_form',
		'id'	=> 'system_home_inventory_top10_form'
	)
);?>
	<label for="system_home_inventory_top10_form_month">Period</label>
<?php 
$months = $this->config->item('months');
$month_options = array();
foreach($months as $month=>$month_name)
{
	$month_options[$month] = $month_name;
}
echo form_dropdown('from_month', $month_options, date('n'), 'id="system_home_inventory_top10_form_month"');

$year_options = array();
for ($year_begin = date('Y') + 1; $year_begin >= date('Y') - 9; $year_begin--)
{
	$year_options[$year_begin] = $year_begin;
}
echo form_dropdown('from_year', $year_options, date('Y'), 'id="system_home_inventory_top10_form_year"');
echo form_close();?>
</div>

<div id="system_home_inventory_top10_data"></div>

<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery('#system_home_inventory_top10_form').validate({
		submitHandler: function(form){
			system_home_inventory_top10_show();
		}
	});
	
	jQuery('#system_home_inventory_top10_form_month,#system_home_inventory_top10_form_year').change(function(){
		jQuery('#system_home_inventory_top10_form').submit();
	});
	
	system_home_inventory_top10_show();
});

function system_home_inventory_top10_show(){
	jQuery.ajax({
		url: "<?php echo site_url('system/home/inventory_top10_product_data');?>",
		data : {
			month	: jQuery('#system_home_inventory_top10_form_month').val(),
			year	: jQuery('#system_home_inventory_top10_form_year').val()
		},
		type: "GET",
		dataType: "html",
		error: jquery_ajax_error_handler,
		beforeSend: function(jqXHR, settings){
			jquery_blockui($('#system_home_inventory_top10_data'));
		},
		success: function(data, textStatus, jqXHR){
			jQuery('#system_home_inventory_top10_data').html(data);
		},
		complete: function(jqXHR, textStatus){
			jquery_unblockui($('#system_home_inventory_top10_data'));
		}
	});
}
</script>