<table width="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td width="50%" valign="top">
			<table id="system_home_inventory_product_position_list_table"></table>
			<div id="system_home_inventory_product_position_list_table_nav"></div>
		</td>
		<td valign="top">
			<div id="system_home_inventory_product_position_graph_chart_1" style="width:100%;display:inline-block"></div>
		</td>
	</tr>
</table>

<script type="text/javascript">
var system_home_inventory_product_position_graph_chart_1;

jQuery(document).ready(function(){
	system_home_inventory_product_position_list_load_table('system_home_inventory_product_position_list_table');
});

function system_home_inventory_product_position_list_load_table(table_id){
	jQuery('#' + table_id).jqGrid({
		loadError: jquery_ajax_error_handler,
		datatype: "json", 
		viewrecords: true, 
		rownumbers: true,
		shrinkToFit: false,
		rowNum: <?php echo $this->config->item('jqgrid_limit_per_page');?>, 
		rowList: [<?php echo $this->config->item('jqgrid_limit_pages');?>], 
		pginput: false,
		multiselect: true,
		jsonReader : {
			root: "data",
			page: "page",
			total: "total",
			records: "records",
			repeatitems: false
		},
		width: jqgrid_window_fixed_width(table_id),
		url: "<?php echo site_url('system/home/inventory_product_position_data');?>", 
		colNames: [
			'Id',
			'Code',
			'Product',
			'Quantity On Hand'
		], 
		colModel: [
			{name:'m_product_id', index:'inv.m_product_id', key:true, hidden:true, frozen:true},
			{name:'m_product_code', index:'pro.code', width:120, frozen:true},
			{name:'m_product_name', index:'pro.name', width:220},
			{name:'quantity_onhand', index:'quantity_onhand', width:120, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', search:false}
		],
		gridComplete: function(){
			if (!system_home_inventory_product_position_graph_chart_1)
			{
				jQuery('#system_home_inventory_product_position_graph_chart_1').css('height', jQuery('#' + table_id).getGridParam('height') + 50);
				system_home_inventory_product_position_graph_chart_1_load();
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
			system_home_inventory_product_position_graph_chart_1_show();
		},
		onSelectRow: function(rowid, status, e){
			system_home_inventory_product_position_graph_chart_1_show();
		},
		pager: '#' + table_id + '_nav'
	});
	
	jQuery("#" + table_id).jqGrid('navGrid', '#' + table_id + '_nav', {
		/* -- Button Configuration -- */
		edit: false,
		add: false,
		del: false
	});
	
	jQuery("#" + table_id).jqGrid('setFrozenColumns');
	jQuery("#" + table_id).setGridHeight(jqgrid_window_fixed_height(jQuery("#" + table_id), -200));
}
</script>

<script type="text/javascript">
function system_home_inventory_product_position_graph_chart_1_load(){
	var divHolder = document.getElementById('system_home_inventory_product_position_graph_chart_1');
	
	system_home_inventory_product_position_graph_chart_1 = new cfx.Chart();
	
	var fields = system_home_inventory_product_position_graph_chart_1.getDataSourceSettings().getFields();
	
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

	system_home_inventory_product_position_graph_chart_1.setGallery(cfx.Gallery.Pie);	
	system_home_inventory_product_position_graph_chart_1.getAllSeries().getPointLabels().setVisible(true);
	system_home_inventory_product_position_graph_chart_1.getAllSeries().setFillMode(cfx.FillMode.Pattern);
	system_home_inventory_product_position_graph_chart_1.setBorder(new cfx.SimpleBorder);;
	
	var background = new cfx.GradientBackground();
	background.getColors().setItem(0, "#FFFFFF");
	background.getColors().setItem(1, "#FFFFFF");
	system_home_inventory_product_position_graph_chart_1.setBackground(background);
	system_home_inventory_product_position_graph_chart_1.setPlotAreaBackground(background);
	
	var items = {};
	
	var crosstab = new cfx.data.CrosstabDataProvider();
	crosstab.setDataSource(items);
	
	system_home_inventory_product_position_graph_chart_1.setDataSource(crosstab);
	
	var titles = system_home_inventory_product_position_graph_chart_1.getTitles();
	var title = new cfx.TitleDockable();
	title.setText("Quantity On Hand Compare");
	titles.add(title);
	
	system_home_inventory_product_position_graph_chart_1.create(divHolder);
	
	jQuery('g.LegendItem').hide();
}

function system_home_inventory_product_position_graph_chart_1_show(){
	var m_product_ids = jQuery('#system_home_inventory_product_position_list_table').getGridParam('selarrrow');
	var m_product_records = new Array;
	var total_quantity_onhand = 0;
	jQuery.each(m_product_ids, function(idx, m_product_id){
		var m_product_ref = jQuery('#system_home_inventory_product_position_list_table').getRowData(m_product_id);
		var m_product = {
			Row		: m_product_ref.m_product_name,
			Column	: 'Quantity',
			Cell	: m_product_ref.quantity_onhand
		};
		m_product_records.push(m_product);
		total_quantity_onhand += parseFloat(m_product_ref.quantity_onhand);
	});
	
	var crosstab = new cfx.data.CrosstabDataProvider();
	crosstab.setDataSource(m_product_records);
	system_home_inventory_product_position_graph_chart_1.setDataSource(crosstab);
	
	var titles = system_home_inventory_product_position_graph_chart_1.getTitles();
	titles.c.A[0].setText("Quantity Total " + total_quantity_onhand.toString());
	
	setTimeout(function(){
		jQuery('g.LegendItem').hide();
	}, 500);
}
</script>