<?php 
$shipment_types = array_merge(array('' => ''), $this->config->item('shipment_types'));
$transport_modes = array_merge(array('' => ''), $this->config->item('transport_modes'));

echo form_open($form_action.(!empty($record) ? '/'.$record->id : ''),
	array(
		'name'	=> 'material_inventory_shipment_form',
		'id'	=> 'material_inventory_shipment_form'
	)
);?>
<table>
	<tr>
		<td valign="top">
			<table class="form-table">
				<thead>
					<tr>
						<td colspan="2" class="form-table-title">Information</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th width="100"><label for="material_inventory_shipment_form_code">No</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'code',
		'id' 	=> 'material_inventory_shipment_form_code',
		'class'	=> 'required',
		'style'	=> 'width:120px;',
		'value'	=> (!empty($record) ? $record->code : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_inventory_shipment_form_shipment_date">Date</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'shipment_date',
		'id' 	=> 'material_inventory_shipment_form_shipment_date',
		'class'	=> 'required date',
		'style'	=> 'width:75px;',
		'value'	=> (!empty($record->shipment_date) ? date($this->config->item('server_display_date_format'), strtotime($record->shipment_date)) : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_inventory_shipment_form_shipment_type">Shipment Type</label></th>
						<td>
<?php 
echo form_dropdown('shipment_type', $shipment_types, (!empty($record) ? $record->shipment_type : ''), 'id="material_inventory_shipment_form_shipment_type"');?>
						</td>
					</tr>
					<tr>
						<th><label for="material_inventory_shipment_form_transport_mode">Transport Mode</label></th>
						<td>
<?php 
echo form_dropdown('transport_mode', $transport_modes, (!empty($record) ? $record->transport_mode : ''), 'id="material_inventory_shipment_form_transport_mode"');?>
						</td>
					</tr>
					<tr>
						<th><label for="material_inventory_shipment_form_notes">Notes</label></th>
						<td>
<?php 
echo form_textarea(
	array(
		'name' 	=> 'notes',
		'id' 	=> 'material_inventory_shipment_form_notes',
		'value'	=> (!empty($record) ? $record->notes : ''),
		'cols'	=> 30, 'rows'	=> 2
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
						<td colspan="2" class="form-table-title">More</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th width="100"><label for="material_inventory_shipment_form_request_arrive_date">Request Arrival Date</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'request_arrive_date',
		'id' 	=> 'material_inventory_shipment_form_request_arrive_date',
		'class'	=> 'date',
		'style'	=> 'width:75px;',
		'value'	=> (!empty($record->request_arrive_date) ? date($this->config->item('server_display_date_format'), strtotime($record->request_arrive_date)) : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_inventory_shipment_form_estimated_time_arrive">Estimated Time Arrival</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'estimated_time_arrive',
		'id' 	=> 'material_inventory_shipment_form_estimated_time_arrive',
		'class'	=> 'datetime',
		'style'	=> 'width:110px;',
		'value'	=> (!empty($record->estimated_time_arrive) ? date($this->config->item('server_display_datetime_format'), strtotime($record->estimated_time_arrive)) : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_inventory_shipment_form_shipment_to">Shipment To</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'shipment_to',
		'id' 	=> 'material_inventory_shipment_form_shipment_to',
		'style'	=> 'width:200px;',
		'value'	=> (!empty($record) ? $record->shipment_to : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_inventory_shipment_form_vehicle_no">Vehicle No</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'vehicle_no',
		'id' 	=> 'material_inventory_shipment_form_vehicle_no',
		'style'	=> 'width:120px;',
		'value'	=> (!empty($record) ? $record->vehicle_no : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_inventory_shipment_form_vehicle_driver">Driver Name</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'vehicle_driver',
		'id' 	=> 'material_inventory_shipment_form_vehicle_driver',
		'style'	=> 'width:160px;',
		'value'	=> (!empty($record) ? $record->vehicle_driver : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_inventory_shipment_form_police_name">Police Name</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'police_name',
		'id' 	=> 'material_inventory_shipment_form_police_name',
		'style'	=> 'width:160px;',
		'value'	=> (!empty($record) ? $record->police_name : '')
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
var material_inventory_shipment_on_sucess;

jQuery(function(){
	jquery_ready_load();
	
	jQuery("#material_inventory_shipment_form").validate({
		submitHandler: function(form){
			jQuery("#material_inventory_shipment_form").ajaxSubmit({
				dataType: "json",
				async : false,
				error: jquery_ajax_error_handler,
				beforeSend: function(jqXHR, settings){
					jquery_blockui();
				},
				success: function(data, textStatus, jqXHR){
					material_inventory_shipment_on_sucess(data, textStatus, jqXHR);
				},
				complete: function(jqXHR, textStatus){
					jquery_unblockui();
				}
			});
		}
	});
});

function material_inventory_shipment_form_submit(on_success){
	material_inventory_shipment_on_sucess = on_success;
	jQuery('#material_inventory_shipment_form').submit();
}
</script>