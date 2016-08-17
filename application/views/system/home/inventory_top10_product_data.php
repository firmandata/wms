<table class="table-data ui-widget ui-widget-content ui-corner-all" width="100%" cellspacing="0">
	<thead>
		<tr><th class="ui-state-default ui-corner-tl" width="80px">Ranking</th>
			<th class="ui-state-default ui-corner-tr">Product Fast Moving</th>
		</tr>
	</thead>
	<tbody>
<?php
foreach ($records as $record_idx=>$record)
{?>
		<tr><td class="ui-state-default" align="center"><?php echo $record_idx + 1;?></td>
			<td class="ui-widget-content"><?php echo $record->m_product_name;?></td>
		</tr>
<?php
}?>
	</tbody>
	<tfoot>
		<tr>
			<td class="ui-state-default ui-corner-bottom" colspan="2">&nbsp;</td>
		</tr>
	</tfoot>
</table>