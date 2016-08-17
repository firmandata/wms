<?php
$inventory_invoice_calculates = array_merge(array('' => ''), $this->config->item('inventory_invoice_calculates'));

echo form_open($form_action.(!empty($record) ? '/'.$record->id : ''),
	array(
		'name'	=> 'material_inventory_invoice_form',
		'id'	=> 'material_inventory_invoice_form'
	)
);?>
<table>
	<tr>
		<td valign="top">
			<table class="form-table">
				<tbody>
					<tr>
						<th width="100"><label for="material_inventory_invoice_form_code">No</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'code',
		'id' 	=> 'material_inventory_invoice_form_code',
		'class'	=> (!empty($record) ? 'required' : ''),
		'style'	=> 'width:120px;',
		'value'	=> (!empty($record) ? $record->code : ''),
		'placeholder'	=> '## Auto ##'
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_inventory_invoice_form_invoice_date">Date</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'invoice_date',
		'id' 	=> 'material_inventory_invoice_form_invoice_date',
		'class'	=> 'required date',
		'style'	=> 'width:75px;',
		'value'	=> (!empty($record->invoice_date) ? date($this->config->item('server_display_date_format'), strtotime($record->invoice_date)) : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_inventory_invoice_form_period_from">Period</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'period_from',
		'id' 	=> 'material_inventory_invoice_form_period_from',
		'class'	=> 'required date',
		'style'	=> 'width:75px;',
		'value'	=> (!empty($record->period_from) ? date($this->config->item('server_display_date_format'), strtotime($record->period_from)) : '')
	)
);?>
							<label for="material_inventory_invoice_form_period_to">To</label>
<?php 
echo form_input(
	array(
		'name' 	=> 'period_to',
		'id' 	=> 'material_inventory_invoice_form_period_to',
		'class'	=> 'required date',
		'style'	=> 'width:75px;',
		'value'	=> (!empty($record->period_to) ? date($this->config->item('server_display_date_format'), strtotime($record->period_to)) : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_inventory_invoice_form_invoice_handling">Model</label></th>
						<td>
							<table>
								<thead>
									<tr>
										<th colspan="2">&nbsp;</th>
										<th style="text-align:center !important;">Price Per Unit</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>
<?php 
echo form_checkbox(
	array(
		'name'		=> 'invoice_handling_in',
		'id'		=> 'material_inventory_invoice_form_invoice_handling_in',
		'value'		=> '1',
		'checked'	=> (!empty($record) ? $record->invoice_handling_in : 0)
	)
);?>
										</td>
										<td><label for="material_inventory_invoice_form_invoice_handling_in">Handling In</label></td>
										<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'invoice_handling_in_price',
		'id' 		=> 'material_inventory_invoice_form_invoice_handling_in_price',
		'class'		=> 'number',
		'value'		=> (!empty($record) ? $record->invoice_handling_in_price : 0),
		'style'		=> "width:100px"
	)
);?>
										</td>
									</tr>
									<tr>
										<td>
<?php 
echo form_checkbox(
	array(
		'name'		=> 'invoice_handling_out',
		'id'		=> 'material_inventory_invoice_form_invoice_handling_out',
		'value'		=> '1',
		'checked'	=> (!empty($record) ? $record->invoice_handling_out : 0)
	)
);?>
										</td>
										<td><label for="material_inventory_invoice_form_invoice_handling_out">Handling Out</label></td>
										<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'invoice_handling_out_price',
		'id' 		=> 'material_inventory_invoice_form_invoice_handling_out_price',
		'class'		=> 'number',
		'value'		=> (!empty($record) ? $record->invoice_handling_out_price : 0),
		'style'		=> "width:100px"
	)
);?>
										</td>
									</tr>
									<tr>
										<td>
<?php 
echo form_checkbox(
	array(
		'name'		=> 'invoice_handling_storage',
		'id'		=> 'material_inventory_invoice_form_invoice_handling_storage',
		'value'		=> '1',
		'checked'	=> (!empty($record) ? $record->invoice_handling_storage : 0)
	)
);?>
										</td>
										<td><label for="material_inventory_invoice_form_invoice_handling_storage">Storage (Recurring)</label></td>
										<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'invoice_handling_storage_price',
		'id' 		=> 'material_inventory_invoice_form_invoice_handling_storage_price',
		'class'		=> 'number',
		'value'		=> (!empty($record) ? $record->invoice_handling_storage_price : 0),
		'style'		=> "width:100px"
	)
);?>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<th><label for="material_inventory_invoice_form_invoice_calculate">Calculate</label></th>
						<td>
