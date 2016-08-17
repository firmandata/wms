<?php 
$grid_types = array_merge(array('' => ''), $this->config->item('grid_types'));
$grid_statuses = array_merge(array('' => ''), $this->config->item('grid_statuses'));
echo form_open($form_action.(!empty($record) ? '/'.$record->id : ''),
	array(
		'name'	=> 'material_warehouse_form',
		'id'	=> 'material_warehouse_form'
	)
);?>
	<table class="form-table">
		<thead>
			<tr>
				<td colspan="2" class="form-table-title">Location</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th width="120"><label for="material_warehouse_form_code">Code</label></th>
				<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'code',
		'id' 	=> 'material_warehouse_form_code',
		'class'	=> 'required',
		'value'	=> (!empty($record) ? $record->code : '')
	)
);?>
				</td>
			</tr>
			<tr>
				<th><label for="material_warehouse_form_name">Name</label></th>
				<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'name',
		'id' 	=> 'material_warehouse_form_name',
		'class'	=> 'required',
		'value'	=> (!empty($record) ? $record->name : '')
	)
);?>
				</td>
			</tr>
			<tr>
				<th><label for="material_warehouse_form_address">Address</label></th>
				<td>
<?php 
echo form_textarea(
	array(
		'name' 	=> 'address',
		'id' 	=> 'material_warehouse_form_address',
		'rows'	=> 3, 'cols' => 30,
		'value'	=> (!empty($record) ? $record->address : '')
	)
);?>
				</td>
			</tr>
			<tr>
				<th><label for="material_warehouse_form_notes">Notes</label></th>
				<td>
<?php 
echo form_textarea(
	array(
		'name' 	=> 'notes',
		'id' 	=> 'material_warehouse_form_notes',
		'rows'	=> 3, 'cols' => 30,
		'value'	=> (!empty($record) ? $record->notes : '')
	)
);?>
				</td>
			</tr>
			<tr>
				<th>&nbsp;</th>
				<td>
<?php 
echo form_checkbox(
	array(
		'name' 		=> 'is_generate_grid',
		'id' 		=> 'material_warehouse_form_is_generate_grid',
		'value'		=> 1,
		'checked'	=> (!empty($record) ? FALSE : TRUE)
	)
);?>
					<label for="material_warehouse_form_is_generate_grid"><?php echo (!empty($record) ? 'Regenerate' : 'Generate');?> Detail</label>
				</td>
			</tr>
		</tbody>
	</table>
	<table id="material_warehouse_form_generate_grid" class="form-table">
		<thead>
			<tr>
				<td colspan="2" class="form-table-title">Detail</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th width="120"><label for="material_warehouse_form_rows">Max Row</label></th>
				<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'rows',
		'id' 	=> 'material_warehouse_form_rows',
		'class'	=> 'required number',
		'value'	=> (!empty($record->grid_scalar) ? $record->grid_scalar->rows : 0)
	)
);?>
				</td>
			</tr>
			<tr>
				<th><label for="material_warehouse_form_cols">Max Cols</label></th>
				<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'cols',
		'id' 	=> 'material_warehouse_form_cols',
		'class'	=> 'required number',
		'value'	=> (!empty($record->grid_scalar) ? $record->grid_scalar->cols : 0)
	)
);?>
				</td>
			</tr>
			<tr>
				<th><label for="material_warehouse_form_levels">Max Levels</label></th>
				<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'levels',
		'id' 	=> 'material_warehouse_form_levels',
		'class'	=> 'required number',
		'value'	=> (!empty($record->grid_scalar) ? $record->grid_scalar->levels : 0)
	)
);?>
				</td>
			</tr>
			<tr>
				<th><label for="material_warehouse_form_types">Type</label></th>
				<td>
<?php 
echo form_dropdown('types', $grid_types, (!empty($record->grid_scalar) ? $record->grid_scalar->types : ''), 'id="material_warehouse_form_types"');?>
				</td>
			</tr>
			<tr>
				<th><label for="material_warehouse_form_statuses">Status</label></th>
				<td>
<?php 
echo form_dropdown('statuses', $grid_statuses, (!empty($record->grid_scalar) ? $record->grid_scalar->statuses : ''), 'id="material_warehouse_form_statuses"');?>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<strong>Size</strong>
				</td>
			<tr>
			<tr>
				<th><label for="material_warehouse_form_lengths">Length</label></th>
				<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'lengths',
		'id' 	=> 'material_warehouse_form_lengths',
		'class'	=> 'required number',
		'value'	=> (!empty($record->grid_scalar) ? $record->grid_scalar->lengths : '0')
	)
);?>
					Meter
				</td>
			</tr>
			<tr>
				<th><label for="material_warehouse_form_widths">Width</label></th>
				<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'widths',
		'id' 	=> 'material_warehouse_form_widths',
		'class'	=> 'required number',
		'value'	=> (!empty($record->grid_scalar) ? $record->grid_scalar->widths : '0')
	)
);?>
					Meter
				</td>
			</tr>
			<tr>
				<th><label for="material_warehouse_form_sizes">Size</label></th>
				<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'sizes',
		'id' 		=> 'material_warehouse_form_sizes',
		'readonly'	=> 'readonly',
		'class'		=> 'number',
		'value'		=> (!empty($record->grid_scalar) ? $record->grid_scalar->lengths * $record->grid_scalar->widths : '0')
	)
);?>
					Meter
				</td>
			</tr>
			<tr>
				<th><label for="material_warehouse_form_heights">Height</label></th>
				<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'heights',
		'id' 	=> 'material_warehouse_form_heights',
		'class'	=> 'required number',
		'value'	=> (!empty($record->grid_scalar) ? $record->grid_scalar->heights : '0')
	)
);?>
					Meter
				</td>
			</tr>
		</tbody>
	</table>
<?php echo form_close();?>

<script type="text/javascript">
var material_warehouse_on_sucess;

jQuery(function(){
	jQuery("#material_warehouse_form").validate({
		submitHandler: function(form){
			jQuery("#material_warehouse_form").ajaxSubmit({
				dataType: "json",
				async : false,
				error: jquery_ajax_error_handler,
				beforeSend: function(jqXHR, settings){
					jquery_blockui();
				},
				success: function(data, textStatus, jqXHR){
					material_warehouse_on_sucess(data, textStatus, jqXHR);
				},
				complete: function(jqXHR, textStatus){
					jquery_unblockui();
				}
			});
		}
	});
	
	jQuery("#material_warehouse_form_is_generate_grid").change(function(){
		material_warehouse_form_generate_grid();
	});
	
	material_warehouse_form_generate_grid();
	
	jQuery("#material_warehouse_form_lengths,#material_warehouse_form_widths").change(function() {
		var size = parseFloat(jQuery("#material_warehouse_form_lengths").val()) * parseFloat(jQuery("#material_warehouse_form_widths").val());
		jQuery("#material_warehouse_form_sizes").val(size.toFixed(4));
	});
});

function material_warehouse_form_generate_grid(){
	var is_generate = jQuery("#material_warehouse_form_is_generate_grid").is(':checked');
	if (is_generate)
		jQuery('#material_warehouse_form_generate_grid').show();
	else
		jQuery('#material_warehouse_form_generate_grid').hide();
}

function material_warehouse_form_submit(on_success){
	material_warehouse_on_sucess = on_success;
	jQuery('#material_warehouse_form').submit();
}
</script>