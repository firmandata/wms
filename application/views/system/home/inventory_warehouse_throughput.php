<div id="system_home_inventory_warehouse_throughput_graph_chart_1" style="width:100%;display:inline-block"></div>
<table id="system_home_inventory_warehouse_throughput_list_table"></table>
<div id="system_home_inventory_warehouse_throughput_list_table_nav"></div>

<script type="text/javascript">
var system_home_inventory_warehouse_throughput_graph_chart_1;

jQuery(document).ready(function(){
	system_home_inventory_warehouse_throughput_list_load_table('system_home_inventory_warehouse_throughput_list_table');
});

function system_home_inventory_warehouse_throughput_list_load_table(table_id){
	jQuery('#' + table_id).jqGrid({
		loadError: jquery_ajax_error_handler,
		datatype: "json", 
		viewrecords: true, 
		rownumbers: true,
		shrinkToFit: false,
		rowNum: 10000, 
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
		url: "<?php echo site_url('system/home/inventory_warehouse_throughput_data');?>", 
		colNames: [
			'Id',
			'Code',
			'Product',
<?php
foreach ($days as $day)
{?>
			'In',
			'Out',
<?php
}?>
		], 
		colModel: [
			{name:'m_product_id', index:'inv.m_product_id', key:true, hidden:true, frozen:true},
			{name:'m_product_code', index:'pro.code', width:120, sortable:false, frozen:true},
			{name:'m_product_name', index:'pro.name', width:220, sortable:false, frozen:true},
<?php
foreach ($days as $day)
{?>
			{name:'quantity_in_<?php echo $day;?>', index:'quantity_in_<?php echo $day;?>', width:70, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', sortable:false},
			{name:'quantity_out_<?php echo $day;?>', index:'quantity_out_<?php echo $day;?>', width:70, formatter:'number', formatoptions:{decimalPlaces: 4}, align:'right', sortable:false},
<?php
}?>			
		],
		gridComplete: function(){
			if (!system_home_inventory_warehouse_throughput_graph_chart_1)
			{
				jQuery('#system_home_inventory_warehouse_throughput_graph_chart_1').css('height', jQuery('#' + table_id).getGridParam('height') + 50);
				system_home_inventory_warehouse_throughput_graph_chart_1_load();
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
			system_home_inventory_warehouse_throughput_graph_chart_1_show();
		},
		onSelectRow: function(rowid, status, e){
			system_home_inventory_warehouse_throughput_graph_chart_1_show();
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
	
	jQuery("#" + table_id).jqGrid('setGroupHeaders', {
		useColSpanStyle: true, 
		groupHeaders:[
<?php
foreach ($days as $day)
{?>
			{startColumnName: 'quantity_in_<?php echo $day;?>', numberOfColumns: 2, titleText: '<?php echo $day;?>'},
<?php
}?>
		]
	});
	
	jQuery("#" + table_id).jqGrid('setFrozenColumns');
	jQuery("#" + table_id).setGridHeight(185);
}
</script>

<script type="text/javascript">
function system_home_inventory_warehouse_throughput_graph_chart_1_load(){
	var divHolder = document.getElementById('system_home_inventory_warehouse_throughput_graph_chart_1');
	
	system_home_inventory_warehouse_throughput_graph_chart_1 = new cfx.Chart();
	
	var fields = system_home_inventory_warehouse_throughput_graph_chart_1.getDataSourceSettings().getFields();
	
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

	system_home_inventory_warehouse_throughput_graph_chart_1.setGallery(cfx.Gallery.Lines);	
	system_home_inventory_warehouse_throughput_graph_chart_1.getAllSeries().getPointLabels().setVisible(true);
	system_home_inventory_warehouse_throughput_graph_chart_1.getAllSeries().setFillMode(cfx.FillMode.Pattern);
	system_home_inventory_warehouse_throughput_graph_chart_1.setBorder(new cfx.SimpleBorder);;
	
	var background = new cfx.GradientBackground();
	background.getColors().setItem(0, "#FFFFFF");
	background.getColors().setItem(1, "#FFFFFF");
	system_home_inventory_warehouse_throughput_graph_chart_1.setBackground(background);
	system_home_inventory_warehouse_throughput_graph_chart_1.setPlotAreaBackground(background);
	
	var items = {};
	
	var crosstab = new cfx.data.CrosstabDataProvider();
	crosstab.setDataSource(items);
	
	system_home_inventory_warehouse_throughput_graph_chart_1.setDataSource(crosstab);
	
	var titles = system_home_inventory_warehouse_throughput_graph_chart_1.getTitles();
	var title = new cfx.TitleDockable();
	title.setText("Warehouse Throughput");
	titles.add(title);
	
	system_home_inventory_warehouse_throughput_graph_chart_1.create(divHolder);
	
	jQuery('g.LegendItem').hide();
}

function system_home_inventory_warehouse_throughput_graph_chart_1_show(){
	var m_product_ids = jQuery('#system_home_inventory_warehouse_throughput_list_table').getGridParam('selarrrow');
<?php
foreach ($days as $day)
{?>
	var m_product_in_<?php echo $day;?>_total = 0;
	var m_product_out_<?php echo $day;?>_total = 0;
<?php
}?>
	var m_product_out_totals = new Array;
	jQuery.each(m_product_ids, function(idx, m_product_id){
		var m_product_ref = jQuery('#system_home_inventory_warehouse_throughput_list_table').getRowData(m_product_id);
		
<?php
foreach ($days as $day)
{?>
		m_product_in_<?php echo $day;?>_total += parseFloat(m_product_ref.quantity_in_<?php echo $day;?>);
		m_product_out_<?php echo $day;?>_total += parseFloat(m_product_ref.quantity_out_<?php echo $day;?>);
<?php
}?>
	});

	var m_product_records = new Array;
	var m_product = {};
	<?php
foreach ($days as $day)
{?>
	m_product = {
		Row		: '<?php echo $day;?>',
		Column	: 'In',
		Cell	: m_product_in_<?php echo $day;?>_total
	};
	m_product_records.push(m_product);
	
	m_product = {
		Row		: '<?php echo $day;?>',
		Column	: 'Out',
		Cell	: m_product_out_<?php echo $day;?>_total
	};
	m_product_records.push(m_product);
	
	m_product = {
		Row		: '<?php echo $day;?>',
		Column	: 'Avg',
		Cell	: (m_product_in_<?php echo $day;?>_total + m_product_out_<?php echo $day;?>_total) / 2
	};
	m_product_records.push(m_product);
<?php
}?>
	
	var crosstab = new cfx.data.CrosstabDataProvider();
	crosstab.setDataSource(m_product_records);
	system_home_inventory_warehouse_throughput_graph_chart_1.setDataSource(crosstab);
	
	setTimeout(function(){
		jQuery('g.LegendItem').hide();
	}, 500);
}
</script>