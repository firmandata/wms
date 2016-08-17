<?php
$random_id = rand(1000, 2000);?>
<table id="a_assetamount_view_<?php echo $random_id;?>" class="table-data ui-widget ui-widget-content ui-corner-all" style="height: 274px;" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<th class="ui-state-default ui-corner-tl" style="width:22px">No</th>
			<th class="ui-state-default" style="width:100px">Date</th>
			<th class="ui-state-default" style="width:100px">Book</th>
			<th class="ui-state-default" style="width:100px">Market</th>
			<th class="ui-state-default" style="width:100px">Depreciated</th>
			<th class="ui-state-default" style="width:100px">Accumulated</th>
			<th class="ui-state-default" width="20px">&nbsp;</th>
		</tr>
	</thead>
	<tbody style="max-height: 250px; overflow-x: auto; position: absolute;">
<?php
foreach ($records as $record_idx=>$record)
{?>
		<tr>
			<td class="ui-state-default" align="right"><?php echo $record_idx + 1;?></td>
			<td class="ui-widget-content" align="center"><?php echo date($this->config->item('server_display_date_format'), strtotime($record->depreciated_date));?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format($record->book_value, 2);?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format($record->market_value, 2);?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format($record->depreciated_value, 2);?></td>
			<td class="ui-widget-content" align="right"><?php echo number_format($record->depreciation_accumulated, 2);?></td>
		</tr>
<?php
}?>
	</tbody>
</table>
<script type="text/javascript">
jQuery(function(){
	a_assetamount_global_scalled();
});

function a_assetamount_view_<?php echo $random_id;?>_scalled(){
	var a_assetamount_view_id = 'a_assetamount_view_<?php echo $random_id;?>';
	var self = jQuery('#' + a_assetamount_view_id);
	var self_parent = self.parent();
	var parent_width = self_parent.innerWidth();
	
	// set table width
	self.width(parent_width);
	
	// set tbody width
	jQuery('tbody', self).width(parent_width);
	
	// set tbody td width
	jQuery('thead th', self).each(function(index){
		var width = jQuery(this).width();
		jQuery('tbody td:eq(' + index + ')', self).width(width);
	});
}

function a_assetamount_global_scalled(){
	a_assetamount_view_<?php echo $random_id;?>_scalled();
}
</script>