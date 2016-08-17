<div class="content-right-header-toolbar toolbar-one-line ui-state-default ui-corner-all ui-helper-clearfix">
<?php 
echo form_open('system/home/inventory_ontime_delivery',
	array(
		'name'	=> 'system_home_inventory_ontime_form',
		'id'	=> 'system_home_inventory_ontime_form'
	)
);?>
	<label for="system_home_inventory_ontime_form_year">Period</label>
<?php 
$year_options = array();
for ($year_begin = date('Y') + 1; $year_begin >= date('Y') - 9; $year_begin--)
{
	$year_options[$year_begin] = $year_begin;
}
echo form_dropdown('from_year', $year_options, date('Y'), 'id="system_home_inventory_ontime_form_year"');
echo form_close();?>
</div>

<div id="system_home_inventory_ontime_graph_chart_1" style="width:100%;height:470px;display:inline-block"></div>
<script type="text/javascript">
var system_home_inventory_ontime_graph_chart_1;
jQuery(document).ready(function(){
	var divHolder = document.getElementById('system_home_inventory_ontime_graph_chart_1');
	
	system_home_inventory_ontime_graph_chart_1 = new cfx.Chart();
	
	var fields = system_home_inventory_ontime_graph_chart_1.getDataSourceSettings().getFields();
	
	var field1 = new cfx.FieldMap();
	field1.setName("Row");
	field1.setUsage(cfx.FieldUsage.RowHeading);
	fields.add(field1);

	var field2 = new cfx.FieldMap();
	field2.setName("Column");
	field2.setUsage(cfx.FieldUsage.ColumnHeading);
	fields.add(field2);

	var fieldVal = new cfx.FieldMap();
	fieldVal.setName("Cell");
	fieldVal.setUsage(cfx.FieldUsage.Value);
	fields.add(fieldVal);

	system_home_inventory_ontime_graph_chart_1.setGallery(cfx.Gallery.Lines);	
	system_home_inventory_ontime_graph_chart_1.getAllSeries().getPointLabels().setVisible(true);
	system_home_inventory_ontime_graph_chart_1.getAllSeries().setFillMode(cfx.FillMode.Pattern);
	system_home_inventory_ontime_graph_chart_1.setBorder(new cfx.SimpleBorder);;
	
	var background = new cfx.GradientBackground();
	background.getColors().setItem(0, "#FFFFFF");
	background.getColors().setItem(1, "#FFFFFF");
	system_home_inventory_ontime_graph_chart_1.setBackground(background);
	system_home_inventory_ontime_graph_chart_1.setPlotAreaBackground(background);
	
	var items = {};
	
	var crosstab = new cfx.data.CrosstabDataProvider();
	crosstab.setDataSource(items);
	
	system_home_inventory_ontime_graph_chart_1.setDataSource(crosstab);
	
	var titles = system_home_inventory_ontime_graph_chart_1.getTitles();
	var title = new cfx.TitleDockable();
	title.setText("On Time Delivery");
	titles.add(title);
	system_home_inventory_ontime_graph_chart_1.getDataGrid().setVisible(true);

	system_home_inventory_ontime_graph_chart_1.create(divHolder);
	
	jQuery('g.LegendItem').hide();
});
</script>

<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery('#system_home_inventory_ontime_form').validate({
		submitHandler: function(form){
			system_home_inventory_ontime_show();
		}
	});
	
	jQuery('#system_home_inventory_ontime_form_year').change(function(){
		jQuery('#system_home_inventory_ontime_form').submit();
	});
	
	system_home_inventory_ontime_show();
});

function system_home_inventory_ontime_show(){
	jQuery.ajax({
		url: "<?php echo site_url('system/home/inventory_ontime_delivery_data');?>",
		data : {
			year	: jQuery('#system_home_inventory_ontime_form_year').val()
		},
		type: "GET",
		dataType: "json",
		error: jquery_ajax_error_handler,
		beforeSend: function(jqXHR, settings){
			jquery_blockui();
		},
		success: function(data, textStatus, jqXHR){
			var crosstab = new cfx.data.CrosstabDataProvider();
			crosstab.setDataSource(data);
			system_home_inventory_ontime_graph_chart_1.setDataSource(crosstab);
			
			setTimeout(function(){
				jQuery('g.LegendItem').hide();
			}, 500);
		},
		complete: function(jqXHR, textStatus){
			jquery_unblockui();
		}
	});
}
</script>