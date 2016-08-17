<html>
	<head>
		<title>SPK (Surat Perintah Kerja)</title>
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
					<td width="25%" valign="top" align="center" class="line_right line_top line_bottom">
						<strong>
							SURAT PERINTAH KERJA
							<br/>(Pemberian Pakan/Vitamin/Obat)
						</strong>
					</td>
					<td width="25%" valign="top" rowspan="2" class="line_right line_top line_bottom">
						Kepada:
						<br/>Kepala Gudang
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
						Tanggal : <?php echo (!empty($record->orderout_date) ? date($this->config->item('server_display_date_format'), strtotime($record->orderout_date)) : '');?>
					</td>
					<td class="line_right line_bottom">
						Supervisor : <?php echo $record->approval_by;?>
					</td>
				</tr>
			</table>
			
			<br/>
			<table class="table" width="100%" border="0" cellspacing="0">
				<thead>
					<tr>
						<th class="line_right line_top line_bottom line_left">No</th>
						<th class="line_right line_top line_bottom">Code</th>
						<th class="line_right line_top line_bottom">Description</th>
						<th class="line_right line_top line_bottom">Quantity</th>
						<th class="line_right line_top line_bottom">UOM</th>
						<th class="line_right line_top line_bottom">Note</th>
					</tr>
				</thead>
				<tbody>
<?php
	$quantity_total = 0;
	foreach ($c_orderoutdetails as $c_orderoutdetail_idx=>$c_orderoutdetail)
	{?>
					<tr>
						<td class="line_right line_bottom line_left" align="right"><?php echo $c_orderoutdetail_idx + 1;?></td>
						<td class="line_right line_bottom"><?php echo $c_orderoutdetail->m_product_code;?></td>
						<td class="line_right line_bottom"><?php echo $c_orderoutdetail->m_product_name;?></td>
						<td class="line_right line_bottom" align="right"><?php echo number_format_clear($c_orderoutdetail->quantity, 4);?></td>
						<td class="line_right line_bottom"><?php echo $c_orderoutdetail->m_product_uom;?></td>
						<td class="line_right line_bottom"><?php echo $c_orderoutdetail->notes;?></td>
					</tr>
<?php
		$quantity_total += $c_orderoutdetail->quantity;
	}?>
				</tbody>
				<tfoot>
					<tr>
						<td class="line_right line_bottom line_left" colspan="3" align="center" style="font-size: 18px;"><strong>Quantity Total</strong></td>
						<td class="line_right line_bottom" align="center" style="font-size: 18px;"><strong><?php echo number_format_clear($quantity_total, 4);?></strong></td>
						<td class="line_right line_bottom" colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td class="line_right line_bottom line_left" colspan="2" align="center">Disetujui Oleh</td>
						<td class="line_right line_bottom" colspan="3" align="center">Diketahui Oleh</td>
						<td class="line_right line_bottom" align="center">Diterima Oleh</td>
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
					<tr>
						<td class="line_right line_bottom line_left" colspan="2" align="center">&nbsp;</td>
						<td class="line_right line_bottom" align="center">&nbsp;</td>
						<td class="line_right line_bottom" colspan="2" align="center">&nbsp;</td>
						<td class="line_right line_bottom" align="center">&nbsp;</td>
					</tr>
					<tr>
						<td class="line_right line_bottom line_left" colspan="2" align="center" width="25%">
							<strong>
								(Manager Operasional)
							<strong>
						</td>
						<td class="line_right line_bottom" align="center" width="25%">
							<strong>
								(Manager Budidaya)
							<strong>
						</td>
						<td class="line_right line_bottom" colspan="2" align="center" width="25%">
							<strong>
								(WH Supervisor)
							<strong>
						</td>
						<td class="line_right line_bottom" align="center" width="25%">
							<strong>
								(Supervisor)
							<strong>
						</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</body>
</html>