<?php 
echo form_dropdown('invoice_calculate', $inventory_invoice_calculates, (!empty($record) ? $record->invoice_calculate : ''), 'id="material_inventory_invoice_form_invoice_calculate"');?>
						</td>
					</tr>
					<tr>
						<th><label for="material_inventory_invoice_form_c_businesspartner_caption">Business Partner</label></th>
						<td><input type="hidden" name="c_businesspartner_id" id="material_inventory_invoice_form_c_businesspartner_id" class="required" value="<?php echo (!empty($record) ? $record->c_businesspartner_id : '');?>" data-text="<?php echo (!empty($record) ? $record->c_businesspartner_text : '');?>"/></td>
					</tr>
				</tbody>
			</table>
		</td>
		<td valign="top">
			<table class="form-table">
				<tbody>
					<tr>
						<th width="100"><label for="material_inventory_invoice_form_jo_no">J/O No</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'jo_no',
		'id' 	=> 'material_inventory_invoice_form_jo_no',
		'value'	=> (!empty($record) ? $record->jo_no : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_inventory_invoice_form_term_of_payment">Term Of Payment</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'term_of_payment',
		'id' 	=> 'material_inventory_invoice_form_term_of_payment',
		'value'	=> (!empty($record) ? $record->term_of_payment : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_inventory_invoice_form_tax">Tax</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 		=> 'tax',
		'id' 		=> 'material_inventory_invoice_form_tax',
		'class'		=> 'required number',
		'value'		=> (!empty($record) ? $record->tax : ''),
		'style'		=> "width:50px"
	)
);?>
							%
						</td>
					</tr>
					<tr>
						<th><label for="material_inventory_invoice_form_reference">Reference</label></th>
						<td>
<?php 
echo form_textarea(
	array(
		'name' 	=> 'reference',
		'id' 	=> 'material_inventory_invoice_form_reference',
		'value'	=> (!empty($record) ? $record->reference : ''),
		'cols'	=> 30, 'rows'	=> 2
	)
);?>
						</td>
					</tr>
				</tbody>
			</table>
		</td>
	</tr>
	<tr>
		<td valign="top">
			<table class="form-table">
				<thead>
					<tr>
						<td colspan="2" class="form-table-title">Shipment Details</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th width="100"><label for="material_inventory_invoice_form_plate_no">Plate No.</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'plate_no',
		'id' 	=> 'material_inventory_invoice_form_plate_no',
		'value'	=> (!empty($record) ? $record->plate_no : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_inventory_invoice_form_si_sj_so_no">SI/SJ/SO No.</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'si_sj_so_no',
		'id' 	=> 'material_inventory_invoice_form_si_sj_so_no',
		'value'	=> (!empty($record) ? $record->si_sj_so_no : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_inventory_invoice_form_spk_po_no">SPK/PO No.</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'spk_po_no',
		'id' 	=> 'material_inventory_invoice_form_spk_po_no',
		'value'	=> (!empty($record) ? $record->spk_po_no : '')
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
						<td colspan="2" class="form-table-title">Bank</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th width="100"><label for="material_inventory_invoice_form_bank_ac_name">Beneficiary Name</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'bank_ac_name',
		'id' 	=> 'material_inventory_invoice_form_bank_ac_name',
		'value'	=> (!empty($record) ? $record->bank_ac_name : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_inventory_invoice_form_bank_ac_no">A/C No</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'bank_ac_no',
		'id' 	=> 'material_inventory_invoice_form_bank_ac_no',
		'value'	=> (!empty($record) ? $record->bank_ac_no : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_inventory_invoice_form_bank_name">Bank</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'bank_name',
		'id' 	=> 'material_inventory_invoice_form_bank_name',
		'value'	=> (!empty($record) ? $record->bank_name : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_inventory_invoice_form_bank_branch">Branch</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'bank_branch',
		'id' 	=> 'material_inventory_invoice_form_bank_branch',
		'value'	=> (!empty($record) ? $record->bank_branch : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_inventory_invoice_form_bank_swift_code">Swift Code</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'bank_swift_code',
		'id' 	=> 'material_inventory_invoice_form_bank_swift_code',
		'value'	=> (!empty($record) ? $record->bank_swift_code : '')
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
var material_inventory_invoice_on_sucess;

jQuery(function(){
	jquery_ready_load();
	
	jquery_autocomplete_build("#material_inventory_invoice_form_c_businesspartner_id", "<?php echo site_url('material/inventory_invoice/get_businesspartner_autocomplete_list_json');?>", {
		width : 250
	});
	
	jQuery("#material_inventory_invoice_form").validate({
		submitHandler: function(form){
			jQuery("#material_inventory_invoice_form").ajaxSubmit({
				dataType: "json",
				async : false,
				error: jquery_ajax_error_handler,
				beforeSend: function(jqXHR, settings){
					jquery_blockui();
				},
				success: function(data, textStatus, jqXHR){
					material_inventory_invoice_on_sucess(data, textStatus, jqXHR);
				},
				complete: function(jqXHR, textStatus){
					jquery_unblockui();
				}
			});
		}
	});
});

function material_inventory_invoice_form_submit(on_success){
	material_inventory_invoice_on_sucess = on_success;
	jQuery('#material_inventory_invoice_form').submit();
}
</script>