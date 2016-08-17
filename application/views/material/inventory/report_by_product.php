<html>
	<head>
		<title>Inventory By Product Summary</title>
		<style>
			@page {
			}
			
			body {
				font-size: 12px
			}
			
			table.table-document-header td {
				font-size: 12px;
				font-weight: bold;
			}
			
			.table tbody td, .table thead td, .table tfoot td, .table thead th {
				font-size: 10px;
				padding-left: 3px;
				padding-top: 2px;
				padding-right: 3px;
				padding-bottom: 2px;
			}
			
			.table thead th {
				background-color: #00FF00;
			}
			
			.table tfoot td.header {
				background-color: #CFCFCF;
				font-weight: bold;
			}
			
			.table td.header-1 {
				background-color: #CFCFCF;
			}
			
			.table td.header-2 {
				background-color: #CFCFCF;
				font-weight: bold;
			}
			
			.netto-background-custom {
				background-color: #FFFF00;
			}
			
			.line_top {
				border-top: 1px solid #000;
			}
			
			.line_left {
				border-left: 1px solid #000;
			}
			
			.line_right {
				border-right: 1px solid #000;
			}
			
			.line_bottom {
				border-bottom: 1px solid #000;
			}
		</style>
	</head>
	<body>
<?php
$project_counter = 0;
foreach ($records as $c_project)
{?>
		<div <?php echo $project_counter > 0 ? 'style="page-break-before: always;"' : '';?>>
			<table width="100%" border="0" class="table-document-header">
				<tr>
					<td colspan="3">LAPORAN STOK PER-ITEM</td>
				</tr>
				<tr>
					<td width="15%">Project</td><td width="10px">:</td>
					<td><?php echo ($c_project->name !== NULL ? $c_project->name : '-');?></td>
				</tr>
				<tr>
					<td>Date</td><td>:</td>
					<td><?php echo date($this->config->item('server_display_datetime_format'));?></td>
				</tr>
			</table>
			<br/>
			
			<table class="table" width="100%" border="0" cellspacing="0">
				<thead>
					<tr>
						<th class="line_right line_top line_bottom line_left" rowspan="2">Item #</th>
						<th class="line_right line_top line_bottom" rowspan="2">Description</th>
						<th class="line_right line_top line_bottom" rowspan="2">Merk</th>
						<th class="line_right line_top line_bottom" rowspan="2">Net</th>
						<th class="line_right line_top" colspan="4">Stok (By Carton)</th>
						<th class="line_right line_top" colspan="4">Stok (By KG)</th>
					</tr>
					<tr>
						<th class="line_right line_top line_bottom" width="5%">Existed</th>
						<th class="line_right line_top line_bottom" width="5%">Allocated</th>
						<th class="line_right line_top line_bottom" width="5%">Picked</th>
						<th class="line_right line_top line_bottom" width="5%">On Hand</th>
						<th class="line_right line_top line_bottom" width="5%">Existed</th>
						<th class="line_right line_top line_bottom" width="5%">Allocated</th>
						<th class="line_right line_top line_bottom" width="5%">Picked</th>
						<th class="line_right line_top line_bottom" width="5%">On Hand</th>
					</tr>
				</thead>
<?php
	$total_quantity_box_exist = 0;
	$total_quantity_box_allocated = 0;
	$total_quantity_box_picked = 0;
	$total_quantity_box_onhand = 0;
	$total_quantity_exist = 0;
	$total_quantity_allocated = 0;
	$total_quantity_picked = 0;
	$total_quantity_onhand = 0;
	foreach ($c_project->records as $m_product_type)
	{?>
				<thead>
					<tr>
						<td class="line_right line_left" colspan="12">
							&nbsp;
						</td>
					</tr>
					<tr>
						<td class="line_left line_bottom line_right line_top header-1" colspan="2">
							<strong><?php echo ($m_product_type->name !== NULL ? $m_product_type->name : '-');?></strong>
						</td>
						<td class="line_right line_bottom" colspan="10">
							&nbsp;
						</td>
					</tr>
				</thead>
<?php	foreach ($m_product_type->records as $m_productgroup)
		{?>
				<thead>
					<tr>
						<td class="line_bottom line_left line_right">
							&nbsp;
						</td>
						<td class="line_bottom" align="center">
							<strong><?php echo ($m_productgroup->name !== NULL ? $m_productgroup->name : '-');?></strong>
						</td>
						<td class="line_right line_bottom" colspan="10">
							&nbsp;
						</td>
					</tr>
				</thead>
				<tbody>
<?php		$quantity_box_exist = 0;
			$quantity_box_allocated = 0;
			$quantity_box_picked = 0;
			$quantity_box_onhand = 0;
			$quantity_exist = 0;
			$quantity_allocated = 0;
			$quantity_picked = 0;
			$quantity_onhand = 0;
			foreach ($m_productgroup->records as $record)
			{?>
					<tr>
						<td class="line_right line_bottom line_left"><?php echo $record->m_product_code;?></td>
						<td class="line_right line_bottom"><?php echo $record->m_product_name;?></td>
						<td class="line_right line_bottom"><?php echo $record->m_product_brand;?></td>
<?php			if ($record->m_product_netto > 0)
				{?>
						<td class="line_right line_bottom" align="right"><?php echo number_format_clear($record->m_product_netto, 4);?></td>
<?php			}
				else
				{?>
						<td class="line_right line_bottom netto-background-custom" align="center">Random</td>
<?php			}?>
						
						<td class="line_right line_bottom" align="right"><?php echo number_format_clear($record->quantity_box_exist);?></td>
						<td class="line_right line_bottom" align="right"><?php echo number_format_clear($record->quantity_box_allocated);?></td>
						<td class="line_right line_bottom" align="right"><?php echo number_format_clear($record->quantity_box_picked);?></td>
						<td class="line_right line_bottom" align="right"><?php echo number_format_clear($record->quantity_box_onhand);?></td>
						<td class="line_right line_bottom" align="right"><?php echo number_format_clear($record->quantity_exist, 4);?></td>
						<td class="line_right line_bottom" align="right"><?php echo number_format_clear($record->quantity_allocated, 4);?></td>
						<td class="line_right line_bottom" align="right"><?php echo number_format_clear($record->quantity_picked, 4);?></td>
						<td class="line_right line_bottom" align="right"><?php echo number_format_clear($record->quantity_onhand, 4);?></td>
					</tr>
<?php			$quantity_box_exist += $record->quantity_box_exist;
				$quantity_box_allocated += $record->quantity_box_allocated;
				$quantity_box_picked += $record->quantity_box_picked;
				$quantity_box_onhand += $record->quantity_box_onhand;
				$quantity_exist += $record->quantity_exist;
				$quantity_allocated += $record->quantity_allocated;
				$quantity_picked += $record->quantity_picked;
				$quantity_onhand += $record->quantity_onhand;
			}
			$total_quantity_box_exist += $quantity_box_exist;
			$total_quantity_box_allocated += $quantity_box_allocated;
			$total_quantity_box_picked += $quantity_box_picked;
			$total_quantity_box_onhand += $quantity_box_onhand;
			$total_quantity_exist += $quantity_exist;
			$total_quantity_allocated += $quantity_allocated;
			$total_quantity_picked += $quantity_picked;
			$total_quantity_onhand += $quantity_onhand;?>
					<tr>
						<td class="line_right line_left">
							&nbsp;
						</td>
						<td class="line_bottom header-2" align="right">
							Sub Total <?php echo ($m_productgroup->name !== NULL ? $m_productgroup->name : '-');?>
						</td>
						<td class="line_right line_bottom header-2" colspan="2">
							&nbsp;
						</td>
						<td class="line_right line_bottom header-2" align="right"><?php echo number_format_clear($quantity_box_exist);?></td>
						<td class="line_right line_bottom header-2" align="right"><?php echo number_format_clear($quantity_box_allocated);?></td>
						<td class="line_right line_bottom header-2" align="right"><?php echo number_format_clear($quantity_box_picked);?></td>
						<td class="line_right line_bottom header-2" align="right"><?php echo number_format_clear($quantity_box_onhand);?></td>
						<td class="line_right line_bottom header-2" align="right"><?php echo number_format_clear($quantity_exist, 4);?></td>
						<td class="line_right line_bottom header-2" align="right"><?php echo number_format_clear($quantity_allocated, 4);?></td>
						<td class="line_right line_bottom header-2" align="right"><?php echo number_format_clear($quantity_picked, 4);?></td>
						<td class="line_right line_bottom header-2" align="right"><?php echo number_format_clear($quantity_onhand, 4);?></td>
					</tr>
				</tbody>
<?php	}
	}?>
				<tfoot>
					<tr>
						<td class="line_right line_bottom line_left" colspan="12">
							&nbsp;
						</td>
					</tr>
					<tr>
						<td class="line_right line_bottom line_left header" align="center" colspan="4">
							Total <?php echo ($c_project->name !== NULL ? $c_project->name : '-');?>
						</td>
						<td class="line_right line_bottom header" align="right"><?php echo number_format_clear($total_quantity_box_exist);?></td>
						<td class="line_right line_bottom header" align="right"><?php echo number_format_clear($total_quantity_box_allocated);?></td>
						<td class="line_right line_bottom header" align="right"><?php echo number_format_clear($total_quantity_box_picked);?></td>
						<td class="line_right line_bottom header" align="right"><?php echo number_format_clear($total_quantity_box_onhand);?></td>
						<td class="line_right line_bottom header" align="right"><?php echo number_format_clear($total_quantity_exist, 4);?></td>
						<td class="line_right line_bottom header" align="right"><?php echo number_format_clear($total_quantity_allocated, 4);?></td>
						<td class="line_right line_bottom header" align="right"><?php echo number_format_clear($total_quantity_picked, 4);?></td>
						<td class="line_right line_bottom header" align="right"><?php echo number_format_clear($total_quantity_onhand, 4);?></td>
					</tr>
				</tfoot>
			</table>
		</div>
<?php
	$project_counter++;
}?>
	</body>
</html>