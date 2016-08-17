<html>
	<head>
		<title>Nota Timbang</title>
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
				text-align: center;
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
			<table width="100%" border="0" cellspacing="0">
				<tr>
					<td valign="center" class="line_bottom" align="center" width="100px">
						<img src="<?php echo base_url('img/logo.png');?>"/>
					</td>
					<td valign="top" class="line_bottom">
						PT. Dua Putra Perkasa Pratama
						<br/>Tambak Udang Vannamei
						<br/>Kawasan Industri Cipendewa
						<br/>Jl. Baru Cipendewa No.88
						<br/>Jati Asih Bekasi 17117
					</td>
				</tr>
			</table>
			<br/>
			<table width="100%" border="0">
				<tr>
					<td width="15%">Nomor Slip</td><td width="10px">:</td>
					<td><?php echo $record->code;?></td>
					<td width="15%">No Order</td><td width="10px">:</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>Tanggal</td><td>:</td>
					<td><?php echo (!empty($record->balance_date) ? date($this->config->item('server_display_date_format'), strtotime($record->balance_date)) : '');?></td>
					<td>Print Date</td><td>:</td>
					<td><?php echo date($this->config->item('server_display_datetime_format'));?></td>
				</tr>
				<tr>
					<td>Kolam/periode</td><td>:</td>
					<td><?php echo $record->m_grid_code;?> / <?php echo $record->harvest_sequence;?></td>
					<td>Keterangan</td><td>:</td>
					<td><?php echo nl2br($record->notes);?></td>
				</tr>
			</table>
			<br/>
			<h2>NOTA TIMBANG</h2>
			<br/>
<?php
$details = array();
foreach ($m_inventory_balancedetails as $m_inventory_balancedetail_idx=>$m_inventory_balancedetail)
{
	
}?>
			<table class="table" width="100%" border="0" cellspacing="0">
				<thead>
					<tr>
						<th class="line_right line_top line_bottom line_left">No</th>
						<th class="line_right line_top line_bottom">Berat (Kg)</th>
						<th class="line_right line_top line_bottom">No</th>
						<th class="line_right line_top line_bottom">Berat (Kg)</th>
						<th class="line_right line_top line_bottom">No</th>
						<th class="line_right line_top line_bottom">Berat (Kg)</th>
						<th class="line_right line_top line_bottom">No</th>
						<th class="line_right line_top line_bottom">Berat (Kg)</th>
					</tr>
				</thead>
				<tbody>
<?php
$col1_total = 0;
$col2_total = 0;
$col3_total = 0;
$col4_total = 0;
for ($detail_counter = 0; $detail_counter < 10; $detail_counter++)
{
	$col1 = 0;
	if (isset($m_inventory_balancedetails[$detail_counter]))
		$col1 = $m_inventory_balancedetails[$detail_counter]->quantity;
	$col2 = 0;
	if (isset($m_inventory_balancedetails[$detail_counter + 10]))
		$col2 = $m_inventory_balancedetails[$detail_counter + 10]->quantity;
	$col3 = 0;
	if (isset($m_inventory_balancedetails[$detail_counter + 20]))
		$col3 = $m_inventory_balancedetails[$detail_counter + 20]->quantity;
	$col4 = 0;
	if (isset($m_inventory_balancedetails[$detail_counter + 30]))
		$col4 = $m_inventory_balancedetails[$detail_counter + 30]->quantity;
	
	$col1_print = '';
	if ($col1 > 0)
		$col1_print = number_format_clear($col1, 4);
	$col2_print = '';
	if ($col2 > 0)
		$col2_print = number_format_clear($col2, 4);
	$col3_print = '';
	if ($col3 > 0)
		$col3_print = number_format_clear($col3, 4);
	$col4_print = '';
	if ($col4 > 0)
		$col4_print = number_format_clear($col4, 4);?>
					<tr>
						<td class="line_right line_bottom line_left" align="right"><?php echo $detail_counter + 1;?></td>
						<td class="line_right line_bottom" align="right"><?php echo $col1_print;?></td>
						<td class="line_right line_bottom" align="right"><?php echo $detail_counter + 11;?></td>
						<td class="line_right line_bottom" align="right"><?php echo $col2_print;?></td>
						<td class="line_right line_bottom" align="right"><?php echo $detail_counter + 21;?></td>
						<td class="line_right line_bottom" align="right"><?php echo $col3_print;?></td>
						<td class="line_right line_bottom" align="right"><?php echo $detail_counter + 31;?></td>
						<td class="line_right line_bottom" align="right"><?php echo $col4_print;?></td>
					</tr>
<?php
	$col1_total += $col1;
	$col2_total += $col2;
	$col3_total += $col3;
	$col4_total += $col4;
}?>
				</tbody>
				<tfoot>
					<tr>
						<td class="line_right line_bottom line_left" align="center">Subtotal</td>
						<td class="line_right line_bottom" align="right"><?php echo number_format_clear($col1_total, 4);?></td>
						<td class="line_right line_bottom" align="center">Subtotal</td>
						<td class="line_right line_bottom" align="right"><?php echo number_format_clear($col2_total, 4);?></td>
						<td class="line_right line_bottom" align="center">Subtotal</td>
						<td class="line_right line_bottom" align="right"><?php echo number_format_clear($col3_total, 4);?></td>
						<td class="line_right line_bottom" align="center">Subtotal</td>
						<td class="line_right line_bottom" align="right"><?php echo number_format_clear($col4_total, 4);?></td>
					</tr>
				</tfoot>
			</table>
			<br/>
			<table width="100%" border="0">
				<tr>
					<td width="50%" valign="top">
						<table border="0">
							<tr>
								<td>Subtotal I</td>
								<td>=</td>
								<td align="right"><?php echo number_format_clear($col1_total, 4);?></td>
								<td>Kg</td>
							</tr>
							<tr>
								<td>Subtotal II</td>
								<td>=</td>
								<td align="right"><?php echo number_format_clear($col2_total, 4);?></td>
								<td>Kg</td>
							</tr>
							<tr>
								<td>Subtotal III</td>
								<td>=</td>
								<td align="right"><?php echo number_format_clear($col3_total, 4);?></td>
								<td>Kg</td>
							</tr>
							<tr>
								<td>Subtotal IV</td>
								<td>=</td>
								<td align="right"><?php echo number_format_clear($col4_total, 4);?></td>
								<td>Kg</td>
							</tr>
							<tr>
								<td>Total</td>
								<td>=</td>
								<td align="right"><?php echo number_format_clear($col1_total + $col2_total + $col3_total + $col4_total, 4);?></td>
								<td>Kg</td>
							</tr>
						</table>
					</td>
					<td width="70%" valign="top" class="line_top line_right line_bottom line_left">
						Keterangan :
					</td>
				</tr>
			</table>
			&nbsp;
			<br/>&nbsp;
			<br/>&nbsp;
			<table width="100%" border="0">
				<tr>
					<td width="50%" align="center">
						&nbsp;
						<br/>&nbsp;
						<br/>&nbsp;
						<br/>&nbsp;
						_________________________________________
						<br/>PT. DUA PUTRA PERKASA PRATAMA
					</td>
					<td width="50%" align="center">
						&nbsp;
						<br/>&nbsp;
						<br/>&nbsp;
						<br/>&nbsp;
						_________________________________________
						<br/>Pembeli
					</td>
				</tr>
			</table>
		</div>
	</body>
</html>