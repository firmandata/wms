<html>
	<head>
		<title>Purchase Order</title>
		<style>
			@page {
				margin: 20px;
			}
			
			body {
				font-size: 12px;
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
			
			td, th {
				padding-left: 3px;
				padding-top: 2px;
				padding-right: 3px;
				padding-bottom: 2px;
				font-size: 12px;
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
		<div>
			<h1>Purchase Order</h1>
			<table width="100%" border="0" cellspacing="0"  cellpadding="10">
				<tr>
					<td width="50%" valign="top" class="line_right line_top line_bottom line_left">
						To:<br/>
						<?php echo $record->c_businesspartner_name;?><br/>
						<?php echo nl2br($record->c_businesspartner_address);?><br/>
						<?php echo $record->c_businesspartner_pic;?>
					</td>
					<td width="50%" valign="top" class="line_right line_top line_bottom">
						<table width="100%" border="0">
							<tr>
								<td width="40%">No PO</td>
								<td><?php echo $record->code;?></td>
							</tr>
							<tr>
								<td>Date</td>
								<td><?php echo (!empty($record->orderin_date) ? date($this->config->item('server_display_date_format'), strtotime($record->orderin_date)) : '');?></td>
							</tr>
							<tr>
								<td colspan="2">
									TOP<br/>
									<?php echo nl2br($record->term);?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<br/>
			<table class="table" width="100%" border="0" cellspacing="0">
				<thead>
					<tr>
						<th class="line_right line_top line_bottom line_left">No</th>
						<th class="line_right line_top line_bottom">Code</th>
						<th class="line_right line_top line_bottom">Desc</th>
						<th class="line_right line_top line_bottom">UOM</th>
						<th class="line_right line_top line_bottom">Qty Case</th>
						<th class="line_right line_top line_bottom">Quantity</th>
						<th class="line_right line_top line_bottom">Price</th>
						<th class="line_right line_top line_bottom">Total Price</th>
					</tr>
				</thead>
				<tbody>
<?php
	$subtotal = 0;
	$discount = 0;
	foreach ($c_orderindetails as $c_orderindetail_idx=>$c_orderindetail)
	{?>
					<tr>
						<td class="line_right line_bottom line_left" align="right"><?php echo $c_orderindetail_idx + 1;?></td>
						<td class="line_right line_bottom"><?php echo $c_orderindetail->m_product_code;?></td>
						<td class="line_right line_bottom"><?php echo $c_orderindetail->m_product_name;?></td>
						<td class="line_right line_bottom"><?php echo $c_orderindetail->m_product_uom;?></td>
						<td class="line_right line_bottom" align="right"><?php echo number_format_clear($c_orderindetail->quantity_box);?></td>
						<td class="line_right line_bottom" align="right"><?php echo number_format_clear($c_orderindetail->quantity, 4);?></td>
						<td class="line_right line_bottom" align="right"><?php echo number_format_clear($c_orderindetail->price, 4);?></td>
						<td class="line_right line_bottom" align="right"><?php echo number_format_clear($c_orderindetail->quantity * $c_orderindetail->price, 4);?></td>
					</tr>
<?php
		$subtotal += ($c_orderindetail->quantity * $c_orderindetail->price);
		$discount += ($c_orderindetail->quantity * $c_orderindetail->price * ($c_orderindetail->discount / 100));
	}
	$ppn = $record->ppn * ($subtotal - $discount);
	$total = $subtotal - $discount + $ppn;?>
				</tbody>
				<tfoot>
					<tr>
						<td class="line_right" colspan="3">&nbsp;</td>
						<td class="line_right line_bottom" colspan="4"><strong><em>Subtotal</em></strong></td>
						<td class="line_right line_bottom" align="right"><strong><?php echo number_format_clear($subtotal, 4);?></strong></td>
					</tr>
					<tr>
						<td class="line_right" colspan="3">&nbsp;</td>
						<td class="line_right line_bottom" colspan="4"><strong><em>Potongan Harga (Discount)</em></strong></td>
						<td class="line_right line_bottom" align="right"><strong><?php echo number_format_clear($discount, 4);?></strong></td>
					</tr>
					<tr>
						<td class="line_right" colspan="3">&nbsp;</td>
						<td class="line_right line_bottom" colspan="4"><strong><em>PPN <?php echo ($record->ppn * 100);?>%</em></strong></td>
						<td class="line_right line_bottom" align="right"><strong><?php echo number_format_clear($ppn, 4);?></strong></td>
					</tr>
					<tr>
						<td class="line_right" colspan="3">&nbsp;</td>
						<td class="line_right line_bottom" colspan="4"><strong><em>Total</em></strong></td>
						<td class="line_right line_bottom" align="right"><strong><?php echo number_format_clear($total, 4);?></strong></td>
					</tr>
				</tfoot>
			</table>
			<br/>
<?php
if (!empty($record->notes))
{?>
			<br/>
			Catatan:<br/>
			<?php echo nl2br($record->notes);?><br/>
			<br/>
<?php
}?>
			Demikian surat ini disampaikan, terima kasih.<br/>
			<br/>
			<table width="100%" border="0">
				<tr>
					<td width="33%" align="center">
						PT. DUA PUTRA PERKASA PRAKARSA
						<br/>Hormat Kami,
						<br/>&nbsp;
						<br/>&nbsp;
						<br/>&nbsp;
						<br/>&nbsp;
						<br/>&nbsp;
						<u><?php echo $record->signer;?></u>
					</td>
					<td>&nbsp;</td>
				</tr>
			</table>
		</div>
	</body>
</html>