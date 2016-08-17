<div class="content-right-header-toolbar toolbar-one-line ui-state-default ui-corner-all ui-helper-clearfix">
<?php 
echo form_open('system/home/inventory_stock_accuration',
	array(
		'name'	=> 'system_home_inventory_stock_accuration_form',
		'id'	=> 'system_home_inventory_stock_accuration_form'
	)
);?>
	<label for="system_home_inventory_stock_accuration_form_month">Period</label>
<?php 
$months = $this->config->item('months');
$month_options = array();
foreach($months as $month=>$month_name)
{
	$month_options[$month] = $month_name;
}
echo form_dropdown('from_month', $month_options, date('n'), 'id="system_home_inventory_stock_accuration_form_month"');

$year_options = array();
for ($year_begin = date('Y') + 1; $year_begin >= date('Y') - 9; $year_begin--)
{
	$year_options[$year_begin] = $year_begin;
}
echo form_dropdown('from_year', $year_options, date('Y'), 'id="system_home_inventory_stock_accuration_form_year"');
echo form_close();?>
</div>

<table width="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td width="50%" valign="top">
			<table id="system_home_inventory_stock_accuration_list_table"></table>
			<div id="system_home_inventory_stock_accuration_list_table_nav"></div>
		</td>
		<td valign="top">
			<div id="system_home_inventory_stock_accuration_graph_chart_1" style="width:100%;display:inline-block"></div>
		</td>
	</tr>
</table>

<script type="text/javascript">
var system_home_inventory_stock_accuration_graph_chart_1;

jQuery(document).ready(function(){
	system_home_inventory_stock_accuration_list_load_table('system_home_inventory_stock_accuration_list_table');
	
	jQuery('#system_home_inventory_stock_accuration_form').validate({
		submitHandler: function(form){
			jQuery('#system_home_inventory_stock_accuration_list_table').setGridParam({
				postData : system_home_inventory_stock_accuration_list_get_param()
			});
			jQuery('#system_home_inventory_stock_accuration_list_table').trigger("reloadGrid", [{page:1}]);
		}
	});
	
	jQuery('#system_home_inventory_stock_accuration_form_month,#system_home_inventory_stock_accuration_form_year').change(function(){
		jQuery('#system_home_inventory_stock_accuration_form').submit();
	});
});

function system_home_inventory_stock_accuration_list_load_table(table_id){
	jQuery('#' + table_id).jqGrid({
		loadError: jquery_ajax_error_handler,
		datatype: "json", 
		viewrecords: true, 
		rownumbers: true,
		shrinkToFit: false,
		rowNum: 100000, 
		pginput: false,
		pgbuttons: false,
		multiselect: true,
		jsonReader : {
			root: "data",
			page: "page",
			total: "total",
			records: "records",
			repeatitems: false
		},
		width: jqgrid_window_fixed_width(table_id),
		url: "<?php echo site_url('system/home/inventory_stock_accuration_data');?>", 
		postData : system_home_inventory_stock_accuration_list_get_param(),
		colNames: [
			'Id',
			'Code',
			'Product',
			'Accuration'
		], 
		colModel: [
			{name:'m_product_id', index:'ood.m_product_id', key:true, hidden:true, frozen:true},
			{name:'m_product_code', index:'pro.code', width:120, frozen:true, sortable:false},
			{name:'m_product_name', index:'pro.name', width:220, frozen:true, sortable:false},
			{name:'accuration', index:'accuration', width:80, sortable:false, align:'right', 
			 formatter: function (cellvalue, options, rowObject){
				return cellvalue + ' %';
			 }
			}
		],
		gridComplete: function(){
			if (!system_home_inventory_stock_accuration_graph_chart_1)
			{
				jQuery('#system_home_inventory_stock_accuration_graph_chart_1').css('height', jQuery('#' + table_id).getGridParam('height') + 50);
				system_home_inventory_stock_accuration_graph_chart_1_load();
			}
		},
		loadComplete: function(data){
			var record_counter = 1;
			jQuery.each(data.data, function(idx, record){
				jQuery('#' + table_id).setSelection(record.m_product_id, false);
				if (record_counter >= 5)
					return false;
				record_counter++;
			});
			system_home_inventory_stock_accuration_graph_chart_1_show();
		},
		onSelectRow: function(rowid, status, e){
			system_home_inventory_stock_accuration_graph_chart_1_show();
		},
		pager: '#' + table_id + '_nav'
	});
	
	jQuery("#" + table_id).jqGrid('navGrid', '#' + table_id + '_nav', {
		/* -- Button Configuration -- */
		edit: false,
		add: false,
		del: false,
		search: false
	});
	
	jQuery("#" + table_id).jqGrid('setFrozenColumns');
	jQuery("#" + table_id).setGridHeight(jqgrid_window_fixed_height(jQuery("#" + table_id), -255));
}

