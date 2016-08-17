<?php 
$transport_modes = array_merge(array('' => ''), $this->config->item('transport_modes'));
$product_conditions = $this->config->item('product_conditions');

echo form_open($form_action.(!empty($record) ? '/'.$record->id : ''),
	array(
		'name'	=> 'material_inventory_receive_form',
		'id'	=> 'material_inventory_receive_form'
	)
);?>
<table>
	<tr><td valign="top">
			<table class="form-table">
				<thead>
					<tr>
						<td colspan="2" class="form-table-title">Information</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th><label for="material_inventory_receive_form_code">No</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'code',
		'id' 	=> 'material_inventory_receive_form_code',
		'class'	=> 'required',
		'style'	=> 'width:120px;',
		'value'	=> (!empty($record) ? $record->code : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_inventory_receive_form_receive_date">Date</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'receive_date',
		'id' 	=> 'material_inventory_receive_form_receive_date',
		'class'	=> 'required date',
		'style'	=> 'width:75px;',
		'value'	=> (!empty($record->receive_date) ? date($this->config->item('server_display_date_format'), strtotime($record->receive_date)) : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_inventory_receive_form_notes">Notes</label></th>
						<td>
<?php 
echo form_textarea(
	array(
		'name' 	=> 'notes',
		'id' 	=> 'material_inventory_receive_form_notes',
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
						<td colspan="2" class="form-table-title">Mis. Information</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th><label for="material_inventory_receive_form_vehicle_no">Vehicle No</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'vehicle_no',
		'id' 	=> 'material_inventory_receive_form_vehicle_no',
		'value'	=> (!empty($record) ? $record->vehicle_no : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_inventory_receive_form_vehicle_driver">Driver</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'vehicle_driver',
		'id' 	=> 'material_inventory_receive_form_vehicle_driver',
		'value'	=> (!empty($record) ? $record->vehicle_driver : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_inventory_receive_form_transport_mode">Transport Mode</label></th>
						<td>
<?php 
echo form_dropdown('transport_mode', $transport_modes, (!empty($record) ? $record->transport_mode : ''), 'id="material_inventory_receive_form_transport_mode"');?>
						</td>
					</tr>
				</tbody>
			</table>
		</td>
	</tr>
</table>
<br/>
<table id="material_inventory_receive_receivedetail_list_table" class="table-data ui-widget ui-widget-content ui-corner-all" width="100%" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<th class="ui-state-default ui-corner-tl" width="22px">&nbsp;</th>
			<th class="ui-state-default">Order In Product</th>
			<th class="ui-state-default">Box</th>
			<th class="ui-state-default">Quantity</th>
			<th class="ui-state-default">Condition</th>
			<th class="ui-state-default">Notes</th>
		</tr>
	</thead>
	<tbody></tbody>
	<tfoot>
		<tr>
			<td colspan="6" class="ui-state-default ui-corner-bl ui-corner-br">
				<button id="material_inventory_receive_receivedetail_list_table_add" class="table-data-button">Add</button>
			</td>
		</tr>
	</tfoot>
</table>
<?php echo form_close();?>

<script type="text/javascript">
var material_inventory_receive_on_sucess;
var material_inventory_receive_receivedetail_row_id = 0;

jQuery(function(){
	jquery_ready_load();
	
	jQuery("#material_inventory_receive_form").validate({
		submitHandler: function(form){
			jQuery("#material_inventory_receive_form").ajaxSubmit({
				dataType: "json",
				async : false,
				error: jquery_ajax_error_handler,
				beforeSend: function(jqXHR, settings){
					jquery_blockui();
				},
				success: function(data, textStatus, jqXHR){
					material_inventory_receive_on_sucess(data, textStatus, jqXHR);
				},
				complete: function(jqXHR, textStatus){
					jquery_unblockui();
				}
			});
		}
	});
	
	jQuery('#material_inventory_receive_receivedetail_list_table_add').button({
		text: false,
		icons: {
			primary: "ui-icon-plus"
		}
	})
	.click(function(e){
		e.preventDefault();
		material_inventory_receive_form_receivedetail_add(
			null, null, null, 0, 1, 1, null, null
		);
		
		jQuery('#material_inventory_receive_form_detail_c_orderindetail_id_' + material_inventory_receive_receivedetail_row_id.toString() + '_caption').focus();
	});
	
	jQuery.each(<?php echo !empty($m_inventory_receivedetails) ? json_encode($m_inventory_receivedetails) : '[]';?>, function(receivedetail_idx, receivedetail){
		material_inventory_receive_form_receivedetail_add(
			receivedetail.id, receivedetail.c_orderindetail_id, receivedetail.m_product_text, receivedetail.m_product_netto, receivedetail.quantity_box, receivedetail.quantity, receivedetail.condition, receivedetail.notes
		);
	});
	
<?php
if (empty($m_inventory_receivedetails))
{?>
	material_inventory_receive_form_receivedetail_add(
		null, null, null, 0, 1, 1, null, null
	);
<?php
}?>
});

function material_inventory_receive_form_receivedetail_add(
	id, c_orderindetail_id, m_product_text, m_product_netto, quantity_box, quantity, condition, notes
){
	material_inventory_receive_receivedetail_row_id++;
	
	$('#material_inventory_receive_receivedetail_list_table tbody').append(
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
			$("<td>", {'class':'ui-widget-content', 'align':'center'}).append(
				$("<input>", {type:'hidden', name:'m_inventory_receivedetails[' + material_inventory_receive_receivedetail_row_id.toString() + '][id]', id:'material_inventory_receive_form_detail_id_' + material_inventory_receive_receivedetail_row_id.toString()})
					.val(id)
			).append(
				$("<input>", {type:'hidden', name:'m_inventory_receivedetails[' + material_inventory_receive_receivedetail_row_id.toString() + '][c_orderindetail_id]', id:'material_inventory_receive_form_detail_c_orderindetail_id_' + material_inventory_receive_receivedetail_row_id.toString(), 'class':'required'})
					.val(c_orderindetail_id).attr('data-text', m_product_text)
			).append(
				$("<input>", {type:'hidden', name:'m_inventory_receivedetails[' + material_inventory_receive_receivedetail_row_id.toString() + '][m_product_netto]', id:'material_inventory_receive_form_detail_m_product_netto_' + material_inventory_receive_receivedetail_row_id.toString()})
					.val(m_product_netto)
			)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center'}).append(
				$("<input>", {type:'text', name:'m_inventory_receivedetails[' + material_inventory_receive_receivedetail_row_id.toString() + '][quantity_box]', id:'material_inventory_receive_form_detail_quantity_box_' + material_inventory_receive_receivedetail_row_id.toString(), 'class':'required number'})
					.attr('data-index', material_inventory_receive_receivedetail_row_id)
					.width(40).val(quantity_box).css('text-align', 'right')
					.change(function(){
						material_inventory_receive_form_quantity_calculate(jQuery(this).attr('data-index'));
					})
			)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center'}).append(
				$("<input>", {type:'text', name:'m_inventory_receivedetails[' + material_inventory_receive_receivedetail_row_id.toString() + '][quantity]', id:'material_inventory_receive_form_detail_quantity_' + material_inventory_receive_receivedetail_row_id.toString(), 'class':'required number'})
					.width(40).val(quantity).css('text-align', 'right')
			)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center'}).append(
				$("<select>", {name:'m_inventory_receivedetails[' + material_inventory_receive_receivedetail_row_id.toString() + '][condition]', id:'material_inventory_receive_form_detail_condition_' + material_inventory_receive_receivedetail_row_id.toString(), 'class':'required'})
					.width(100)
			)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center'}).append(
				$("<input>", {type:'text', name:'m_inventory_receivedetails[' + material_inventory_receive_receivedetail_row_id.toString() + '][notes]', id:'material_inventory_receive_form_detail_notes_' + material_inventory_receive_receivedetail_row_id.toString()})
					.width(200).val(notes)
			)
		)
	);
	
	var product_conditions = <?php echo json_encode($product_conditions);?>;
	var product_condition_select = $('#material_inventory_receive_form_detail_condition_' + material_inventory_receive_receivedetail_row_id.toString());
	$.each(product_conditions, function(key, value){   
		product_condition_select
			.append($("<option></option>")
				.attr("value", key)
				.text(value)
			); 
	});
	product_condition_select.val(condition);
	
	material_inventory_receive_form_select(material_inventory_receive_receivedetail_row_id);
}

function material_inventory_receive_form_select(idx){
	jquery_autocomplete_build('#material_inventory_receive_form_detail_c_orderindetail_id_' + idx.toString(), "<?php echo site_url('material/inventory_receive/get_product_autocomplete_list_json');?>", {
		width : 350
	}, {
		change: function(event, ui){
			if (ui.item == null)
			{
				$(this).val('');
				$('#material_inventory_receive_form_detail_c_orderindetail_id_' + idx.toString()).val('');
				$('#material_inventory_receive_form_detail_m_product_netto_' + idx.toString()).val(0);
				$('#material_inventory_receive_form_detail_quantity_box_' + idx.toString()).val(1);
				$('#material_inventory_receive_form_detail_quantity_' + idx.toString()).val(1);
			}
			else
			{
				$('#material_inventory_receive_form_detail_c_orderindetail_id_' + idx.toString()).val(ui.item.id);
				$('#material_inventory_receive_form_detail_m_product_netto_' + idx.toString()).val(ui.item.netto);
				$('#material_inventory_receive_form_detail_quantity_box_' + idx.toString()).val(ui.item.quantity_box);
				$('#material_inventory_receive_form_detail_quantity_' + idx.toString()).val(ui.item.quantity);
			}
			material_inventory_receive_form_quantity_calculate(idx);
		}
	});
}

function material_inventory_receive_form_quantity_calculate(idx){
	var netto = parseFloat($('#material_inventory_receive_form_detail_m_product_netto_' + idx.toString()).val());
	if (netto > 0)
	{
		var quantity_box = parseFloat($('#material_inventory_receive_form_detail_quantity_box_' + idx.toString()).val());
		$('#material_inventory_receive_form_detail_quantity_' + idx.toString()).val(netto * quantity_box);
	}
}

function material_inventory_receive_form_submit(on_success){
	material_inventory_receive_on_sucess = on_success;
	jQuery('#material_inventory_receive_form').submit();
}
</script>