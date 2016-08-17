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
						<td><?php echo (!empty($record->water_date) ? date($this->config->item('server_display_date_format'), strtotime($record->water_date)) : '');?></td>
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
			<th class="ui-state-default">Suhu(<sup>o</sup>C)</th>
			<th class="ui-state-default">DO (Disolved Oksigen)</th>
			<th class="ui-state-default">pH</th>
			<th class="ui-state-default">Salinitas</th>
			<th class="ui-state-default">Kecerahan</th>
			<th class="ui-state-default">Total Ammonia</th>
			<th class="ui-state-default">Total Nitrite</th>
			<th class="ui-state-default">Total Nitrate</th>
			<th class="ui-state-default">Keterangan</th>
		</tr>
	</thead>
	<tbody>
<?php
foreach ($m_inventory_waterdetails as $m_inventory_waterdetail_idx=>$m_inventory_waterdetail)
{?>
		<tr>
			<td class="ui-state-default" align="right"><?php echo $m_inventory_waterdetail_idx + 1;?></td>
			<td class="ui-widget-content"><?php echo $m_inventory_waterdetail->m_grid_code;?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format_clear($m_inventory_waterdetail->doc);?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format_clear($m_inventory_waterdetail->suhu, 4);?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format_clear($m_inventory_waterdetail->disolved_oksigen, 4);?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format_clear($m_inventory_waterdetail->ph, 2);?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format_clear($m_inventory_waterdetail->salinitas, 4);?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format_clear($m_inventory_waterdetail->kecerahan, 4);?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format_clear($m_inventory_waterdetail->total_ammonia, 4);?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format_clear($m_inventory_waterdetail->total_nitrite, 4);?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format_clear($m_inventory_waterdetail->total_nitrate, 2);?></td>
			<td class="ui-widget-content"><?php echo $m_inventory_waterdetail->notes;?></td>
		</tr>
<?php
}?>
	</tbody>
</table>

<script type="text/javascript">
jQuery(function(){
});
</script>