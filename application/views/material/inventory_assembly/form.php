<?php 
$product_conditions = $this->config->item('product_conditions');

echo form_open($form_action.(!empty($record) ? '/'.$record->id : ''),
	array(
		'name'	=> 'material_inventory_assembly_form',
		'id'	=> 'material_inventory_assembly_form'
	)
);?>
<table class="form-table">
	<thead>
		<tr>
			<td colspan="2" class="form-table-title">Information</td>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th><label for="material_inventory_assembly_form_code">No</label></th>
			<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'code',
		'id' 	=> 'material_inventory_assembly_form_code',
		'class'	=> 'required',
		'style'	=> 'width:120px;',
		'value'	=> (!empty($record) ? $record->code : '')
	)
);?>
			</td>
		</tr>
		<tr>
			<th><label for="material_inventory_assembly_form_assembly_date">Date</label></th>
			<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'assembly_date',
		'id' 	=> 'material_inventory_assembly_form_assembly_date',
		'class'	=> 'required date',
		'style'	=> 'width:75px;',
		'value'	=> (!empty($record->assembly_date) ? date($this->config->item('server_display_date_format'), strtotime($record->assembly_date)) : '')
	)
);?>
			</td>
		</tr>
		<tr>
			<th><label for="material_inventory_assembly_form_notes">Notes</label></th>
			<td>
<?php 
echo form_textarea(
	array(
		'name' 	=> 'notes',
		'id' 	=> 'material_inventory_assembly_form_notes',
		'value'	=> (!empty($record) ? $record->notes : ''),
		'cols'	=> 30, 'rows'	=> 2
	)
);?>
			</td>
		</tr>
	</tbody>
</table>
<br/>
<strong>Inventory Source</strong><br/>
<table id="material_inventory_assembly_assemblysource_list_table" class="table-data ui-widget ui-widget-content ui-corner-all" width="100%" cellspacing="0" cellpadding="0">
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
			<th class="ui-state-default" rowspan="2">Quantity</th>
		</tr>
		<tr>
			<th class="ui-state-default">Length</th>
			<th class="ui-state-default">Width</th>
			<th class="ui-state-default">Height</th>
		</tr>
	</thead>
	<tbody></tbody>
	<tfoot>
		<tr>
			<td colspan="13" class="ui-state-default ui-corner-bl ui-corner-br">
				<button id="material_inventory_assembly_assemblysource_list_table_add" class="table-data-button">Add</button>
			</td>
		</tr>
	</tfoot>
</table>
<br/>
<strong>Inventory Target</strong><br/>
<table id="material_inventory_assembly_assemblytarget_list_table" class="table-data ui-widget ui-widget-content ui-corner-all" width="100%" cellspacing="0" cellpadding="0">
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
	<tbody></tbody>
	<tfoot>
		<tr>
			<td colspan="17" class="ui-state-default ui-corner-bl ui-corner-br">
				<button id="material_inventory_assembly_assemblytarget_list_table_add" class="table-data-button">Add</button>
			</td>
		</tr>
	</tfoot>
</table>
<?php echo form_close();?>

<script type="text/javascript">
var material_inventory_assembly_on_sucess;
var material_inventory_assembly_assemblysource_row_id = 0;
var material_inventory_assembly_assemblytarget_row_id = 0;

