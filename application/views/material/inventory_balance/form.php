<?php 
$harvest_sequences = $this->config->item('harvest_sequences');

echo form_open($form_action.(!empty($record) ? '/'.$record->id : ''),
	array(
		'name'	=> 'material_inventory_balance_form',
		'id'	=> 'material_inventory_balance_form'
	)
);?>
<table>
	<tr><td valign="top">
			<table class="form-table">
				<thead>
					<tr>
						<td colspan="2" class="form-table-title">General</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th width="120"><label for="material_inventory_balance_form_code">No</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'code',
		'id' 	=> 'material_inventory_balance_form_code',
		'class'	=> (!empty($record) ? 'required' : ''),
		'style'	=> 'width:120px;',
		'value'	=> (!empty($record) ? $record->code : ''),
		'placeholder'	=> '## Auto ##'
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_inventory_balance_form_balance_date">Date</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'balance_date',
		'id' 	=> 'material_inventory_balance_form_balance_date',
		'class'	=> 'required date',
		'style'	=> 'width:75px;',
		'value'	=> (!empty($record->balance_date) ? date($this->config->item('server_display_date_format'), strtotime($record->balance_date)) : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_inventory_balance_form_m_inventory_id_caption">Location</label></th>
						<td>
<?php
if (!empty($record))
{
	echo $record->m_inventory_text;
}
else
{?>
							<input type="hidden" name="m_inventory_id" id="material_inventory_balance_form_m_inventory_id" class="required"/>
<?php
}?>
						</td>
					</tr>
					<tr>
						<th><label for="material_inventory_balance_form_harvest_sequence">Harvest Sequence</label></th>
						<td>
<?php 
if (!empty($record))
	echo $record->harvest_sequence;
else
	echo form_dropdown('harvest_sequence', $harvest_sequences, '', 'id="material_inventory_balance_form_harvest_sequence"');?>
						</td>
					</tr>
					<tr>
						<th><label for="material_inventory_balance_form_product_size">Actual Size</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'product_size',
		'id' 	=> 'material_inventory_balance_form_product_size',
		'class'	=> 'required number',
		'style'	=> 'width:60px;',
		'value'	=> (!empty($record) ? $record->product_size : '0')
	)
);?>
						</td>
					</tr>
				</tbody>
			</table>
		</td>
		<td valign="top">
			<table class="form-table">
				<thead>
					<tr>
						<td colspan="2" class="form-table-title">Information</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th width="120"><label for="material_inventory_balance_form_pic">PIC</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'pic',
		'id' 	=> 'material_inventory_balance_form_pic',
		'value'	=> (!empty($record) ? $record->pic : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th width="120"><label for="material_inventory_balance_form_vehicle_no">Vehicle No</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'vehicle_no',
		'id' 	=> 'material_inventory_balance_form_vehicle_no',
		'value'	=> (!empty($record) ? $record->vehicle_no : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_inventory_balance_form_notes">Notes</label></th>
						<td>
<?php 
echo form_textarea(
	array(
		'name' 	=> 'notes',
		'id' 	=> 'material_inventory_balance_form_notes',
		'value'	=> (!empty($record) ? $record->notes : ''),
		'cols'	=> 30, 'rows'	=> 2
	)
);?>
						</td>
					</tr>
				</tbody>
			</table>
		</td>
	</tr>
</table>
<?php echo form_close();?>

<script type="text/javascript">
var material_inventory_balance_on_sucess;

jQuery(function(){
	jquery_ready_load();
	
	jquery_autocomplete_build("#material_inventory_balance_form_m_inventory_id", "<?php echo site_url('material/inventory_balance/get_m_inventory_autocomplete_list_json');?>", {
		width : 150
	});
	
	jQuery("#material_inventory_balance_form").validate({
		submitHandler: function(form){
			jQuery("#material_inventory_balance_form").ajaxSubmit({
				dataType: "json",
				async : false,
				error: jquery_ajax_error_handler,
				beforeSend: function(jqXHR, settings){
					jquery_blockui();
				},
				success: function(data, textStatus, jqXHR){
					material_inventory_balance_on_sucess(data, textStatus, jqXHR);
				},
				complete: function(jqXHR, textStatus){
					jquery_unblockui();
				}
			});
		}
	});
});

function material_inventory_balance_form_submit(on_success){
	material_inventory_balance_on_sucess = on_success;
	jQuery('#material_inventory_balance_form').submit();
}
</script>