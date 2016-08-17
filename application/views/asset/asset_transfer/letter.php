<?php
$this->load->helper('date');?>
<html>
	<head>
		<title>Berita Acara Serah Terima</title>
		<style>
			@page {
				margin: 50px;
			}
			
			body, table td {
				font-size: 12px;
			}
			
			h2 {
				text-align: center;
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
foreach ($details as $detail_idx=>$detail)
{?>
		<div <?php echo $detail_idx > 0 ? 'style="page-break-before: always;"' : '';?>>
			<table border="0" width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td class="line_bottom" width="55px" height="55px" align="left" valign="top">
						<img src="<?php echo base_url('img/logo.png');?>" width="51px" height="51px"/>
					</td>
					<td class="line_bottom" valign="top">
						<strong><font size="4">PT. FAJAR MAS MURNI</font></strong>
						<br/><?php echo $detail->c_region_address;?>
						<br/><?php echo (!empty($detail->c_region_phone_no) ? 'Tel. '.$detail->c_region_phone_no : '');?><?php echo (!empty($detail->c_region_fax_no) ? ' Fax. '.$detail->c_region_fax_no : '');?>
					</td>
				</tr>
			</table>
			<h2>BERITA ACARA SERAH TERIMA ASET</h2>
			<p>Pada hari ini <?php echo str_replace(array('{day_number}', '{day}', '{month}', '{year}'), date_to_spelling_bahasa($header->date), "{day_number} tanggal {day} bulan {month} tahun {year}");?>, bertempat di <?php echo $detail->c_region_name;?> PT. FAJAR MAS MURNI (FMM) <?php echo $detail->c_region_address;?>. telah dilaksanakan Serah terima Barang/Asset perusahaan :</p>
			<table border="0" width="100%">
				<tr>
					<td width="20px" rowspan="5" valign="top">1.</td>
					<td width="100px">Nama</td>
					<td width="10px">:</td>
					<td><?php echo $detail->c_businesspartner_userfrom_name;?></td>
				</tr>
				<tr>
					<td>NIK</td>
					<td>:</td>
					<td><?php echo $detail->c_businesspartner_userfrom_code;?></td>
				</tr>
				<tr>
					<td>Jabatan</td>
					<td>:</td>
					<td><?php echo $detail->c_businesspartner_userfrom_personal_position;?></td>
				</tr>
				<tr>
					<td>Department</td>
					<td>:</td>
					<td><?php echo $detail->c_departmentfrom_name;?></td>
				</tr>
				<tr>
					<td colspan="3">Selanjutnya disebut PIHAK PERTAMA (sebagai pihak yang menyerahkan)</td>
				</tr>
			</table>
			<table border="0" width="100%">
				<tr>
					<td width="20px" rowspan="5" valign="top">2.</td>
					<td width="100px">Nama</td>
					<td width="10px">:</td>
					<td><?php echo $detail->c_businesspartner_userto_name;?></td>
				</tr>
				<tr>
					<td>NIK</td>
					<td>:</td>
					<td><?php echo $detail->c_businesspartner_userto_code;?></td>
				</tr>
				<tr>
					<td>Jabatan</td>
					<td>:</td>
					<td><?php echo $detail->c_businesspartner_userto_personal_position;?></td>
				</tr>
				<tr>
					<td>Department</td>
					<td>:</td>
					<td><?php echo $detail->c_departmentto_name;?></td>
				</tr>
				<tr>
					<td colspan="3">Selanjutnya disebut PIHAK KEDUA (sebagai pihak yang menerima)</td>
				</tr>
			</table>
			<p>Pihak Pertama menyerahkan kepada Pihak Kedua berupa <?php echo $detail->a_asset_name;?>, golongan <?php echo $detail->m_product_name;?>  dengan nomor asset <?php echo $detail->a_asset_code;?>.</p>
			<p>Demikian Berita Acara Serah Terima ini dibuat untuk dipergunakan sebagaimana mestinya.</p>
			<table border="0" width="100%">
				<tr>
					<td>&nbsp;</td>
					<td align="center">
						<?php echo $detail->c_region_address_city;?>, <?php echo date($this->config->item('server_display_date_format'), strtotime($header->date));?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="top">
						Yang menerima
						<br/>PIHAK KEDUA,
						<br/>&nbsp;
						<br/>&nbsp;
						<br/>&nbsp;
						<br/>
						<u>
<?php
if (!empty($detail->c_businesspartner_userto_name))
{?>
						<?php echo $detail->c_businesspartner_userto_name;?>
<?php
}
else
{?>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<?php
}?>
						</u>
						<br/><?php echo $detail->c_businesspartner_userto_code;?>
					</td>
					<td align="center" valign="top">
						Yang menyerahkan
						<br/>PIHAK PERTAMA,
						<br/>&nbsp;
						<br/>&nbsp;
						<br/>&nbsp;
						<br/>
						<u>
<?php
if (!empty($detail->c_businesspartner_userfrom_name))
{?>
							<?php echo $detail->c_businesspartner_userfrom_name;?>
<?php
}
else
{?>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<?php
}?>
						</u>
						<br/><?php echo $detail->c_businesspartner_userfrom_code;?>
					</td>
				</tr>
			</table>
		</div>
<?php
}?>
	</body>
</html>