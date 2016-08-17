<html>
	<head>
		<title>Label Document</title>
		
		<script type="text/javascript" src="<?php echo base_url('js/jquery/jquery-2.1.1.min.js');?>"></script>
		<script type="text/javascript" src="<?php echo base_url('js/jquery/barcode/jquery.qrcode.min.js');?>"></script>
		
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
				font-size: 14px;
				font-family: arial;
			}
			
			.table tbody td, thead th, tfoot td {
				padding-left: 3px;
				padding-top: 2px;
				padding-right: 3px;
				padding-bottom: 2px;
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
foreach ($details as $detail_idx=>$detail)
{?>
		<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0"<?php echo $detail_idx > 0 ? ' style="page-break-before:always;"' : '';?>>
			<tr>
				<td align="center" valign="middle">
					<table class="table" border="0" cellspacing="0" cellpadding="0" width="370px" height="470px">
						<tr>
							<td colspan="4" align="center" class="line_left line_top line_right">
								<img src="<?php echo site_url('system/share/barcode');?>?value=<?php echo $detail->barcode;?>&type=code128&scale=1&thickness=55"/>
							</td>
						</tr>
						<tr>
							<td class="line_bottom line_left" valign="middle" width="90px">Serial No</td><td class="line_bottom" valign="middle">:</td>
							<td class="line_bottom line_right" colspan="2" style="font-size:18px;"><?php echo $detail->barcode;?></td>
						</tr>
						<tr>
							<td class="line_left" valign="middle">SKU</td><td valign="middle">:</td>
							<td class="line_right" colspan="2" style="font-size:22px;"><?php echo $detail->m_product_code;?></td>
						</tr>
						<tr>
							<td class="line_bottom line_left line_right" colspan="4">
								<?php echo $detail->m_product_name;?>
							</td>
						</tr>
						<tr>
							<td class="line_left" valign="middle">Lot No</td><td valign="middle">:</td>
							<td class="line_right" colspan="2" style="font-size:22px;"><?php echo $detail->lot_no;?></td>
						</tr>
						<tr>
							<td class="line_bottom line_left" valign="middle">CTN No</td><td class="line_bottom" valign="middle">:</td>
							<td class="line_bottom line_right" colspan="2" style="font-size:22px;"><?php echo $detail->carton_no;?></td>
						</tr>
						<tr>
							<td class="line_left" valign="middle">Location</td><td valign="middle">:</td>
							<td class="line_right" colspan="2" style="font-size:22px;"><?php echo $detail->m_grid_code;?></td>
						</tr>
						<tr>
							<td class="line_bottom line_left" valign="middle">Receive Date</td><td class="line_bottom" valign="middle">:</td>
							<td class="line_bottom"><?php echo (!empty($detail->m_inventory_receive_date) ? date('d M Y', strtotime($detail->m_inventory_receive_date)) : '');?></td>
							<td class="line_bottom line_right" align="center">
								<div data-id="qrcode_<?php echo md5($detail->barcode);?>" data-value="<?php echo $detail->barcode;?>"></div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
<?php
}?>
		<script type="text/javascript">
<?php
foreach ($details as $detail_idx=>$detail)
{?>
			var barcode_element = jQuery("div[data-id='qrcode_<?php echo md5($detail->barcode);?>']");
			barcode_element.qrcode({
				width	: 42,
				height	: 42,
				text	: barcode_element.attr('data-value')
			});
<?php
}?>
		</script>
	</body>
</html>