jQuery(function(){
	jquery_ready_load();
	
	jQuery("#material_inventory_assembly_form").validate({
		submitHandler: function(form){
			jQuery("#material_inventory_assembly_form").ajaxSubmit({
				dataType: "json",
				async : false,
				error: jquery_ajax_error_handler,
				beforeSend: function(jqXHR, settings){
					jquery_blockui();
				},
				success: function(data, textStatus, jqXHR){
					material_inventory_assembly_on_sucess(data, textStatus, jqXHR);
				},
				complete: function(jqXHR, textStatus){
					jquery_unblockui();
				}
			});
		}
	});
	
	jQuery('#material_inventory_assembly_assemblysource_list_table_add').button({
		text: false,
		icons: {
			primary: "ui-icon-plus"
		}
	})
	.click(function(e){
		e.preventDefault();
		
		jquery_dialog_form_open('material_inventory_assembly_form_inventory_container', "<?php echo site_url('material/inventory_assembly/form_inventory');?>", null, null, {
			title		: "Select Inventories", 
			resizable	: false,
			width		: 1000,
			height		: 650,
			buttons		: [ 
				{text: "Add", 
				 icons: {
					primary: "ui-icon-check"
				 },
				 click: function(){
					material_inventory_assembly_form_inventory_submit(function(records){
						jQuery.each(records, function(record_idx, record){
							material_inventory_assembly_form_assemblysource_add(
								0, 0, 
								record.m_product_id, record.m_product_code, record.m_product_name, record.m_product_netto, 
								record.m_grid_id, record.m_grid_code,
								record.c_project_id, record.c_project_name,
								record.c_businesspartner_id, record.c_businesspartner_name,
								record.pallet,
								record.barcode,
								record.carton_no,
								record.lot_no,
								record.volume_length, record.volume_width, record.volume_height,
								record.quantity_exist,
								false
							);
						});
						
						jQuery('#material_inventory_assembly_form_source_quantity_' + material_inventory_assembly_assemblysource_row_id.toString()).focus();
					}, jQuery(this));
				 }
				},
				{text: "Cancel", 
				 icons: {
					primary: "ui-icon-cancel"
				 },
				 click: function(){
					jQuery(this).dialog("close");
				 }
				} 
			]
		});
	});
	
	jQuery('#material_inventory_assembly_assemblytarget_list_table_add').button({
		text: false,
		icons: {
			primary: "ui-icon-plus"
		}
	})
	.click(function(e){
		e.preventDefault();
		material_inventory_assembly_form_assemblytarget_add(
			null,
			null, null, 0,
			null, null,
			null, null,
			null, null,
			null,
			null,
			null,
			null,
			0, 0, 0,
			null,
			null, null,
			1, 1,
			false
		);
		
		jQuery('#material_inventory_assembly_form_target_m_product_id_' + material_inventory_assembly_assemblytarget_row_id.toString() + '_caption').focus();
	});
	
	jQuery.each(<?php echo !empty($m_inventory_assemblysources) ? json_encode($m_inventory_assemblysources) : '[]';?>, function(assemblysource_idx, assemblysource){
		material_inventory_assembly_form_assemblysource_add(
			assemblysource.sum_id, assemblysource.sum_m_inventory_id,
			assemblysource.m_product_id, assemblysource.m_product_code, assemblysource.m_product_name, assemblysource.m_product_netto, 
			assemblysource.m_grid_id, assemblysource.m_grid_code,
			assemblysource.c_project_id, assemblysource.c_project_name,
			assemblysource.c_businesspartner_id, assemblysource.c_businesspartner_name,
			assemblysource.pallet,
			assemblysource.barcode,
			assemblysource.carton_no,
			assemblysource.lot_no,
			assemblysource.volume_length, assemblysource.volume_width, assemblysource.volume_height,
			assemblysource.quantity_from - assemblysource.quantity_to,
			true
		);
	});
	
	jQuery.each(<?php echo !empty($m_inventory_assemblytargets) ? json_encode($m_inventory_assemblytargets) : '[]';?>, function(assemblytarget_idx, assemblytarget){
		material_inventory_assembly_form_assemblytarget_add(
			assemblytarget.id,
			assemblytarget.m_product_id, assemblytarget.m_product_text, assemblytarget.m_product_netto, 
			assemblytarget.m_grid_id, assemblytarget.m_grid_text,
			assemblytarget.c_project_id, assemblytarget.c_project_text,
			assemblytarget.c_businesspartner_id, assemblytarget.c_businesspartner_text,
			assemblytarget.pallet,
			assemblytarget.barcode,
			assemblytarget.carton_no,
			assemblytarget.lot_no,
			assemblytarget.volume_length, assemblytarget.volume_width, assemblytarget.volume_height,
			assemblytarget.condition,
			assemblytarget.packed_date, assemblytarget.expired_date,
			assemblytarget.quantity_box, assemblytarget.quantity,
			true
		);
	});
});

