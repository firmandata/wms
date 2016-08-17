<html>
	<head>
		<title>Label Document</title>
		<style>
			body, table {
				font-size: 12px
			}
			
			h1 {
				text-align: left;
				width: 100%;
				font-size: x-large;
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
		<table width="100%" border="0" cellspacing="0">
			<tr>
				<td rowspan="3" class="line_bottom">
					<h1>Advance Shipping Notice</h1>
				</td>
			</tr>
			<tr>
				<td>Print Date</td>
				<td>:</td>
				<td><?php echo date('d F Y H:i');?></td>
			</tr>
			<tr>
				<td class="line_bottom">Print By</td>
				<td class="line_bottom">:</td>
				<td class="line_bottom"><?php echo $user_name;?></td>
			</tr>
		</table>
		<table border="0">
			<tr>
				<td width="25%" valign="top">ASN NO</td><td width="10px" valign="top">:</td>
				<td align="center">
					<img src="<?php echo url_host_change(site_url('system/share/barcode'));?>?value=<?php echo urlencode($header->code);?>&type=code128&scale=1"/>
					<br/><?php echo $header->code;?>
				</td>
			</tr>
			<tr>
				<td valign="top">DATE</td><td valign="top">:</td>
				<td><?php echo (!empty($header->receive_date) ? date('d F Y', strtotime($header->receive_date)) : '');?></td>
			</tr>
			<tr>
				<td valign="top">Vehicle No</td><td valign="top">:</td>
				<td><?php echo $header->vehicle_no;?></td>
			</tr>
			<tr>
				<td valign="top">Driver</td><td valign="top">:</td>
				<td><?php echo $header->vehicle_driver;?></td>
			</tr>
			<tr>
				<td valign="top">PO Number</td><td valign="top">:</td>
				<td><?php echo $header->c_orderin_code;?></td>
			</tr>
			<tr>
				<td valign="top">External No</td><td valign="top">:</td>
				<td><?php echo $header->c_orderin_external_no;?></td>
			</tr>
		</table>
		<br/>
		
		<table class="table" width="100%" border="0" cellspacing="0">
			<thead>
				<tr>
					<th class="line_top line_bottom" width="25px">No</th>
					<th class="line_top line_bottom">Product No</th>
					<th class="line_top line_bottom" align="left">Product Name</th>
					<th class="line_top line_bottom" align="left">UOM</th>
					<th class="line_top line_bottom" align="left">Qty Box</th>
					<th class="line_top line_bottom" align="left">Qty Kg</th>
				</tr>
			</thead>
			<tbody>
<?php
foreach ($details as $detail_idx=>$detail)
{?>
				<tr>
					<td class="line_bottom" align="right"><?php echo $detail_idx + 1;?></td>
					<td class="line_bottom" align="center">
						<img src="<?php echo url_host_change(site_url('system/share/barcode'));?>?value=<?php echo urlencode($detail->m_product_code);?>&type=code128&scale=1"/>
						<br/><?php echo $detail->m_product_code;?>
					</td>
					<td class="line_bottom"><?php echo $detail->m_product_name;?></td>
					<td class="line_bottom"><?php echo $detail->m_product_uom;?></td>
					<td class="line_bottom"><?php echo number_format_clear($detail->quantity_box);?></td>
					<td class="line_bottom"><?php echo number_format_clear($detail->quantity, 4);?></td>
				</tr>
<?php
}?>
			</tbody>
		</table>
	</body>
</html>