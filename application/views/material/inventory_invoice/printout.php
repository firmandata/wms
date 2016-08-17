<html>
	<head>
		<title>Invoice</title>
		<style>
			@page {
				margin-top: 143px;
				margin-left: 30px;
				margin-right: 30px;
				margin-bottom: 30px;
			}
			
			body, table td, table th {
				font-size: 12px;
			}
			
			h1 {
				font-size: 24px;
				text-align: center;
			}
			
			.table tbody td, thead th, tfoot td {
				padding-left: 3px;
				padding-top: 2px;
				padding-right: 3px;
				padding-bottom: 2px;
				font-size: 12px;
			}
			
			.table thead th, tfoot td {
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
$header_counter = 0;
foreach ($data as $header_idx=>$header)
{
	$parts_num_total = 0;
	$weight_total = 0;
	foreach ($header->m_inventory_invoicedetails as $m_inventory_invoicedetail_idx=>$m_inventory_invoicedetail)
	{
		$parts_num_total += $m_inventory_invoicedetail->parts_num;
		$weight_total += $m_inventory_invoicedetail->weight;
	}?>
		<h1 <?php echo $header_counter > 0 ? ' style="page-break-before:always;"' : '';?>>
			INVOICE
		</h1>
		<table class="table" width="100%" border="0" cellspacing="0">
			<tr>
				<td width="50%" class="line_top line_left line_right line_bottom" style="padding:10px;">
					<strong><?php echo $header->c_businesspartner_name;?></strong>
					<br/><?php echo nl2br($header->c_businesspartner_address);?>
					<br/>&nbsp;
					<br/>&nbsp;
					<br/>Attn. Finance/ Acccount Dept
				</td>
				<td>
					<table border="0" cellspacing="0">
						<tr>
							<td>Invoice No.</td>
							<td>:</td>
							<td><?php echo $header->code;?></td>
						</tr>
						<tr>
							<td>Date</td>
							<td>:</td>
							<td><?php echo (!empty($header->invoice_date) ? date($this->config->item('server_display_date_format'), strtotime($header->invoice_date)) : '');?></td>
						</tr>
						<tr>
							<td>J/O No.</td>
							<td>:</td>
							<td><?php echo $header->jo_no;?></td>
						</tr>
						<tr>
							<td>Terms Of Payment</td>
							<td>:</td>
							<td><?php echo $header->term_of_payment;?></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<br/>&nbsp;
		<table class="table" width="100%" border="0" cellspacing="0">
			<tr>
				<td class="line_left line_top" width="100px">Storage Detail</td>
				<td class="line_top" width="5px">&nbsp;</td>
				<td class="line_top line_bottom" colspan="6">&nbsp;</td>
				<td class="line_top line_right">&nbsp;</td>
			</tr>
			<tr>
				<td class="line_left">Plate No.</td>
				<td>:</td>
				<td><?php echo $header->plate_no;?></td>
				<td width="100px">Volume</td>
				<td width="5px">:</td>
				<td align="right" width="50px"><?php echo number_format_clear($parts_num_total, 4);?></td>
				<td>
<?php
	$unit_of_material = NULL;
	if ($header->invoice_calculate == 'BOX')
		$unit_of_material = "BOX";
	elseif ($header->invoice_calculate == 'PALLET')
		$unit_of_material = "PLT";
	elseif ($header->invoice_calculate == 'VOLUME')
		$unit_of_material = "CBM";
	elseif ($header->invoice_calculate == 'WEIGHT')
		$unit_of_material = "BOX";
	echo $unit_of_material;
?>
				</td>
				<td class="line_right" colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td class="line_left">SI/SJ/SO No.</td>
				<td>:</td>
				<td><?php echo $header->si_sj_so_no;?></td>
				<td>Weight</td>
				<td>:</td>
				<td align="right"><?php echo $weight_total > 0 ? number_format_clear($weight_total, 4) : '';?></td>
				<td>Kgs</td>
				<td class="line_right" colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td class="line_left">SPK/PO No.</td>
				<td>:</td>
				<td><?php echo $header->spk_po_no;?></td>
				<td>Contents</td>
				<td>:</td>
				<td colspan="3">
<?php
	$invoice_contents = array();
	if ($header->invoice_handling_in)
		$invoice_contents[] = "Handling In";
	if ($header->invoice_handling_out)
		$invoice_contents[] = "Handling Out";
	if ($header->invoice_handling_storage)
		$invoice_contents[] = "Storage";
	echo implode(", ", $invoice_contents);?>
				</td>
				<td class="line_right">&nbsp;</td>
			</tr>
			<tr>
				<td class="line_left">Aging</td>
				<td>:</td>
				<td colspan="6">
<?php
	$period_from_display = date('d M Y', strtotime($header->period_from));
	$period_to_display = date('d M Y', strtotime($header->period_to));?>
					<?php echo $period_from_display;?> - <?php echo $period_to_display;?>
				</td>
				<td class="line_right">&nbsp;</td>
			</tr>
			<tr>
				<td class="line_left">Charges Details</td>
				<td>&nbsp;</td>
				<td class="line_bottom" colspan="5">&nbsp;</td>
				<td class="line_bottom" >&nbsp;</td>
				<td class="line_right">&nbsp;</td>
			</tr>
		</table>
		<table class="table" width="100%" border="0" cellspacing="0">
			<thead>
				<tr>
					<th class="line_left">NO</th>
					<th>DESCRIPTION</th>
					<th class="line_right">AMOUNT</th>
				</tr>
			</thead>
			<tbody>
<?php
	$amount_total = 0;
	foreach ($header->m_inventory_invoicedetails as $m_inventory_invoicedetail_idx=>$m_inventory_invoicedetail)
	{?>
				<tr>
					<td class="line_left" align="center"><?php echo $m_inventory_invoicedetail_idx + 1;?></td>
					<td><?php echo $m_inventory_invoicedetail->description;?></td>
					<td class="line_right" align="right"><?php echo number_format_clear($m_inventory_invoicedetail->amount, 4);?></td>
				</tr>
<?php
		$amount_total += $m_inventory_invoicedetail->amount;
	}
	$tax_total = $amount_total * ($header->tax / 100);?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="3" class="line_left line_right line_bottom">
						Your Ref:*)
						<br/><?php echo nl2br($header->reference);?>
					</td>
				</tr>
			</tfoot>
		</table>
		<br/>&nbsp;
		<table width="100%" border="0" cellspacing="0">
			<tr>
				<td width="50%">
					Note :
					<br/>Please transfer to our A/C (Full Amount) or Cash Only :
					<br/>
					<table border="0" cellspacing="0">
						<tr>
							<td>&gt;</td>
							<td>Beneficiary Name</td>
							<td>:</td>
							<td><?php echo $header->bank_ac_name;?></td>
						</tr>
						<tr>
							<td>&gt;</td>
							<td>A/C No.</td>
							<td>:</td>
							<td><?php echo $header->bank_ac_no;?></td>
						</tr>
						<tr>
							<td>&gt;</td>
							<td>Bank</td>
							<td>:</td>
							<td><?php echo $header->bank_name;?></td>
						</tr>
						<tr>
							<td>&gt;</td>
							<td>Branch</td>
							<td>:</td>
							<td><?php echo $header->bank_branch;?></td>
						</tr>
						<tr>
							<td>&gt;</td>
							<td>Swift Code</td>
							<td>:</td>
							<td><?php echo $header->bank_swift_code;?></td>
						</tr>
					</table>
				</td>
				<td valign="top">
					<table class="table" width="100%" border="0" cellspacing="0">
						<tr>
							<td class="line_top line_left">Total Amount</td>
							<td class="line_top" align="right">Rp</td>
							<td class="line_top line_right" align="right"><?php echo number_format_clear($amount_total, 4);?></td>
						</tr>
						<tr>
							<td class="line_left">PPN/VAT <?php echo number_format_clear($header->tax, 2);?>%</td>
							<td align="right">Rp</td>
							<td class="line_right" align="right"><?php echo number_format_clear($tax_total, 4);?></td>
						</tr>
						<tr>
							<td class="line_bottom line_left">Grand Total</td>
							<td class="line_bottom" align="right">Rp</td>
							<td class="line_bottom line_right" align="right"><?php echo number_format_clear($amount_total + $tax_total, 4);?></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<br/>&nbsp;
		<table width="100%" border="0" cellspacing="0">
			<tr>
				<td width="50%">&nbsp;</td>
				<td align="center">
					PT. MULTI ANGKUTAN EKSPRESS
					<br/>&nbsp;
					<br/>&nbsp;
					<br/>&nbsp;
					<br/>&nbsp;
					<br/>&nbsp;
					<br/>&nbsp;
					<br/>&nbsp;
					<br/>Authorized Signature
				</td>
			</tr>
		</table>
<?php
	$header_counter++;
}?>
	</body>
</html>