function material_inventory_assembly_form_assemblysource_add(
	sum_id, sum_m_inventory_id,
	m_product_id, m_product_code, m_product_name, m_product_netto, 
	m_grid_id, m_grid_code,
	c_project_id, c_project_name,
	c_businesspartner_id, c_businesspartner_name,
	pallet,
	barcode,
	carton_no,
	lot_no,
	volume_length, volume_width, volume_height,
	quantity,
	is_edit_mode
){
	material_inventory_assembly_assemblysource_row_id++;
	
	$('#material_inventory_assembly_assemblysource_list_table tbody').append(
		$("<tr>").append(
			$("<td>", {'class':'ui-state-default', 'align':'center'}).append(
				$("<button>", {'class' : 'table-data-button'}).text("Remove").button({text: false,icons: {primary: "ui-icon-trash"}}).click(function(e){
					e.preventDefault();
					var to_remove_row = $(this).parent().parent();
					jquery_show_confirm("Are your sure ?", function(){
						to_remove_row.remove();
					});
				})
			)
		).append(
			$("<td>", {'class':'ui-widget-content'})
				.text(m_product_code + " - " + m_product_name)
				.append(
					$("<input>", {type:'hidden', name:'m_inventory_assemblysources[' + material_inventory_assembly_assemblysource_row_id.toString() + '][sum_id]', id:'material_inventory_assembly_form_source_sum_id_' + material_inventory_assembly_assemblysource_row_id.toString(), 'class':'required'})
						.val(sum_id)
				)
				.append(
					$("<input>", {type:'hidden', name:'m_inventory_assemblysources[' + material_inventory_assembly_assemblysource_row_id.toString() + '][sum_m_inventory_id]', id:'material_inventory_assembly_form_source_sum_m_inventory_id_' + material_inventory_assembly_assemblysource_row_id.toString(), 'class':'required'})
						.val(sum_m_inventory_id)
				)
				.append(
					$("<input>", {type:'hidden', name:'m_inventory_assemblysources[' + material_inventory_assembly_assemblysource_row_id.toString() + '][m_product_id]', id:'material_inventory_assembly_form_source_m_product_id_' + material_inventory_assembly_assemblysource_row_id.toString(), 'class':'required'})
						.val(m_product_id)
				)
				.append(
					$("<input>", {type:'hidden', name:'m_inventory_assemblysources[' + material_inventory_assembly_assemblysource_row_id.toString() + '][m_product_netto]', id:'material_inventory_assembly_form_source_m_product_netto_' + material_inventory_assembly_assemblysource_row_id.toString()})
						.val(m_product_netto)
				)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center'})
				.text(m_grid_code)
				.append(
					$("<input>", {type:'hidden', name:'m_inventory_assemblysources[' + material_inventory_assembly_assemblysource_row_id.toString() + '][m_grid_id]', id:'material_inventory_assembly_form_source_m_grid_id_' + material_inventory_assembly_assemblysource_row_id.toString(), 'class':'required'})
						.val(m_grid_id)
				)
		).append(
			$("<td>", {'class':'ui-widget-content'})
				.text(c_project_name)
				.append(
					$("<input>", {type:'hidden', name:'m_inventory_assemblysources[' + material_inventory_assembly_assemblysource_row_id.toString() + '][c_project_id]', id:'material_inventory_assembly_form_source_c_project_id_' + material_inventory_assembly_assemblysource_row_id.toString()})
						.val(c_project_id)
				)
		).append(
			$("<td>", {'class':'ui-widget-content'})
				.text(c_businesspartner_name)
				.append(
					$("<input>", {type:'hidden', name:'m_inventory_assemblysources[' + material_inventory_assembly_assemblysource_row_id.toString() + '][c_businesspartner_id]', id:'material_inventory_assembly_form_source_c_businesspartner_id_' + material_inventory_assembly_assemblysource_row_id.toString()})
						.val(c_businesspartner_id)
				)
		).append(
			$("<td>", {'class':'ui-widget-content'})
				.text(pallet)
				.append(
					$("<input>", {type:'hidden', name:'m_inventory_assemblysources[' + material_inventory_assembly_assemblysource_row_id.toString() + '][pallet]', id:'material_inventory_assembly_form_source_pallet_' + material_inventory_assembly_assemblysource_row_id.toString()})
						.val(pallet)
				)
		).append(
			$("<td>", {'class':'ui-widget-content'})
				.text(barcode)
				.append(
					$("<input>", {type:'hidden', name:'m_inventory_assemblysources[' + material_inventory_assembly_assemblysource_row_id.toString() + '][barcode]', id:'material_inventory_assembly_form_source_barcode_' + material_inventory_assembly_assemblysource_row_id.toString()})
						.val(barcode)
				)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'right'})
				.text(carton_no)
				.append(
					$("<input>", {type:'hidden', name:'m_inventory_assemblysources[' + material_inventory_assembly_assemblysource_row_id.toString() + '][carton_no]', id:'material_inventory_assembly_form_source_carton_no_' + material_inventory_assembly_assemblysource_row_id.toString()})
						.val(carton_no)
				)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center'})
				.text(lot_no)
				.append(
					$("<input>", {type:'hidden', name:'m_inventory_assemblysources[' + material_inventory_assembly_assemblysource_row_id.toString() + '][lot_no]', id:'material_inventory_assembly_form_source_lot_no_' + material_inventory_assembly_assemblysource_row_id.toString()})
						.val(lot_no)
				)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center'})
				.text(volume_length)
				.append(
					$("<input>", {type:'hidden', name:'m_inventory_assemblysources[' + material_inventory_assembly_assemblysource_row_id.toString() + '][volume_length]', id:'material_inventory_assembly_form_source_volume_length_' + material_inventory_assembly_assemblysource_row_id.toString()})
						.val(volume_length)
				)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center'})
				.text(volume_width)
				.append(
					$("<input>", {type:'hidden', name:'m_inventory_assemblysources[' + material_inventory_assembly_assemblysource_row_id.toString() + '][volume_width]', id:'material_inventory_assembly_form_source_volume_width_' + material_inventory_assembly_assemblysource_row_id.toString()})
						.val(volume_width)
				)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center'})
				.text(volume_height)
				.append(
					$("<input>", {type:'hidden', name:'m_inventory_assemblysources[' + material_inventory_assembly_assemblysource_row_id.toString() + '][volume_height]', id:'material_inventory_assembly_form_source_volume_height_' + material_inventory_assembly_assemblysource_row_id.toString()})
						.val(volume_height)
				)
		).append(
			!is_edit_mode ? $("<td>", {'class':'ui-widget-content', 'align':'center'})
				.append(
					$("<input>", {type:'text', name:'m_inventory_assemblysources[' + material_inventory_assembly_assemblysource_row_id.toString() + '][quantity]', id:'material_inventory_assembly_form_source_quantity_' + material_inventory_assembly_assemblysource_row_id.toString(), 'class':'required number', 'min':'0.0001'})
						.width(40).val(quantity).css('text-align', 'right')
				)
			: $("<td>", {'class':'ui-widget-content', 'align':'right'})
				.text(quantity)
				.append(
					$("<input>", {type:'hidden', name:'m_inventory_assemblysources[' + material_inventory_assembly_assemblysource_row_id.toString() + '][quantity]', id:'material_inventory_assembly_form_source_quantity_' + material_inventory_assembly_assemblysource_row_id.toString(), 'class':'required number'})
						.val(quantity)
				)
		)
	);
}

