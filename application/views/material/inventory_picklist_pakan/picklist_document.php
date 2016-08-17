<html>
	<head>
		<title>Picking Document</title>
		<style>
			@page {
				margin: 20px;
			}
			
			body {
				font-size: 10px
			}
			
			h1 {
				text-align: center;
				background-color: #CFCFCF;
				width: 100%;
			}
			
			h2 {
				margin-bottom: 0px;
				font-size: larger;
			}
			
			.table tbody td, thead th {
				padding-left: 3px;
				padding-top: 2px;
				padding-right: 3px;
				padding-bottom: 2px;
			}
			
			.table thead th {
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
foreach ($documents as $document_idx=>$document)
{?>
		<div <?php echo $document_idx > 0 ? 'style="page-break-before: always;"' : '';?>>
			<h1>PICKLIST</h1>
			<table width="100%" border="0">
				<tr>
					<td width="15%">No</td><td width="10px">:</td>
					<td><?php echo $document->m_inventory_picklist_code;?></td>
					<td width="15%">Driver</td><td width="10px">:</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>Date</td><td>:</td>
					<td><?php echo (!empty($document->m_inventory_picklist_date) ? date($this->config->item('server_display_date_format'), strtotime($document->m_inventory_picklist_date)) : '');?></td>
					<td>No Truck</td><td>:</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>Project</td><td>:</td>
					<td colspan="4"><?php echo $document->c_project_name;?></td>
				</tr>
				<tr>
					<td>ETA</td><td>:</td>
					<td colspan="4"><?php echo (!empty($document->c_orderout_request_arrive_date) ? date($this->config->item('server_display_date_format'), strtotime($document->c_orderout_request_arrive_date)) : '');?></td>
				</tr>
			</table>
			<br/>
			
			<h2>Summary</h2>
			<table class="table" width="100%" border="0" cellspacing="0">
				<thead>
					<tr>
						<th class="line_right line_top line_bottom line_left">No</th>
						<th class="line_right line_top line_bottom">Product Code</th>
						<th class="line_right line_top line_bottom">Product Name</th>
						<th class="line_right line_top line_bottom">Qty Box</th>
						<th class="line_right line_top line_bottom">Qty KG</th>
						<th class="line_right line_top line_bottom">Notes</th>
					</tr>
				</thead>
				<tbody>
<?php
	foreach ($document->summaries as $summary_idx=>$summary)
	{?>
					<tr>
						<td class="line_right line_bottom line_left"><?php echo $summary_idx + 1;?></td>
						<td class="line_right line_bottom"><?php echo $summary->m_product_code;?></td>
						<td class="line_right line_bottom"><?php echo $summary->m_product_name;?></td>
						<td class="line_right line_bottom" align="right"><?php echo number_format_clear($summary->quantity_box);?></td>
						<td class="line_right line_bottom" align="right"><?php echo number_format_clear($summary->quantity, 4);?></td>
						<td class="line_right line_bottom">&nbsp;</td>
					</tr>
<?php
	}?>
				</tbody>
			</table>
			<br/>

<?php
	$pallet_group_records = array();
	foreach ($document->details as $detail_idx=>$detail)
	{
		$pallet_grid_product_hash = md5($detail->pallet.'__'.$detail->m_grid_id.'__'.$detail->m_product_id);
		
		if (!isset($pallet_group_records[$pallet_grid_product_hash]))
		{
			$pallet_group_records[$pallet_grid_product_hash] = clone $detail;
		}
		else
		{
			$pallet_group_records[$pallet_grid_product_hash]->quantity_box += $detail->quantity_box;
			$pallet_group_records[$pallet_grid_product_hash]->quantity += $detail->quantity;
		}
	}
	
	$full_pallet_records = array();
	$full_pallets = array();
	foreach ($pallet_group_records as $pallet_grid_product_hash=>$pallet_data)
	{
		if ($pallet_data->quantity_box == $pallet_data->m_product_pack)
		{
			$full_pallet_records[] = $pallet_data;
			$full_pallets[] = $pallet_grid_product_hash;
		}
	}
	
	$partial_pallet_records = array();
	foreach ($document->details as $detail_idx=>$detail)
	{
		$pallet_grid_product_hash = md5($detail->pallet.'__'.$detail->m_grid_id.'__'.$detail->m_product_id);
		
		if (!in_array($pallet_grid_product_hash, $full_pallets))
		{
			$partial_pallet_records[] = $detail;
		}
	}?>

<?php
	if (count($full_pallet_records) > 0)
	{?>
			<h2>Full Pallet (Detail)</h2>
			<table class="table" width="100%" border="0" cellspacing="0">
				<thead>
					<tr>
						<th class="line_right line_top line_bottom line_left">Pallet</th>
						<th class="line_right line_top line_bottom">Lokasi</th>
						<th class="line_right line_top line_bottom">Qty Box</th>
						<th class="line_right line_top line_bottom">Qty Kg</th>
						<th class="line_right line_top line_bottom">Barcode</th>
						<th class="line_right line_top line_bottom">Actual</th>
					</tr>
				</thead>
				<tbody>
<?php
		foreach ($full_pallet_records as $detail_idx=>$detail)
		{?>
					<tr>
						<td class="line_right line_bottom line_left"><?php echo $detail->pallet;?></td>
						<td class="line_right line_bottom"><?php echo $detail->m_grid_code;?></td>
						<td class="line_right line_bottom" align="right"><?php echo number_format_clear($detail->quantity_box);?></td>
						<td class="line_right line_bottom" align="right"><?php echo number_format_clear($detail->quantity, 4);?></td>
						<td class="line_right line_bottom">-</td>
						<td class="line_right line_bottom">&nbsp;</td>
					</tr>
<?php	}?>
				</tbody>
			</table>
			<br/>
<?php
	}?>

<?php
	if (count($partial_pallet_records) > 0)
	{?>
			<h2>Partial Pallet (Detail)</h2>
			<table class="table" width="100%" border="0" cellspacing="0">
				<thead>
					<tr>
						<th class="line_right line_top line_bottom line_left">Pallet</th>
						<th class="line_right line_top line_bottom">Lokasi</th>
						<th class="line_right line_top line_bottom">Qty Box</th>
						<th class="line_right line_top line_bottom">Qty KG</th>
						<th class="line_right line_top line_bottom">Barcode</th>
						<th class="line_right line_top line_bottom">Actual</th>
					</tr>
				</thead>
				<tbody>
<?php
		foreach ($partial_pallet_records as $detail_idx=>$detail)
		{
			$pallet = $detail->pallet;
			$pallet_before = (isset($partial_pallet_records[$detail_idx - 1]->pallet) ? $partial_pallet_records[$detail_idx - 1]->pallet : NULL);
			if ($pallet == $pallet_before)
				$pallet = '';
			
			$m_grid_code = $detail->m_grid_code;
			$m_grid_code_before = (isset($partial_pallet_records[$detail_idx - 1]->m_grid_code) ? $partial_pallet_records[$detail_idx - 1]->m_grid_code : NULL);
			if ($m_grid_code == $m_grid_code_before)
				$m_grid_code = '';?>
					<tr>
						<td class="line_right line_bottom line_left"><?php echo $pallet;?></td>
						<td class="line_right line_bottom"><?php echo $m_grid_code;?></td>
						<td class="line_right line_bottom" align="right"><?php echo number_format_clear($detail->quantity_box);?></td>
						<td class="line_right line_bottom" align="right"><?php echo number_format_clear($detail->quantity, 4);?></td>
						<td class="line_right line_bottom"><?php echo $detail->barcode;?>
							<!--br/><img src="<?php echo url_host_change(site_url('system/share/barcode'));?>?value=<?php echo urlencode($detail->barcode);?>&scale=1"/-->
						</td>
						<td class="line_right line_bottom">&nbsp;</td>
					</tr>
<?php	}?>
				</tbody>
			</table>
			<br/>
<?php
	}?>
			<table width="100%" border="0">
				<tr>
					<td width="33%" align="center">
						Checker,
						<br/>&nbsp;
						<br/>&nbsp;
						<br/>&nbsp;
						<br/>&nbsp;
						(______________________)
					</td>
					<td width="33%" align="center">
						Picker,
						<br/>&nbsp;
						<br/>&nbsp;
						<br/>&nbsp;
						<br/>&nbsp;
						(______________________)
					</td>
					<td width="33%" align="center">
						WH Admin,
						<br/>&nbsp;
						<br/>&nbsp;
						<br/>&nbsp;
						<br/>&nbsp;
						(______________________)
					</td>
				</tr>
			</table>
		</div>
<?php
}?>
	</body>
</html>