function system_home_inventory_stock_accuration_list_get_param(){
	var params = {
		year	: jQuery('#system_home_inventory_stock_accuration_form_year').val(),
		month	: jQuery('#system_home_inventory_stock_accuration_form_month').val()
	};
	
	return params;
}
</script>

<script type="text/javascript">
function system_home_inventory_stock_accuration_graph_chart_1_load(){
	var divHolder = document.getElementById('system_home_inventory_stock_accuration_graph_chart_1');
	
	system_home_inventory_stock_accuration_graph_chart_1 = new cfx.Chart();
	
	var fields = system_home_inventory_stock_accuration_graph_chart_1.getDataSourceSettings().getFields();
	
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

	system_home_inventory_stock_accuration_graph_chart_1.setGallery(cfx.Gallery.Pie);	
	system_home_inventory_stock_accuration_graph_chart_1.getAllSeries().getPointLabels().setVisible(true);
	system_home_inventory_stock_accuration_graph_chart_1.getAllSeries().setFillMode(cfx.FillMode.Pattern);
	system_home_inventory_stock_accuration_graph_chart_1.setBorder(new cfx.SimpleBorder);;
	
	var background = new cfx.GradientBackground();
	background.getColors().setItem(0, "#FFFFFF");
	background.getColors().setItem(1, "#FFFFFF");
	system_home_inventory_stock_accuration_graph_chart_1.setBackground(background);
	system_home_inventory_stock_accuration_graph_chart_1.setPlotAreaBackground(background);
	
	var items = {};
	
	var crosstab = new cfx.data.CrosstabDataProvider();
	crosstab.setDataSource(items);
	
	system_home_inventory_stock_accuration_graph_chart_1.setDataSource(crosstab);
	
	var titles = system_home_inventory_stock_accuration_graph_chart_1.getTitles();
	var title = new cfx.TitleDockable();
	title.setText("Accuration Compare");
	titles.add(title);
	
	system_home_inventory_stock_accuration_graph_chart_1.create(divHolder);
	
	jQuery('g.LegendItem').hide();
}

function system_home_inventory_stock_accuration_graph_chart_1_show(){
	var m_product_ids = jQuery('#system_home_inventory_stock_accuration_list_table').getGridParam('selarrrow');
	var m_product_records = new Array;
	var total_accuration = 0;
	jQuery.each(m_product_ids, function(idx, m_product_id){
		var m_product_ref = jQuery('#system_home_inventory_stock_accuration_list_table').getRowData(m_product_id);
		var m_product = {
			Row		: m_product_ref.m_product_name,
			Column	: 'Accuration',
			Cell	: m_product_ref.accuration
		};
		m_product_records.push(m_product);
		total_accuration += parseFloat(m_product_ref.accuration);
	});
	var total_percentage_accuration = 0;
	if (m_product_ids.length > 0)
	{
		total_percentage_accuration = total_accuration / m_product_ids.length;
	}
	
	var crosstab = new cfx.data.CrosstabDataProvider();
	crosstab.setDataSource(m_product_records);
	system_home_inventory_stock_accuration_graph_chart_1.setDataSource(crosstab);
	
	var titles = system_home_inventory_stock_accuration_graph_chart_1.getTitles();
	titles.c.A[0].setText("Accuration Compare with " + total_percentage_accuration.toFixed(2).toString() + "%");
	
	setTimeout(function(){
		jQuery('g.LegendItem').hide();
	}, 500);
}
</script>