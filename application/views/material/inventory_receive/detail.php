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
						<th width="120">No</th>
						<td><?php echo (!empty($record) ? $record->code : '');?></td>
					</tr>
					<tr>
						<th>Date</th>
						<td><?php echo (!empty($record->receive_date) ? date($this->config->item('server_display_date_format'), strtotime($record->receive_date)) : '');?></td>
					</tr>
					<tr>
						<th>Notes</th>
						<td><?php echo (!empty($record) ? nl2br($record->notes) : '');?></td>
					</tr>
					<tr>
						<th>Inbound Status</th>
						<td><?php echo (!empty($record) ? $record->status_inventory_inbound : '');?></td>
					</tr>
				</tbody>
			</table>
		</td>
		<td valign="top">
			<table class="form-table">
				<thead>
					<tr>
						<td colspan="2" class="form-table-title">Mis Information</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>Vehicle No</th>
						<td><?php echo (!empty($record) ? $record->vehicle_no : '');?></td>
					</tr>
					<tr>
						<th>Driver</th>
						<td><?php echo (!empty($record) ? $record->vehicle_driver : '');?></td>
					</tr>
					<tr>
						<th>Transport Mode</th>
						<td><?php echo (!empty($record) ? $record->transport_mode : '');?></td>
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
			<th class="ui-state-default" rowspan="2">Order In</th>
			<th class="ui-state-default" rowspan="2">Order In Date</th>
			<th class="ui-state-default" rowspan="2">Business Partner</th>
			<th class="ui-state-default" rowspan="2">Project</th>
			<th class="ui-state-default" rowspan="2">Product Code</th>
			<th class="ui-state-default" rowspan="2">Product Name</th>
			<th class="ui-state-default" colspan="3">Qty Case</th>
			<th class="ui-state-default" rowspan="2">Netto / Pack</th>
			<th class="ui-state-default" colspan="3">Qty Total</th>
			<th class="ui-state-default" rowspan="2">UOM</th>
			<th class="ui-state-default" rowspan="2">Inbound Status</th>
			<th class="ui-state-default" rowspan="2">Condition</th>
			<th class="ui-state-default" rowspan="2">Location</th>
			<th class="ui-state-default" rowspan="2">Supervisor</th>
			<th class="ui-state-default" rowspan="2">Notes</th>
		</tr>
		<tr>
			<th class="ui-state-default">Inbound</th>
			<th class="ui-state-default">Free</th>
			<th class="ui-state-default">Quantity</th>
			<th class="ui-state-default">Inbound</th>
			<th class="ui-state-default">Free</th>
			<th class="ui-state-default">Quantity</th>
		</tr>
	</thead>
	<tbody>
<?php
foreach ($m_inventory_receivedetails as $m_inventory_receivedetail_idx=>$m_inventory_receivedetail)
{
	$color = NULL;
	if ($m_inventory_receivedetail->status_inventory_inbound == 'NO INBOUND')
		$color = 'red';
	elseif ($m_inventory_receivedetail->status_inventory_inbound == 'COMPLETE')
		$color = 'green';
	elseif ($m_inventory_receivedetail->status_inventory_inbound == 'INCOMPLETE')
		$color = 'orange';
	else
		$color = 'yellow';?>
		<tr>
			<td class="ui-state-default" align="right"><?php echo $m_inventory_receivedetail_idx + 1;?></td>
			<td class="ui-widget-content"><?php echo $m_inventory_receivedetail->c_orderin_code;?></td>
			<td class="ui-widget-content" align="center"><?php echo (!empty($m_inventory_receivedetail->c_orderin_date) ? date($this->config->item('server_display_date_format'), strtotime($m_inventory_receivedetail->c_orderin_date)) : '');?></td>
			<td class="ui-widget-content"><?php echo $m_inventory_receivedetail->c_businesspartner_name;?></td>
			<td class="ui-widget-content"><?php echo $m_inventory_receivedetail->c_project_name;?></td>
			<td class="ui-widget-content"><?php echo $m_inventory_receivedetail->m_product_code;?></td>
			<td class="ui-widget-content"><?php echo $m_inventory_receivedetail->m_product_name;?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format_clear($m_inventory_receivedetail->quantity_box_used);?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format_clear($m_inventory_receivedetail->quantity_box - $m_inventory_receivedetail->quantity_box_used);?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format_clear($m_inventory_receivedetail->quantity_box);?></td>
			<td class="ui-widget-content"><?php echo $m_inventory_receivedetail->m_product_pack;?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format_clear($m_inventory_receivedetail->quantity_used, 4);?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format_clear($m_inventory_receivedetail->quantity - $m_inventory_receivedetail->quantity_used, 4);?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format_clear($m_inventory_receivedetail->quantity, 4);?></td>
			<td class="ui-widget-content"><?php echo $m_inventory_receivedetail->m_product_uom;?></td>
			<td style="background-color:<?php echo $color;?>; font-weight:bold;" align="center"><?php echo $m_inventory_receivedetail->status_inventory_inbound;?></td>
			<td class="ui-widget-content"><?php echo $m_inventory_receivedetail->condition;?></td>
			<td class="ui-widget-content"><?php echo $m_inventory_receivedetail->m_grid_code;?></td>
			<td class="ui-widget-content"><?php echo $m_inventory_receivedetail->supervisor;?></td>
			<td class="ui-widget-content"><?php echo $m_inventory_receivedetail->notes;?></td>
		</tr>
<?php
}?>
	</tbody>
</table>

<script type="text/javascript">
jQuery(function(){
});
</script>