<html>
	<head>
		<title>Label Document</title>
		<style>
			@page {
				margin: 0px;
				padding: 0px;
			}
			
			body {
				margin: 0px;
				padding: 0px;
			}
			
			body, table {
				font-size: 13px;
			}
		</style>
	</head>
	<body>
<?php
foreach ($details as $detail_idx=>$detail)
{?>
		<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0"<?php echo $detail_idx > 0 ? ' style="page-break-before:always;"' : '';?>>
			<tr>
				<td align="center" valign="middle">
					<table border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td valign="top" width="80px">Project</td><td valign="top" width="10px">:</td>
							<td>
								<img src="<?php echo site_url('system/share/barcode');?>?value=<?php echo $detail->c_project_code;?>&type=code128&scale=1&thickness=22"/>
								<br/><?php echo $detail->c_project_code;?>
							</td>
						</tr>
						<tr>
							<td valign="top">Pallet ID</td><td valign="top">:</td>
							<td><img src="<?php echo site_url('system/share/barcode');?>?value=<?php echo $detail->pallet;?>&type=code128&scale=1&thickness=22"/>
								<br/><?php echo $detail->pallet;?>
							</td>
						</tr>
						<tr>
							<td valign="top">Product Code</td><td valign="top">:</td>
							<td><img src="<?php echo site_url('system/share/barcode');?>?value=<?php echo $detail->m_product_code;?>&type=code128&scale=1&thickness=22"/>
								<br/><?php echo $detail->m_product_code;?>
							</td>
						</tr>
						<tr>
							<td valign="top">Product Name</td><td valign="top">:</td>
							<td><?php echo $detail->m_product_name;?></td>
						</tr>
						<tr>
							<td valign="top">STD-Pack</td><td valign="top">:</td>
							<td width="110px"><?php echo $detail->m_product_pack;?> Box/Pallet</td>
						</tr>
						<tr>
							<td valign="top">Lot</td><td valign="top">:</td>
							<td><?php echo $detail->lot_no;?></td>
						</tr>
						<tr>
							<td valign="top">Exp. Date</td><td valign="top">:</td>
							<td><?php echo (!empty($detail->expired_date) ? date('d-M-Y', strtotime($detail->expired_date)) : '');?></td>
						</tr>
						<tr>
							<td valign="top">PO No</td><td valign="top">:</td>
							<td><?php echo $detail->c_orderin_code;?></td>
						</tr>
						<tr>
							<td valign="top">Receive Date</td><td valign="top">:</td>
							<td><?php echo (!empty($detail->m_inventory_receive_date) ? date('d-M-Y', strtotime($detail->m_inventory_receive_date)) : '');?></td>
						</tr>
						<tr>
							<td valign="top">Qty Box/Kg</td><td valign="top">:</td>
							<td><?php echo number_format_clear($detail->quantity_box);?> Box / <?php echo number_format_clear($detail->quantity, 4);?></td>
						</tr>
						<tr>
							<td valign="top">Location</td><td valign="top">:</td>
							<td><?php echo $detail->m_grid_code;?></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
<?php
}?>
	</body>
</html>