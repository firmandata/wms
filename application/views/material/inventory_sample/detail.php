<table>
	<tr><td valign="top">
			<table class="form-table">
				<tbody>
					<tr>
						<th width="120">No</th>
						<td><?php echo (!empty($record) ? $record->code : '');?></td>
					</tr>
					<tr>
						<th>Date</th>
						<td><?php echo (!empty($record->sampling_date) ? date($this->config->item('server_display_date_format'), strtotime($record->sampling_date)) : '');?></td>
					</tr>
					<tr>
						<th>Notes</th>
						<td><?php echo (!empty($record) ? nl2br($record->notes) : '');?></td>
					</tr>
				</tbody>
			</table>
		</td>
		<td valign="top">
			<table class="form-table">
				<tbody>
					<tr>
						<th width="120">Supervisor</th>
						<td><?php echo (!empty($record) ? $record->supervisor : '');?></td>
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
			<th class="ui-state-default ui-corner-tl" width="22px">&nbsp;</th>
			<th class="ui-state-default">Location</th>
			<th class="ui-state-default">DOC</th>
			<th class="ui-state-default">ADG(gr)</th>
			<th class="ui-state-default">BIOMASS(kg)</th>
			<th class="ui-state-default">SR(%)</th>
			<th class="ui-state-default">FCR</th>
			<th class="ui-state-default">ABW(gr)</th>
			<th class="ui-state-default">F/D(kg)</th>
			<th class="ui-state-default">Population</th>
			<th class="ui-state-default">FR(%)</th>
			<th class="ui-state-default">Notes</th>
		</tr>
	</thead>
	<tbody>
<?php
foreach ($m_inventory_sampledetails as $m_inventory_sampledetail_idx=>$m_inventory_sampledetail)
{?>
		<tr>
			<td class="ui-state-default" align="right"><?php echo $m_inventory_sampledetail_idx + 1;?></td>
			<td class="ui-widget-content"><?php echo $m_inventory_sampledetail->m_grid_code;?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format_clear($m_inventory_sampledetail->doc);?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format_clear($m_inventory_sampledetail->adg, 4);?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format_clear($m_inventory_sampledetail->biomass, 4);?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format_clear($m_inventory_sampledetail->sr, 2);?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format_clear($m_inventory_sampledetail->fcr, 4);?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format_clear($m_inventory_sampledetail->abw, 4);?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format_clear($m_inventory_sampledetail->fd, 4);?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format_clear($m_inventory_sampledetail->population, 4);?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format_clear($m_inventory_sampledetail->fr, 2);?></td>
			<td class="ui-widget-content"><?php echo $m_inventory_sampledetail->notes;?></td>
		</tr>
<?php
}?>
	</tbody>
</table>

<script type="text/javascript">
jQuery(function(){
});
</script>