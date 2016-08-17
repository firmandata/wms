<!--
<table>
	<thead>
		<tr>
			<th>Warehouse</th>
			<th>Used</th>
			<th>Free</th>
			<th>Quantity</th>
			<th>Used All</th>
			<th>Free All</th>
		</tr>
	</thead>
	<tbody>
<?php
foreach ($records as $record_idx=>$record)
{?>
		<tr>
			<td><?php echo $record->m_warehouse_name;?></td>
			<td align="right">
				<?php echo number_format_clear($record->used_percent, 2);?>%
				(<?php echo $record->used;?>)
			</td>
			<td align="right">
				<?php echo number_format_clear($record->free_percent, 2);?>%
				(<?php echo ($record->free);?>)
			</td>
			<td align="right">
				<?php echo number_format_clear($record->quantity, 4);?>
			</td>
			<td align="right">
				<?php echo number_format_clear($record->all_used_percent, 2);?>%
			</td>
			<td align="right">
				<?php echo number_format_clear($record->all_free_percent, 2);?>%
			</td>
		</tr>
<?php
}?>
	</tbody>
</table>
-->

<table width="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td width="50%" valign="top">
			<div id="system_home_inventory_warehouse_graph_chart_1" style="width:100%;height:500px;display:inline-block"></div>
		</td>
		<td valign="top">
			<div id="system_home_inventory_warehouse_graph_chart_2" style="width:100%;height:500px;display:inline-block"></div>
		</td>
	</tr>
</table>

<script type="text/javascript">
$(document).ready(function(){
	var divHolder = document.getElementById('system_home_inventory_warehouse_graph_chart_1');
	
	var graph_chart = new cfx.Chart();
	
	var fields = graph_chart.getDataSourceSettings().getFields();
	
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

	graph_chart.setGallery(cfx.Gallery.Pie);	
	var graph_pie = graph_chart.getGalleryAttributes();
	graph_pie.setExplodingMode(cfx.ExplodingMode.All);
	graph_chart.getAllSeries().getPointLabels().setVisible(true);
	graph_chart.getAllSeries().setFillMode(cfx.FillMode.Pattern);
	graph_chart.setBorder(new cfx.SimpleBorder);;
	
	var background = new cfx.GradientBackground();
	background.getColors().setItem(0, "#FFFFFF");
	background.getColors().setItem(1, "#FFFFFF");
	graph_chart.setBackground(background);
	graph_chart.setPlotAreaBackground(background);
	
	var items = {};
<?php
$chart_records = array();
foreach ($records as $record_idx=>$record)
{
	$chart_record = new stdClass();
	$chart_record->Row = $record->m_warehouse_name;
	$chart_record->Column = "Usage";
	$chart_record->Cell = $record->used;
	$chart_records[] = $chart_record;
}
$chart_record = new stdClass();
$chart_record->Row = "Free";
$chart_record->Column = "Usage";
$chart_record->Cell = $m_grid_total - $used_total;
$chart_records[] = $chart_record;

if (count($chart_records) > 0)
{?>
	items = <?php echo json_encode($chart_records);?>;
<?php
}?>
	
	var crosstab = new cfx.data.CrosstabDataProvider();
	crosstab.setDataSource(items);
	
	graph_chart.setDataSource(crosstab);
	
	var titles = graph_chart.getTitles();
	var title = new cfx.TitleDockable();
	title.setText("Warehouse Usage");
	titles.add(title);
	graph_chart.getDataGrid().setVisible(true);

	graph_chart.create(divHolder);
	
	jQuery('g.LegendItem').hide();
});
</script>

<script type="text/javascript">
$(document).ready(function(){
	var divHolder = document.getElementById('system_home_inventory_warehouse_graph_chart_2');
	
	var graph_chart = new cfx.Chart();
	
	var fields = graph_chart.getDataSourceSettings().getFields();
	
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

	graph_chart.setGallery(cfx.Gallery.Pie);	
	graph_chart.getAllSeries().getPointLabels().setVisible(true);
	graph_chart.getAllSeries().setFillMode(cfx.FillMode.Pattern);
	graph_chart.setBorder(new cfx.SimpleBorder);;
	
	var background = new cfx.GradientBackground();
	background.getColors().setItem(0, "#FFFFFF");
	background.getColors().setItem(1, "#FFFFFF");
	graph_chart.setBackground(background);
	graph_chart.setPlotAreaBackground(background);
	
	var items = {};
<?php
$chart_records = array();
foreach ($records as $record_idx=>$record)
{
	$chart_record = new stdClass();
	$chart_record->Row = "Used";
	$chart_record->Column = $record->m_warehouse_name;
	$chart_record->Cell = $record->used;
	$chart_records[] = $chart_record;
	
	$chart_record = new stdClass();
	$chart_record->Row = "Free";
	$chart_record->Column = $record->m_warehouse_name;
	$chart_record->Cell = $record->free;
	$chart_records[] = $chart_record;
}
if (count($chart_records) > 0)
{?>
	items = <?php echo json_encode($chart_records);?>;
<?php
}?>
	// var items = [
		// {'Row' : 'Row A', 'Column' : 'Col A', 'Cell' : 10},
		// {'Row' : 'Row A', 'Column' : 'Col B', 'Cell' : 20},
		// {'Row' : 'Row A', 'Column' : 'Col A', 'Cell' : 10},
		// {'Row' : 'Row A', 'Column' : 'Col B', 'Cell' : 20},
		// {'Row' : 'Row B', 'Column' : 'Col A', 'Cell' : 30},
		// {'Row' : 'Row B', 'Column' : 'Col B', 'Cell' : 40}
	// ];
	
	var crosstab = new cfx.data.CrosstabDataProvider();
	crosstab.setDataSource(items);
	
	graph_chart.setDataSource(crosstab);
	
	var titles = graph_chart.getTitles();
	var title = new cfx.TitleDockable();
	title.setText("Warehouse Space");
	titles.add(title);
	graph_chart.getDataGrid().setVisible(true);
	
	graph_chart.create(divHolder);
	
	jQuery('g.LegendItem').hide();
});
</script>