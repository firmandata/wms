<table class="form-table">
	<thead>
		<tr>
			<td colspan="2" class="form-table-title">Information</td>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th><label for="material_inventory_assembly_form_code">No</label></th>
			<td><?php echo $record->code;?></td>
		</tr>
		<tr>
			<th><label for="material_inventory_assembly_form_assembly_date">Date</label></th>
			<td><?php echo (!empty($record->assembly_date) ? date($this->config->item('server_display_date_format'), strtotime($record->assembly_date)) : '');?></td>
		</tr>
		<tr>
			<th><label for="material_inventory_assembly_form_notes">Notes</label></th>
			<td><?php echo nl2br((!empty($record) ? $record->notes : ''));?></td>
		</tr>
	</tbody>
</table>
<br/>
<strong>Inventory Source</strong><br/>
<table class="table-data ui-widget ui-widget-content ui-corner-all" width="100%" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<th class="ui-state-default ui-corner-tl" width="22px" rowspan="2">&nbsp;</th>
			<th class="ui-state-default" rowspan="2">Product</th>
			<th class="ui-state-default" rowspan="2">Grid</th>
			<th class="ui-state-default" rowspan="2">Project</th>
			<th class="ui-state-default" rowspan="2">Business Partner</th>
			<th class="ui-state-default" rowspan="2">Pallet</th>
			<th class="ui-state-default" rowspan="2">Barcode</th>
			<th class="ui-state-default" rowspan="2">Carton No</th>
			<th class="ui-state-default" rowspan="2">Lot No</th>
			<th class="ui-state-default" colspan="3">Volume</th>
			<th class="ui-state-default" rowspan="2">Box</th>
			<th class="ui-state-default" rowspan="2">Quantity</th>
		</tr>
		<tr>
			<th class="ui-state-default">Length</th>
			<th class="ui-state-default">Width</th>
			<th class="ui-state-default">Height</th>
		</tr>
	</thead>
	<tbody>
<?php
foreach ($m_inventory_assemblysources as $m_inventory_assemblysource_idx=>$m_inventory_assemblysource)
{?>
		<tr>
			<td class="ui-state-default" align="right"><?php echo $m_inventory_assemblysource_idx + 1;?></td>
			<td class="ui-widget-content">
				<?php echo $m_inventory_assemblysource->m_product_code;?> - <?php echo $m_inventory_assemblysource->m_product_name;?>
			</td>
			<td class="ui-widget-content" align="center"><?php echo $m_inventory_assemblysource->m_grid_code;?></td>
			<td class="ui-widget-content"><?php echo $m_inventory_assemblysource->c_project_name;?></td>
			<td class="ui-widget-content"><?php echo $m_inventory_assemblysource->c_businesspartner_name;?></td>
			<td class="ui-widget-content"><?php echo $m_inventory_assemblysource->pallet;?></td>
			<td class="ui-widget-content"><?php echo $m_inventory_assemblysource->barcode;?></td>
			<td class="ui-widget-content"><?php echo $m_inventory_assemblysource->carton_no;?></td>
			<td class="ui-widget-content" align="center"><?php echo $m_inventory_assemblysource->lot_no;?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format_clear($m_inventory_assemblysource->volume_length, 4);?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format_clear($m_inventory_assemblysource->volume_width, 4);?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format_clear($m_inventory_assemblysource->volume_height, 4);?></td>
			<td class="ui-widget-content" align="right">
				<?php
				$quantity_box = $m_inventory_assemblysource->quantity_box_from - $m_inventory_assemblysource->quantity_box_to;
				if ($quantity_box == 0)
					$quantity_box = $m_inventory_assemblysource->quantity_box_to;
				echo number_format_clear($quantity_box);?>
			</td>
			<td class="ui-widget-content" align="right"><?php echo number_format_clear($m_inventory_assemblysource->quantity_from - $m_inventory_assemblysource->quantity_to, 4);?></td>
		</tr>
<?php
}?>
	</tbody>
</table>
<br/>
<strong>Inventory Target</strong><br/>
<table class="table-data ui-widget ui-widget-content ui-corner-all" width="100%" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<th class="ui-state-default ui-corner-tl" width="22px" rowspan="2">&nbsp;</th>
			<th class="ui-state-default" rowspan="2">Product</th>
			<th class="ui-state-default" rowspan="2">Grid</th>
			<th class="ui-state-default" rowspan="2">Project</th>
			<th class="ui-state-default" rowspan="2">Business Partner</th>
			<th class="ui-state-default" rowspan="2">Pallet</th>
			<th class="ui-state-default" rowspan="2">Barcode</th>
			<th class="ui-state-default" rowspan="2">Carton No</th>
			<th class="ui-state-default" rowspan="2">Lot No</th>
			<th class="ui-state-default" colspan="3">Volume</th>
			<th class="ui-state-default" rowspan="2">Condition</th>
			<th class="ui-state-default" rowspan="2">Packed Date</th>
			<th class="ui-state-default" rowspan="2">Expired Date</th>
			<th class="ui-state-default" rowspan="2">Box</th>
			<th class="ui-state-default" rowspan="2">Quantity</th>
		</tr>
		<tr>
			<th class="ui-state-default">Length</th>
			<th class="ui-state-default">Width</th>
			<th class="ui-state-default">Height</th>
		</tr>
	</thead>
	<tbody>
<?php
foreach ($m_inventory_assemblytargets as $m_inventory_assemblytarget_idx=>$m_inventory_assemblytarget)
{?>
		<tr>
			<td class="ui-state-default" align="right"><?php echo $m_inventory_assemblytarget_idx + 1;?></td>
			<td class="ui-widget-content">
				<?php echo $m_inventory_assemblytarget->m_product_code;?> - <?php echo $m_inventory_assemblytarget->m_product_name;?>
			</td>
			<td class="ui-widget-content" align="center"><?php echo $m_inventory_assemblytarget->m_grid_code;?></td>
			<td class="ui-widget-content"><?php echo $m_inventory_assemblytarget->c_project_name;?></td>
			<td class="ui-widget-content"><?php echo $m_inventory_assemblytarget->c_businesspartner_name;?></td>
			<td class="ui-widget-content"><?php echo $m_inventory_assemblytarget->pallet;?></td>
			<td class="ui-widget-content"><?php echo $m_inventory_assemblytarget->barcode;?></td>
			<td class="ui-widget-content"><?php echo $m_inventory_assemblytarget->carton_no;?></td>
			<td class="ui-widget-content" align="center"><?php echo $m_inventory_assemblytarget->lot_no;?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format_clear($m_inventory_assemblytarget->volume_length, 4);?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format_clear($m_inventory_assemblytarget->volume_width, 4);?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format_clear($m_inventory_assemblytarget->volume_height, 4);?></td>
			<td class="ui-widget-content" align="center"><?php echo $m_inventory_assemblytarget->condition;?></td>
			<td class="ui-widget-content" align="center"><?php echo (!empty($m_inventory_assemblytarget->packed_date) ? date($this->config->item('server_display_date_format'), strtotime($m_inventory_assemblytarget->packed_date)) : '');?></td>
			<td class="ui-widget-content" align="center"><?php echo (!empty($m_inventory_assemblytarget->expired_date) ? date($this->config->item('server_display_date_format'), strtotime($m_inventory_assemblytarget->expired_date)) : '');?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format_clear($m_inventory_assemblytarget->quantity_box);?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format_clear($m_inventory_assemblytarget->quantity, 4);?></td>
		</tr>
<?php
}?>
	</tbody>
</table>

<script type="text/javascript">
jQuery(function(){
});
</script>