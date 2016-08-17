<html>
	<head>
		<title>Forecast Document</title>
		<style>
			@page {
				margin: 20px;
			}
			
			body {
				font-size: 10px
			}
			
			h1 {
				background-color: #CFCFCF;
				text-align: center;
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
		<h1>Received-Forecast</h1>
		<table width="100%" border="0">
			<tr>
				<td width="25%">No Paperwork</td><td width="10px">:</td>
				<td><?php echo $header->code;?></td>
				<td width="25%">No Received</td><td width="10px">:</td>
				<td><?php echo $header->m_inventory_receive_code;?></td>
			</tr>
			<tr>
				<td>Supplier Name</td><td>:</td>
				<td><?php echo $header->c_businesspartner_name;?></td>
				<td>No PO</td><td>:</td>
				<td><?php echo $header->c_orderin_code;?></td>
			</tr>
			<tr>
				<td>Notes</td><td>:</td>
				<td colspan="4"><?php echo nl2br($header->m_inventory_receive_notes);?></td>
			</tr>
			<tr>
				<td>Tanggal</td><td>:</td>
				<td colspan="4"><?php echo (!empty($header->m_inventory_receive_receive_date) ? date($this->config->item('server_display_date_format'), strtotime($header->m_inventory_receive_receive_date)) : '');?></td>
			</tr>
		</table>
		<br/>
		
		<table class="table" width="100%" border="0" cellspacing="0">
			<thead>
				<tr>
					<th class="line_right line_top line_bottom line_left">Line</th>
					<th class="line_right line_top line_bottom">Item Code</th>
					<th class="line_right line_top line_bottom">Item Name</th>
					<th class="line_right line_top line_bottom">UOM</th>
					<th class="line_right line_top line_bottom">Pack</th>
					<th class="line_right line_top line_bottom">QTY (CARTON)</th>
					<th class="line_right line_top line_bottom">QTY (KG)</th>
					<th class="line_right line_top line_bottom">Barcode</th>
				</tr>
			</thead>
			<tbody>
<?php
foreach ($summaries as $summary_idx=>$summary)
{?>
				<tr>
					<td class="line_right line_bottom line_left"><?php echo $summary_idx + 1;?></td>
					<td class="line_right line_bottom"><?php echo $summary->m_product_code;?></td>
					<td class="line_right line_bottom"><?php echo $summary->m_product_name;?></td>
					<td class="line_right line_bottom"><?php echo $summary->m_product_uom;?></td>
					<td class="line_right line_bottom"><?php echo $summary->m_product_pack;?></td>
					<td class="line_right line_bottom" align="right"><?php echo number_format_clear($summary->quantity_box);?></td>
					<td class="line_right line_bottom" align="right"><?php echo number_format_clear($summary->quantity, 4);?></td>
					<td class="line_right line_bottom"><?php echo $summary->m_product_code;?></td>
				</tr>
<?php
}?>
			</tbody>
		</table>
		<br/>
		
		<h2>Detail-Forecast</h2>
		<table class="table" width="100%" border="0" cellspacing="0">
			<thead>
				<tr>
					<th class="line_right line_top line_bottom line_left" rowspan="2">No</th>
					<th class="line_right line_top line_bottom" rowspan="2">Location</th>
					<th class="line_right line_top line_bottom" rowspan="2">PID</th>
					<th class="line_right line_top line_bottom" rowspan="2">Item</th>
					<th class="line_right line_top line_bottom" rowspan="2">Lot</th>
					<th class="line_right line_top line_bottom" rowspan="2">Description</th>
					<th class="line_right line_top line_bottom" colspan="2">In</th>
					<th class="line_right line_top line_bottom" colspan="2">Realisasi</th>
					<th class="line_right line_top line_bottom" rowspan="2">Packed Date</th>
					<th class="line_right line_top line_bottom" rowspan="2">Expired Date</th>
				</tr>
				<tr>
					<th class="line_right line_bottom">Carton</th>
					<th class="line_right line_bottom">KG</th>
					<th class="line_right line_bottom">Carton</th>
					<th class="line_right line_bottom">KG</th>
				</tr>
			</thead>
			<tbody>
<?php
foreach ($details as $detail_idx=>$detail)
{?>
				<tr>
					<td class="line_right line_bottom line_left" align="right"><?php echo $detail_idx + 1;?></td>
					<td class="line_right line_bottom"><?php echo $detail->m_grid_code;?></td>
					<td class="line_right line_bottom">&nbsp;</td>
					<td class="line_right line_bottom"><?php echo $detail->m_product_code;?></td>
					<td class="line_right line_bottom">&nbsp;</td>
					<td class="line_right line_bottom"><?php echo $detail->m_product_name;?></td>
					<td class="line_right line_bottom" align="right"><?php echo number_format_clear($detail->quantity, 4);?></td>
					<td class="line_right line_bottom">&nbsp;</td>
					<td class="line_right line_bottom">&nbsp;</td>
					<td class="line_right line_bottom">&nbsp;</td>
					<td class="line_right line_bottom">&nbsp;</td>
					<td class="line_right line_bottom">&nbsp;</td>
				</tr>
<?php
}?>
			</tbody>
		</table>
		<br/>
		
		<table class="table" width="100%" border="0" cellspacing="0">
			<tr>
				<td width="20%" class="line_right line_top line_bottom line_left">
					&nbsp;
					<br/>&nbsp;
					<br/>&nbsp;
					<br/>&nbsp;
				</td>
				<td width="20%" class="line_right line_top line_bottom">&nbsp;</td>
				<td width="20%" class="line_right line_top line_bottom">&nbsp;</td>
				<td width="20%" class="line_right line_top line_bottom">&nbsp;</td>
				<td width="20%" class="line_right line_top line_bottom">&nbsp;</td>
			</tr>
			<tr>
				<td class="line_right line_bottom line_left" align="center">Admin Operasional</td>
				<td class="line_right line_bottom" align="center">Customer</td>
				<td class="line_right line_bottom" align="center">Supervisor</td>
				<td class="line_right line_bottom" align="center">Juru Tally</td>
				<td class="line_right line_bottom" align="center">Warehouse</td>
			</tr>
		</table>
		<br/>
		
		<table class="table" width="100%" border="0" cellspacing="0">
			<tr>
				<td class="line_right line_top line_bottom line_left">
					Catatan
					<br/>&nbsp;
					<br/>&nbsp;
					<br/>&nbsp;
				</td>
			</tr>
		</table>
		<br/>
		
		<table border="0">
			<tr>
				<td>Jam In</td>
				<td>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td>WIB</td>
			</tr>
			<tr>
				<td>Jam Out</td>
				<td>:</td>
				<td>WIB</td>
			</tr>
		</table>
	</body>
</html>