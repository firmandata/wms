<table>
	<tr><td valign="top">
			<table class="form-table">
				<tbody>
					<tr>
						<th width="120">No</th>
						<td><?php echo (!empty($record) ? $record->code : '');?></td>
					</tr>
					<tr>
						<th>Business Partner</th>
						<td><?php echo (!empty($record) ? $record->c_businesspartner_text : '');?></td>
					</tr>
					<tr>
						<th>Date</th>
						<td><?php echo (!empty($record->orderout_date) ? date($this->config->item('server_display_date_format'), strtotime($record->orderout_date)) : '');?></td>
					</tr>
					<tr>
						<th>Notes</th>
						<td><?php echo (!empty($record) ? nl2br($record->notes) : '');?></td>
					</tr>
					<tr>
						<th>Pick List Status</th>
						<td><?php echo (!empty($record) ? $record->status_inventory_picklist : '');?></td>
					</tr>
				</tbody>
			</table>
		</td>
		<td valign="top">
			<table class="form-table">
				<tbody>
					<tr>
						<th width="120">Project</th>
						<td><?php echo (!empty($record) ? $record->c_project_text : '');?></td>
					</tr>
					<tr>
						<th>External No</th>
						<td><?php echo (!empty($record) ? $record->external_no : '');?></td>
					</tr>
					<tr>
						<th>Approval By</th>
						<td><?php echo (!empty($record) ? $record->approval_by : '');?></td>
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
			<th class="ui-state-default" rowspan="2">Type</th>
			<th class="ui-state-default" rowspan="2">Casing</th>
			<th class="ui-state-default" colspan="3">Quantity</th>
			<th class="ui-state-default" rowspan="2">UOM</th>
			<th class="ui-state-default" rowspan="2">Pick List Status</th>
			<th class="ui-state-default" rowspan="2">Notes</th>
		</tr>
		<tr>
			<th class="ui-state-default">Pick</th>
			<th class="ui-state-default">Free</th>
			<th class="ui-state-default">Total</th>
		</tr>
	</thead>
	<tbody>
<?php
foreach ($c_orderoutdetails as $c_orderoutdetail_idx=>$c_orderoutdetail)
{
	$color = NULL;
	if ($c_orderoutdetail->status_inventory_picklist == 'NO PICK LIST')
		$color = 'red';
	elseif ($c_orderoutdetail->status_inventory_picklist == 'COMPLETE')
		$color = 'green';
	elseif ($c_orderoutdetail->status_inventory_picklist == 'INCOMPLETE')
		$color = 'orange';
	else
		$color = 'yellow';?>
		<tr>
			<td class="ui-state-default" align="right"><?php echo $c_orderoutdetail_idx + 1;?></td>
			<td class="ui-widget-content"><?php echo $c_orderoutdetail->m_product_code;?></td>
			<td class="ui-widget-content"><?php echo $c_orderoutdetail->m_product_name;?></td>
			<td class="ui-widget-content"><?php echo $c_orderoutdetail->m_product_type;?></td>
			<td class="ui-widget-content"><?php echo $c_orderoutdetail->m_product_casing;?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format_clear($c_orderoutdetail->quantity_used, 4);?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format_clear($c_orderoutdetail->quantity - $c_orderoutdetail->quantity_used, 4);?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format_clear($c_orderoutdetail->quantity, 4);?></td>
			<td class="ui-widget-content"><?php echo $c_orderoutdetail->m_product_uom;?></td>
			<td style="background-color:<?php echo $color;?>; font-weight:bold;" align="center"><?php echo $c_orderoutdetail->status_inventory_picklist;?></td>
			<td class="ui-widget-content"><?php echo $c_orderoutdetail->notes;?></td>
		</tr>
<?php
}?>
	</tbody>
</table>

<script type="text/javascript">
jQuery(function(){
});
</script>