<table>
	<tr><td valign="top">
			<table class="form-table">
				<thead>
					<tr>
						<td colspan="2" class="form-table-title">Information</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>No</th>
						<td><?php echo (!empty($record) ? $record->code : '');?></td>
					</tr>
					<tr>
						<th>Business Partner</th>
						<td><?php echo (!empty($record) ? $record->c_businesspartner_text : '');?></td>
					</tr>
					<tr>
						<th>Date</th>
						<td><?php echo (!empty($record->orderin_date) ? date($this->config->item('server_display_date_format'), strtotime($record->orderin_date)) : '');?></td>
					</tr>
					<tr>
						<th>Notes</th>
						<td><?php echo (!empty($record) ? nl2br($record->notes) : '');?></td>
					</tr>
					<tr>
						<th>Receive Status</th>
						<td><?php echo (!empty($record) ? $record->status_inventory_receive : '');?></td>
					</tr>
				</tbody>
			</table>
		</td>
		<td valign="top">
			<table class="form-table">
				<thead>
					<tr>
						<td colspan="2" class="form-table-title">Misc. Information</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>Project</th>
						<td><?php echo (!empty($record) ? $record->c_project_text : '');?></td>
					</tr>
					<tr>
						<th>Origin</th>
						<td><?php echo (!empty($record) ? $record->origin : '');?></td>
					</tr>
					<tr>
						<th>Bill of Load</th>
						<td><?php echo (!empty($record) ? $record->bol_no : '');?></td>
					</tr>
					<tr>
						<th>External No</th>
						<td><?php echo (!empty($record) ? $record->external_no : '');?></td>
					</tr>
				</tbody>
			</table>
		</td>
	</tr>
</table>
<br/>
<table class="table-data ui-widget ui-widget-content ui-corner-all" width="100%" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<th class="ui-state-default ui-corner-tl" width="22px" rowspan="2">&nbsp;</th>
			<th class="ui-state-default" rowspan="2">Product Code</th>
			<th class="ui-state-default" rowspan="2">Product Name</th>
			<th class="ui-state-default" colspan="3">Box</th>
			<th class="ui-state-default" rowspan="2">UOM</th>
			<th class="ui-state-default" colspan="3">Quantity</th>
			<th class="ui-state-default" rowspan="2">Receive Status</th>
			<th class="ui-state-default" rowspan="2">Notes</th>
		</tr>
		<tr>
			<th class="ui-state-default">Receive</th>
			<th class="ui-state-default">Free</th>
			<th class="ui-state-default">Total</th>
			<th class="ui-state-default">Receive</th>
			<th class="ui-state-default">Free</th>
			<th class="ui-state-default">Total</th>
		</tr>
	</thead>
	<tbody>
<?php
foreach ($c_orderindetails as $c_orderindetail_idx=>$c_orderindetail)
{
	$color = NULL;
	if ($c_orderindetail->status_inventory_receive == 'NO RECEIVE')
		$color = 'red';
	elseif ($c_orderindetail->status_inventory_receive == 'COMPLETE')
		$color = 'green';
	elseif ($c_orderindetail->status_inventory_receive == 'INCOMPLETE')
		$color = 'orange';
	else
		$color = 'yellow';?>
		<tr>
			<td class="ui-state-default" align="right"><?php echo $c_orderindetail_idx + 1;?></td>
			<td class="ui-widget-content"><?php echo $c_orderindetail->m_product_code;?></td>
			<td class="ui-widget-content"><?php echo $c_orderindetail->m_product_name;?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format_clear($c_orderindetail->quantity_box_used);?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format_clear($c_orderindetail->quantity_box - $c_orderindetail->quantity_box_used);?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format_clear($c_orderindetail->quantity_box);?></td>
			<td class="ui-widget-content"><?php echo $c_orderindetail->m_product_uom;?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format_clear($c_orderindetail->quantity_used, 4);?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format_clear($c_orderindetail->quantity - $c_orderindetail->quantity_used, 4);?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format_clear($c_orderindetail->quantity, 4);?></td>
			<td align="center" style="background-color:<?php echo $color;?>; font-weight:bold;"><?php echo $c_orderindetail->status_inventory_receive;?></td>
			<td class="ui-widget-content"><?php echo $c_orderindetail->notes;?></td>
		</tr>
<?php
}?>
	</tbody>
</table>

<script type="text/javascript">
jQuery(function(){
});
</script>