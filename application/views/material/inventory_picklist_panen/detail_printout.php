<html>
	<head>
		<title>Faktur Penjualan</title>
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
<?php
foreach ($c_orderouts as $c_orderout_idx=>$c_orderout)
{?>
		<div <?php echo $c_orderout_idx > 0 ? 'style="page-break-before: always;"' : '';?>>
			<table width="100%" border="0" cellspacing="0">
				<tr>
					<td width="25%" valign="center" rowspan="2" class="line_right line_top line_bottom line_left" align="center">
						<img src="<?php echo base_url('img/logo.png');?>"/>
					</td>
					<td width="25%" valign="top" rowspan="2" class="line_right line_top line_bottom">
						PT. Dua Putra Perkasa Pratama
						<br/>Tambak Udang Vannamei
						<br/>Kawasan Industri Cipendewa
						<br/>Jl. Baru Cipendewa No.88
						<br/>Jati Asih Bekasi 17117
					</td>
					<td width="25%" valign="center" align="center" class="line_right line_top line_bottom">
						<strong>
							FAKTUR PENJUALAN
						</strong>
					</td>
					<td width="25%" valign="top" rowspan="2" class="line_right line_top line_bottom">
						Kepada:
						<br/><?php echo $c_orderout->c_businesspartner_name;?>
						<br/><?php echo nl2br($c_orderout->c_businesspartner_address);?>
						<br/><?php echo $c_orderout->c_businesspartner_pic;?>
					</td>
				</tr>
				<tr>
					<td valign="center" align="center" class="line_right line_bottom">
						Nomor : <?php echo $record->code;?>
					</td>
				</tr>
				<tr>
					<td colspan="2" class="line_right line_bottom line_left">
						Phone : 021-2955-5555 / Fax : 021-2956-6666
					</td>
					<td align="center" class="line_right line_bottom">
						Tanggal : <?php echo (!empty($record->picklist_date) ? date($this->config->item('server_display_date_format'), strtotime($record->picklist_date)) : '');?>
					</td>
					<td class="line_right line_bottom">
						Sales : 
					</td>
				</tr>
			</table>
			
			<br/>
			<table class="table" width="100%" border="0" cellspacing="0">
				<thead>
					<tr>
						<th class="line_right line_top line_bottom line_left">No</th>
						<th class="line_right line_top line_bottom">Description</th>
						<th class="line_right line_top line_bottom">Quantity</th>
						<th class="line_right line_top line_bottom">Size</th>
						<th class="line_right line_top line_bottom">Price (IDR)</th>
						<th class="line_right line_top line_bottom">Total Amount (IDR)</th>
					</tr>
				</thead>
				<tbody>
<?php
	$quantity_total = 0;
	$subtotal = 0;
	foreach ($c_orderout->m_inventory_picklistdetails as $m_inventory_picklistdetail_idx=>$m_inventory_picklistdetail)
	{?>
					<tr>
						<td class="line_right line_bottom line_left" align="right"><?php echo $m_inventory_picklistdetail_idx + 1;?></td>
						<td class="line_right line_bottom"><?php echo $m_inventory_picklistdetail->m_product_name;?></td>
						<td class="line_right line_bottom" align="right"><?php echo number_format_clear($m_inventory_picklistdetail->quantity, 4);?></td>
						<td class="line_right line_bottom" align="right"><?php echo number_format_clear($m_inventory_picklistdetail->product_size, 4);?></td>
						<td class="line_right line_bottom" align="right"><?php echo number_format_clear($m_inventory_picklistdetail->price, 4);?></td>
						<td class="line_right line_bottom" align="right"><?php echo number_format_clear($m_inventory_picklistdetail->quantity * $m_inventory_picklistdetail->price, 4);?></td>
					</tr>
<?php
		$quantity_total += $m_inventory_picklistdetail->quantity;
		$subtotal += ($m_inventory_picklistdetail->quantity * $m_inventory_picklistdetail->price);
	}?>
				</tbody>
				<tfoot>
					<tr>
						<td class="line_right line_bottom line_left" colspan="2" align="right">Quantity Total</td>
						<td class="line_right line_bottom" align="right"><?php echo number_format_clear($quantity_total, 4);?></td>
						<td class="line_right line_bottom" colspan="2" align="right">Subtotal</td>
						<td class="line_right line_bottom" align="right"><?php echo number_format_clear($subtotal, 4);?></td>
					</tr>
					<tr>
						<td class="line_right line_bottom line_left" colspan="6" align="right">
							<strong>
								<em><?php echo ucwords(number_to_spelling_bahasa($subtotal));?></em>
							</strong>
						</td>
					</tr>
					<tr>
						<td class="line_right line_bottom line_left" colspan="6">
							<ul>
								<li>Komplain hanya dilayani pada saat tanggal faktur</li>
								<li>Kami mohon BG/Cek agar diatas namakan <strong>PT. DUA PUTRA PERKASA PRATAMA</strong> no rekening <strong>687-0922-238</strong>(BCA KCP Pondok Gede)</li>
							</ul>
						</td>
					</tr>
					<tr>
						<td class="line_right line_bottom line_left" colspan="2" align="center">Diterima Oleh,</td>
						<td class="line_right line_bottom" align="center">Dikirim Oleh,</td>
						<td class="line_right line_bottom" colspan="2" align="center">Dibuat Oleh,</td>
						<td class="line_right line_bottom" align="center">Hormat Kami,</td>
					</tr>
					<tr>
						<td class="line_right line_bottom line_left" colspan="2" align="center">
							<br/>&nbsp;
							<br/>&nbsp;
							<br/>&nbsp;
							<br/>&nbsp;
						</td>
						<td class="line_right line_bottom" align="center">&nbsp;</td>
						<td class="line_right line_bottom" colspan="2" align="center">&nbsp;</td>
						<td class="line_right line_bottom" align="center">&nbsp;</td>
					</tr>
				</tfoot>
			</table>
		</div>
<?php
}?>
	</body>
</html>