<?php
$grid_types = array_merge(array('' => ''), $this->config->item('grid_types'));
$grid_statuses = array_merge(array('' => ''), $this->config->item('grid_statuses'));
echo form_open($form_action.(!empty($record) ? '/'.$record->id : ''),
	array(
		'name'	=> 'material_grid_form',
		'id'	=> 'material_grid_form'
	)
);
if ($m_warehouse_id)
{
	echo form_hidden('m_warehouse_id', $m_warehouse_id);
}?>
	<table class="form-table">
		<tbody>
<?php
if (!empty($record))
{?>
			<tr>
				<th><label for="material_grid_form_code">Code</label></th>
				<td><?php echo $record->code;?></td>
			</tr>
<?php
}?>
			<tr>
				<th width="120">
					<label for="material_grid_form_m_productgroup_id_caption">Product Group</label>
				</th>
				<td><input type="hidden" name="m_productgroup_id" id="material_grid_form_m_productgroup_id" value="<?php echo (!empty($record) ? $record->m_productgroup_id : '');?>" data-text="<?php echo (!empty($record) ? $record->m_productgroup_text : '');?>"/></td>
			</tr>
			<tr>
				<th><label for="material_grid_form_row">Row</label></th>
				<td>
					<?php 
					echo form_input(
						array(
							'name' 	=> 'row',
							'id' 	=> 'material_grid_form_row',
							'class'	=> 'required number',
							'value'	=> (!empty($record) ? $record->row : 0)
						)
					);?>
				</td>
			</tr>
			<tr>
				<th><label for="material_grid_form_col">Col</label></th>
				<td>
					<?php 
					echo form_input(
						array(
							'name' 	=> 'col',
							'id' 	=> 'material_grid_form_col',
							'class'	=> 'required number',
							'value'	=> (!empty($record) ? $record->col : 0)
						)
					);?>
				</td>
			</tr>
			<tr>
				<th><label for="material_grid_form_level">Level</label></th>
				<td>
					<?php 
					echo form_input(
						array(
							'name' 	=> 'level',
							'id' 	=> 'material_grid_form_level',
							'class'	=> 'required number',
							'value'	=> (!empty($record) ? $record->level : 0)
						)
					);?>
				</td>
			</tr>
			<tr>
				<th><label for="material_grid_form_type">Type</label></th>
				<td>
<?php 
echo form_dropdown('type', $grid_types, (!empty($record) ? $record->type : ''), 'id="material_grid_form_type"');?>
				</td>
			</tr>
			<tr>
				<th><label for="material_grid_form_length">Length</label></th>
				<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'length',
		'id' 	=> 'material_grid_form_length',
		'class'	=> 'required number',
		'value'	=> (!empty($record) ? $record->length : '0')
	)
);?>
				</td>
			</tr>
			<tr>
				<th><label for="material_grid_form_width">Width</label></th>
				<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'width',
		'id' 	=> 'material_grid_form_width',
		'class'	=> 'required number',
		'value'	=> (!empty($record) ? $record->width : '0')
	)
);?>
				</td>
			</tr>
			<tr>
				<th><label for="material_grid_form_height">Height</label></th>
				<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'height',
		'id' 	=> 'material_grid_form_height',
		'class'	=> 'required number',
		'value'	=> (!empty($record) ? $record->height : '0')
	)
);?>
				</td>
			</tr>
			<tr>
				<th><label for="material_grid_form_status">Status</label></th>
				<td>
<?php 
echo form_dropdown('status', $grid_statuses, (!empty($record) ? $record->status : ''), 'id="material_grid_form_status"');?>
				</td>
			</tr>
		</tbody>
	</table>
<?php echo form_close();?>

<script type="text/javascript">
var material_grid_on_sucess;

jQuery(function(){
	jQuery("#material_grid_form").validate({
		submitHandler: function(form){
			jQuery("#material_grid_form").ajaxSubmit({
				dataType: "json",
				async : false,
				error: jquery_ajax_error_handler,
				beforeSend: function(jqXHR, settings){
					jquery_blockui();
				},
				success: function(data, textStatus, jqXHR){
					material_grid_on_sucess(data, textStatus, jqXHR);
				},
				complete: function(jqXHR, textStatus){
					jquery_unblockui();
				}
			});
		}
	});
	
	jquery_autocomplete_build("#material_grid_form_m_productgroup_id", "<?php echo site_url('material/grid/get_productgroup_autocomplete_list_json');?>", {
		width : 200
	},
	{
		autoFocus : false
	});
});

function material_grid_form_submit(on_success){
	material_grid_on_sucess = on_success;
	jQuery('#material_grid_form').submit();
}
</script>