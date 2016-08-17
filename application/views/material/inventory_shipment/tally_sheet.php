<html>
	<head>
		<title>Tally Sheet - Out</title>
		<style>
			@page {
				margin: 20px;
			}
			
			body {
				font-size: 10px;
			}
			
			.table tbody td, thead th, tfoot td {
				padding-left: 3px;
				padding-top: 2px;
				padding-right: 3px;
				padding-bottom: 2px;
				font-size: 10px;
			}
			
			.table thead th, tfoot td {
				background-color: #CFCFCF;
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
$header_counter = 0;
foreach ($data as $header_idx=>$header)
{?>
		<table class="table" width="100%" border="0" cellspacing="0"<?php echo $header_counter > 0 ? ' style="page-break-before:always;"' : '';?>>
			<tr>
				<td class="line_left line_right line_top" align="center" style="font-size:14px !important;">
					<div style="position:absolute; margin-left:5px; margin-top:5px;">
						<img src="<?php echo site_url('system/share/barcode');?>?value=<?php echo $header->code;?>&type=code128&scale=1&thickness=22"/>
					</div>
					Data Tally Pengiriman Barang
				</td>
			</tr>
			<tr>
				<td class="line_left line_right line_bottom" align="center" style="font-size:12px !important;">
					<strong><?php echo strtoupper($header->c_project_name);?></strong>
				</td>
			</tr>
		</table>
		<table class="table" width="100%" border="0" cellspacing="0">
			<tr>
				<td width="15%" class="line_left"><strong>No</strong></td><td width="10px">:</td>
				<td><?php echo $header->code;?></td>
				<td width="15%"><strong>Vehicle No</strong></td><td width="10px">:</td>
				<td class="line_right"><?php echo $header->m_inventory_shipment_vehicle_no;?></td>
			</tr>
			<tr>
				<td class="line_left"><strong>Date</strong></td><td>:</td>
				<td><?php echo (!empty($header->shipment_date) ? date($this->config->item('server_display_date_format'), strtotime($header->shipment_date)) : '');?></td>
				<td><strong>Driver</strong></td><td>:</td>
				<td class="line_right"><?php echo $header->m_inventory_shipment_vehicle_driver;?></td>
			</tr>
			<tr>
				<td class="line_left"><strong>Business Partner</strong></td><td>:</td>
				<td><?php echo $header->c_businesspartner_name;?></td>
				<td><strong>External No</strong></td><td>:</td>
				<td class="line_right"><?php echo $header->c_orderout_external_no;?></td>
			</tr>
			<tr>
				<td class="line_left line_bottom"><strong>Project</strong></td><td class="line_bottom" width="10px">:</td>
				<td class="line_bottom"><?php echo $header->c_project_name;?></td>
				<td class="line_bottom"><strong>Print Date</strong></td><td class="line_bottom">:</td>
				<td class="line_right line_bottom"><?php echo date($this->config->item('server_display_datetime_format'));?></td>
			</tr>
		</table>
<?php
	$product_counter = 0;
	foreach ($header->products as $product_idx=>$product)
	{
		$carton_group_quantity = 15;?>
		<table class="table" width="100%" border="0" cellspacing="0">
			<tr>
				<td class="line_left line_right" colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td width="15%" class="line_left"><strong>Product</strong></td><td width="10px">:</td>
				<td class="line_right"><?php echo $product->m_product_code;?></td>
			</tr>
			<tr>
				<td class="line_left">Name</td><td>:</td>
				<td class="line_right"><?php echo $product->m_product_name;?></td>
			</tr>
			<tr>
				<td class="line_left">UOM</td><td>:</td>
				<td class="line_right"><?php echo $product->m_product_uom;?></td>
			</tr>
		</table>
		<table class="table" width="100%" border="0" cellspacing="0">
<?php	if ($product_counter == 0)
		{?>
			<thead>
				<tr>
					<th class="line_right line_top line_bottom line_left">Packed Group/Box</th>
<?php		for ($carton_idx = 0; $carton_idx < $carton_group_quantity; $carton_idx++)
			{?>
					<th class="line_right line_top line_bottom" width="35px"><?php echo $carton_idx + 1;?></th>
<?php		}?>
				</tr>
			</thead>
<?php	}?>

			<tbody>
<?php	$packed_group_carton_quantity_subtotal = 0;
		$carton_count_subtotal = 0;
		$packed_group_counter = 0;
		foreach ($product->packed_groups as $packed_group_idx=>$packed_group)
		{
			$css_class_line_top = '';
			if ($product_counter > 0 && $packed_group_counter == 0)
				$css_class_line_top = 'line_top';
			$carton_count = count($packed_group->cartons);
			$packed_group_rows = 1;
			if ($carton_count > 0)
				$packed_group_rows = ceil($carton_count / $carton_group_quantity);
			?>
<?php		$packed_group_carton_quantity = 0;
			$carton_counter = 0;
			for ($packed_group_row = 0; $packed_group_row < $packed_group_rows; $packed_group_row++)
			{?>
				<tr>
<?php			if ($packed_group_row == 0)
				{?>
					<td class="line_right line_left <?php echo $css_class_line_top;?>" align="center" valign="top">
						<?php echo $packed_group->packed_group;?>
					</td>
<?php			}
				else
				{?>
					<td class="line_right line_left" align="center" valign="top">
						&nbsp;
					</td>
<?php			}
				$css_class_line_top = '';
				if ($product_counter > 0 && $packed_group_counter == 0 && $carton_counter == 0)
					$css_class_line_top = 'line_top';
				for ($carton_idx = 0; $carton_idx < $carton_group_quantity; $carton_idx++)
				{?>
					<td class="line_right line_bottom <?php echo $css_class_line_top;?>" align="right" width="35px">
<?php				if ($carton_counter < $carton_count && isset($packed_group->cartons[$carton_counter]))
					{
						$carton = $packed_group->cartons[$carton_counter];
						echo number_format_clear($carton->quantity, 4);
						
						$packed_group_carton_quantity += $carton->quantity;
						$carton_counter++;
					}?>
					</td>
<?php			}?>
				</tr>
<?php		}
			$packed_group_carton_quantity_subtotal += $packed_group_carton_quantity;
			$carton_count_subtotal += $carton_count;?>
				<tr>
					<td class="line_left line_right line_bottom">&nbsp;</td>
					<td class="line_right line_bottom" colspan="<?php echo $carton_group_quantity;?>">
						<em>Total : <?php echo number_format_clear($packed_group_carton_quantity, 4);?> <?php echo $product->m_product_uom;?> / <?php echo number_format_clear($carton_count);?> Box</em>
					</td>
				</tr>
<?php		$packed_group_counter++;
		}?>
			</tbody>
			<tfoot>
				<tr>
					<td class="line_left line_right line_bottom" colspan="<?php echo $carton_group_quantity + 1;?>">
						Total Item <?php echo $product->m_product_name;?> : <?php echo number_format_clear($packed_group_carton_quantity_subtotal, 4);?> <?php echo $product->m_product_uom;?> / <?php echo number_format_clear($carton_count_subtotal);?> Box
					</td>
				</tr>
			</tfoot>
		</table>
<?php	$product_counter++;
	}?>
		<br/>
		<table class="table" width="100%" border="0" cellspacing="0">
			<tr>
				<td width="33%" align="center">
					Pelanggan
					<br/>&nbsp;
					<br/>&nbsp;
					<br/>&nbsp;
					<br/>(...............................)
				</td>
				<td width="33%" align="center">
					Checker,
					<br/>&nbsp;
					<br/>&nbsp;
					<br/>&nbsp;
					<br/>(...............................)
				</td>
				<td width="33%" align="center">
					WH Admin,
					<br/>&nbsp;
					<br/>&nbsp;
					<br/>&nbsp;
					<br/>(...............................)
				</td>
			</tr>
		</table>
<?php
	$header_counter++;
}?>
	</body>
</html>