function material_inventory_assembly_form_assemblytarget_add(
	id,
	m_product_id, m_product_text, m_product_netto, 
	m_grid_id, m_grid_text,
	c_project_id, c_project_text,
	c_businesspartner_id, c_businesspartner_text,
	pallet,
	barcode,
	carton_no,
	lot_no,
	volume_length, volume_width, volume_height,
	condition,
	packed_date, expired_date,
	quantity_box, quantity,
	is_edit_mode
){
	material_inventory_assembly_assemblytarget_row_id++;
	
	$('#material_inventory_assembly_assemblytarget_list_table tbody').append(
		$("<tr>").append(
			$("<td>", {'class':'ui-state-default', 'align':'center'}).append(
				$("<button>", {'class' : 'table-data-button'}).text("Remove").button({text: false,icons: {primary: "ui-icon-trash"}}).click(function(e){
					e.preventDefault();
					var to_remove_row = $(this).parent().parent();
					jquery_show_confirm("Are your sure ?", function(){
						to_remove_row.remove();
					});
				})
			)
			.append(
				$("<input>", {type:'hidden', name:'m_inventory_assemblytargets[' + material_inventory_assembly_assemblytarget_row_id.toString() + '][id]', id:'material_inventory_assembly_form_target_id_' + material_inventory_assembly_assemblytarget_row_id.toString()})
					.val(id)
			)
		).append(
			!is_edit_mode ? $("<td>", {'class':'ui-widget-content', 'align':'center'})
				.append(
					$("<input>", {type:'hidden', name:'m_inventory_assemblytargets[' + material_inventory_assembly_assemblytarget_row_id.toString() + '][m_product_id]', id:'material_inventory_assembly_form_target_m_product_id_' + material_inventory_assembly_assemblytarget_row_id.toString(), 'class':'required'})
						.val(m_product_id).attr('data-text', m_product_text)
				)
				.append(
					$("<input>", {type:'hidden', name:'m_inventory_assemblytargets[' + material_inventory_assembly_assemblytarget_row_id.toString() + '][m_product_netto]', id:'material_inventory_assembly_form_target_m_product_netto_' + material_inventory_assembly_assemblytarget_row_id.toString()})
						.val(m_product_netto)
				)
			: $("<td>", {'class':'ui-widget-content'})
				.text(m_product_text)
				.append(
					$("<input>", {type:'hidden', name:'m_inventory_assemblytargets[' + material_inventory_assembly_assemblytarget_row_id.toString() + '][m_product_id]', id:'material_inventory_assembly_form_target_m_product_id_' + material_inventory_assembly_assemblytarget_row_id.toString(), 'class':'required'})
						.val(m_product_id)
				)
				.append(
					$("<input>", {type:'hidden', name:'m_inventory_assemblytargets[' + material_inventory_assembly_assemblytarget_row_id.toString() + '][m_product_netto]', id:'material_inventory_assembly_form_target_m_product_netto_' + material_inventory_assembly_assemblytarget_row_id.toString()})
						.val(m_product_netto)
				)
		).append(
			!is_edit_mode ? $("<td>", {'class':'ui-widget-content', 'align':'center'})
				.append(
					$("<input>", {type:'hidden', name:'m_inventory_assemblytargets[' + material_inventory_assembly_assemblytarget_row_id.toString() + '][m_grid_id]', id:'material_inventory_assembly_form_target_m_grid_id_' + material_inventory_assembly_assemblytarget_row_id.toString(), 'class':'required'})
						.val(m_grid_id).attr('data-text', m_grid_text)
				)
			: $("<td>", {'class':'ui-widget-content'})
				.text(m_grid_text)
				.append(
					$("<input>", {type:'hidden', name:'m_inventory_assemblytargets[' + material_inventory_assembly_assemblytarget_row_id.toString() + '][m_grid_id]', id:'material_inventory_assembly_form_target_m_grid_id_' + material_inventory_assembly_assemblytarget_row_id.toString(), 'class':'required'})
						.val(m_grid_id)
				)
		).append(
			!is_edit_mode ? $("<td>", {'class':'ui-widget-content', 'align':'center'})
				.append(
					$("<input>", {type:'hidden', name:'m_inventory_assemblytargets[' + material_inventory_assembly_assemblytarget_row_id.toString() + '][c_project_id]', id:'material_inventory_assembly_form_target_c_project_id_' + material_inventory_assembly_assemblytarget_row_id.toString()})
						.val(c_project_id).attr('data-text', c_project_text)
				)
			: $("<td>", {'class':'ui-widget-content'})
				.text(c_project_text)
				.append(
					$("<input>", {type:'hidden', name:'m_inventory_assemblytargets[' + material_inventory_assembly_assemblytarget_row_id.toString() + '][c_project_id]', id:'material_inventory_assembly_form_target_c_project_id_' + material_inventory_assembly_assemblytarget_row_id.toString()})
						.val(c_project_id)
				)
		).append(
			!is_edit_mode ? $("<td>", {'class':'ui-widget-content', 'align':'center'})
				.append(
					$("<input>", {type:'hidden', name:'m_inventory_assemblytargets[' + material_inventory_assembly_assemblytarget_row_id.toString() + '][c_businesspartner_id]', id:'material_inventory_assembly_form_target_c_businesspartner_id_' + material_inventory_assembly_assemblytarget_row_id.toString()})
						.val(c_businesspartner_id).attr('data-text', c_businesspartner_text)
				)
			: $("<td>", {'class':'ui-widget-content'})
				.text(c_businesspartner_text)
				.append(
					$("<input>", {type:'hidden', name:'m_inventory_assemblytargets[' + material_inventory_assembly_assemblytarget_row_id.toString() + '][c_businesspartner_id]', id:'material_inventory_assembly_form_target_c_businesspartner_id_' + material_inventory_assembly_assemblytarget_row_id.toString()})
						.val(c_businesspartner_id)
				)
		).append(
			!is_edit_mode ? $("<td>", {'class':'ui-widget-content', 'align':'center'})
				.append(
					$("<input>", {type:'text', name:'m_inventory_assemblytargets[' + material_inventory_assembly_assemblytarget_row_id.toString() + '][pallet]', id:'material_inventory_assembly_form_target_pallet_' + material_inventory_assembly_assemblytarget_row_id.toString(), 'class':'required'})
						.width(130).val(pallet)
				)
			: $("<td>", {'class':'ui-widget-content'})
				.text(pallet)
				.append(
					$("<input>", {type:'hidden', name:'m_inventory_assemblytargets[' + material_inventory_assembly_assemblytarget_row_id.toString() + '][pallet]', id:'material_inventory_assembly_form_target_pallet_' + material_inventory_assembly_assemblytarget_row_id.toString(), 'class':'required'})
						.val(pallet)
				)
		).append(
			!is_edit_mode ? $("<td>", {'class':'ui-widget-content', 'align':'center'})
				.append(
					$("<input>", {type:'text', name:'m_inventory_assemblytargets[' + material_inventory_assembly_assemblytarget_row_id.toString() + '][barcode]', id:'material_inventory_assembly_form_target_barcode_' + material_inventory_assembly_assemblytarget_row_id.toString(), 'class':'required'})
						.width(170).val(barcode)
				)
			: $("<td>", {'class':'ui-widget-content'})
				.text(barcode)
				.append(
					$("<input>", {type:'hidden', name:'m_inventory_assemblytargets[' + material_inventory_assembly_assemblytarget_row_id.toString() + '][barcode]', id:'material_inventory_assembly_form_target_barcode_' + material_inventory_assembly_assemblytarget_row_id.toString(), 'class':'required'})
						.val(barcode)
				)
		).append(
			!is_edit_mode ? $("<td>", {'class':'ui-widget-content', 'align':'center'})
				.append(
					$("<input>", {type:'text', name:'m_inventory_assemblytargets[' + material_inventory_assembly_assemblytarget_row_id.toString() + '][carton_no]', id:'material_inventory_assembly_form_target_carton_no_' + material_inventory_assembly_assemblytarget_row_id.toString()})
						.width(50).val(carton_no)
				)
			: $("<td>", {'class':'ui-widget-content'})
				.text(carton_no)
				.append(
					$("<input>", {type:'hidden', name:'m_inventory_assemblytargets[' + material_inventory_assembly_assemblytarget_row_id.toString() + '][carton_no]', id:'material_inventory_assembly_form_target_carton_no_' + material_inventory_assembly_assemblytarget_row_id.toString()})
						.val(carton_no)
				)
		).append(
			!is_edit_mode ? $("<td>", {'class':'ui-widget-content', 'align':'center'})
				.append(
					$("<input>", {type:'text', name:'m_inventory_assemblytargets[' + material_inventory_assembly_assemblytarget_row_id.toString() + '][lot_no]', id:'material_inventory_assembly_form_target_lot_no_' + material_inventory_assembly_assemblytarget_row_id.toString()})
						.width(65).val(lot_no)
				)
			: $("<td>", {'class':'ui-widget-content'})
				.text(lot_no)
				.append(
					$("<input>", {type:'hidden', name:'m_inventory_assemblytargets[' + material_inventory_assembly_assemblytarget_row_id.toString() + '][lot_no]', id:'material_inventory_assembly_form_target_lot_no_' + material_inventory_assembly_assemblytarget_row_id.toString()})
						.val(lot_no)
				)
		).append(
			!is_edit_mode ? $("<td>", {'class':'ui-widget-content', 'align':'center'})
				.append(
					$("<input>", {type:'text', name:'m_inventory_assemblytargets[' + material_inventory_assembly_assemblytarget_row_id.toString() + '][volume_length]', id:'material_inventory_assembly_form_target_volume_length_' + material_inventory_assembly_assemblytarget_row_id.toString(), 'class':'required number'})
						.width(40).val(volume_length).css('text-align', 'right')
				)
			: $("<td>", {'class':'ui-widget-content'})
				.text(volume_length)
				.append(
					$("<input>", {type:'hidden', name:'m_inventory_assemblytargets[' + material_inventory_assembly_assemblytarget_row_id.toString() + '][volume_length]', id:'material_inventory_assembly_form_target_volume_length_' + material_inventory_assembly_assemblytarget_row_id.toString(), 'class':'required number'})
						.val(volume_length)
				)
		).append(
			!is_edit_mode ? $("<td>", {'class':'ui-widget-content', 'align':'center'})
				.append(
					$("<input>", {type:'text', name:'m_inventory_assemblytargets[' + material_inventory_assembly_assemblytarget_row_id.toString() + '][volume_width]', id:'material_inventory_assembly_form_target_volume_width_' + material_inventory_assembly_assemblytarget_row_id.toString(), 'class':'required number'})
						.width(40).val(volume_width).css('text-align', 'right')
				)
			: $("<td>", {'class':'ui-widget-content'})
				.text(volume_width)
				.append(
					$("<input>", {type:'hidden', name:'m_inventory_assemblytargets[' + material_inventory_assembly_assemblytarget_row_id.toString() + '][volume_width]', id:'material_inventory_assembly_form_target_volume_width_' + material_inventory_assembly_assemblytarget_row_id.toString(), 'class':'required number'})
						.val(volume_width)
				)
		).append(
			!is_edit_mode ? $("<td>", {'class':'ui-widget-content', 'align':'center'})
				.append(
					$("<input>", {type:'text', name:'m_inventory_assemblytargets[' + material_inventory_assembly_assemblytarget_row_id.toString() + '][volume_height]', id:'material_inventory_assembly_form_target_volume_height_' + material_inventory_assembly_assemblytarget_row_id.toString(), 'class':'required number'})
						.width(40).val(volume_height).css('text-align', 'right')
				)
			: $("<td>", {'class':'ui-widget-content'})
				.text(volume_height)
				.append(
					$("<input>", {type:'hidden', name:'m_inventory_assemblytargets[' + material_inventory_assembly_assemblytarget_row_id.toString() + '][volume_height]', id:'material_inventory_assembly_form_target_volume_height_' + material_inventory_assembly_assemblytarget_row_id.toString(), 'class':'required number'})
						.val(volume_height)
				)
		).append(
			!is_edit_mode ? $("<td>", {'class':'ui-widget-content', 'align':'center'})
				.append(
					$("<select>", {name:'m_inventory_assemblytargets[' + material_inventory_assembly_assemblytarget_row_id.toString() + '][condition]', id:'material_inventory_assembly_form_target_condition_' + material_inventory_assembly_assemblytarget_row_id.toString()})
						.width(100)
				)
			: $("<td>", {'class':'ui-widget-content'})
				.text(condition)
				.append(
					$("<input>", {type:'hidden', name:'m_inventory_assemblytargets[' + material_inventory_assembly_assemblytarget_row_id.toString() + '][condition]', id:'material_inventory_assembly_form_target_condition_' + material_inventory_assembly_assemblytarget_row_id.toString()})
						.val(condition)
				)
		).append(
			!is_edit_mode ? $("<td>", {'class':'ui-widget-content', 'align':'center'})
				.append(
					$("<input>", {type:'text', name:'m_inventory_assemblytargets[' + material_inventory_assembly_assemblytarget_row_id.toString() + '][packed_date]', id:'material_inventory_assembly_form_target_packed_date_' + material_inventory_assembly_assemblytarget_row_id.toString(), 'class':'date'})
						.width(75)
				)
			: $("<td>", {'class':'ui-widget-content', 'align':'center'})
				.text(formatDate(new Date(getDateFromFormat(packed_date, server_client_parse_validate_date_format)), client_validate_date_format))
				.append(
					$("<input>", {type:'hidden', name:'m_inventory_assemblytargets[' + material_inventory_assembly_assemblytarget_row_id.toString() + '][packed_date]', id:'material_inventory_assembly_form_target_packed_date_' + material_inventory_assembly_assemblytarget_row_id.toString(), 'class':'date'})
						.val(packed_date)
				)
		).append(
			!is_edit_mode ? $("<td>", {'class':'ui-widget-content', 'align':'center'})
				.append(
					$("<input>", {type:'text', name:'m_inventory_assemblytargets[' + material_inventory_assembly_assemblytarget_row_id.toString() + '][expired_date]', id:'material_inventory_assembly_form_target_expired_date_' + material_inventory_assembly_assemblytarget_row_id.toString(), 'class':'date'})
						.width(75)
				)
			: $("<td>", {'class':'ui-widget-content', 'align':'center'})
				.text(formatDate(new Date(getDateFromFormat(expired_date, server_client_parse_validate_date_format)), client_validate_date_format))
				.append(
					$("<input>", {type:'hidden', name:'m_inventory_assemblytargets[' + material_inventory_assembly_assemblytarget_row_id.toString() + '][expired_date]', id:'material_inventory_assembly_form_target_expired_date_' + material_inventory_assembly_assemblytarget_row_id.toString(), 'class':'date'})
						.val(expired_date)
				)
		).append(
			!is_edit_mode ? $("<td>", {'class':'ui-widget-content', 'align':'center'})
				.append(
					$("<input>", {type:'text', name:'m_inventory_assemblytargets[' + material_inventory_assembly_assemblytarget_row_id.toString() + '][quantity_box]', id:'material_inventory_assembly_form_target_quantity_box_' + material_inventory_assembly_assemblytarget_row_id.toString(), 'class':'required number', 'min':'1'})
						.width(30).val(quantity_box).css('text-align', 'right')
				)
			: $("<td>", {'class':'ui-widget-content', 'align':'right'})
				.text(quantity_box)
				.append(
					$("<input>", {type:'hidden', name:'m_inventory_assemblytargets[' + material_inventory_assembly_assemblytarget_row_id.toString() + '][quantity_box]', id:'material_inventory_assembly_form_target_quantity_box_' + material_inventory_assembly_assemblytarget_row_id.toString(), 'class':'required number'})
						.val(quantity_box)
				)
		).append(
			!is_edit_mode ? $("<td>", {'class':'ui-widget-content', 'align':'center'})
				.append(
					$("<input>", {type:'text', name:'m_inventory_assemblytargets[' + material_inventory_assembly_assemblytarget_row_id.toString() + '][quantity]', id:'material_inventory_assembly_form_target_quantity_' + material_inventory_assembly_assemblytarget_row_id.toString(), 'class':'required number', 'min':'0.0001'})
						.width(40).val(quantity).css('text-align', 'right')
				)
			: $("<td>", {'class':'ui-widget-content', 'align':'right'})
				.text(quantity)
				.append(
					$("<input>", {type:'hidden', name:'m_inventory_assemblytargets[' + material_inventory_assembly_assemblytarget_row_id.toString() + '][quantity]', id:'material_inventory_assembly_form_target_quantity_' + material_inventory_assembly_assemblytarget_row_id.toString(), 'class':'required number'})
						.val(quantity)
				)
		)
	);
	
	if (!is_edit_mode)
	{
		jquery_autocomplete_build(
			'#material_inventory_assembly_form_target_m_product_id_' + material_inventory_assembly_assemblytarget_row_id.toString(),
			"<?php echo site_url('material/inventory_assembly/get_product_autocomplete_list_json');?>",
			{
				width : 170
			}
		);
		
		jquery_autocomplete_build(
			'#material_inventory_assembly_form_target_m_grid_id_' + material_inventory_assembly_assemblytarget_row_id.toString(),
			"<?php echo site_url('material/inventory_assembly/get_grid_autocomplete_list_json');?>",
			{
				width : 70
			}
		);
		
		jquery_autocomplete_build(
			'#material_inventory_assembly_form_target_c_project_id_' + material_inventory_assembly_assemblytarget_row_id.toString(),
			"<?php echo site_url('material/inventory_assembly/get_project_autocomplete_list_json');?>",
			{
				width : 150
			}
		);
		
		jquery_autocomplete_build(
			'#material_inventory_assembly_form_target_c_businesspartner_id_' + material_inventory_assembly_assemblytarget_row_id.toString(),
			"<?php echo site_url('material/inventory_assembly/get_businesspartner_autocomplete_list_json');?>",
			{
				width : 150
			}
		);
		
		var product_conditions = <?php echo json_encode($product_conditions);?>;
		var product_condition_select = $('#material_inventory_assembly_form_target_condition_' + material_inventory_assembly_assemblytarget_row_id.toString());
		$.each(product_conditions, function(key, value){   
			product_condition_select
				.append($("<option></option>")
					.attr("value", key)
					.text(value)
				); 
		});
		product_condition_select.val(condition);
		
		$('#material_inventory_assembly_form_target_packed_date_' + material_inventory_assembly_assemblytarget_row_id.toString()).datepicker();
		jquery_field_set('#material_inventory_assembly_form_target_packed_date_' + material_inventory_assembly_assemblytarget_row_id.toString(), packed_date);
		
		$('#material_inventory_assembly_form_target_expired_date_' + material_inventory_assembly_assemblytarget_row_id.toString()).datepicker();
		jquery_field_set('#material_inventory_assembly_form_target_expired_date_' + material_inventory_assembly_assemblytarget_row_id.toString(), expired_date);
	}
}

function material_inventory_assembly_form_submit(on_success){
	material_inventory_assembly_on_sucess = on_success;
	jQuery('#material_inventory_assembly_form').submit();
}
</script>