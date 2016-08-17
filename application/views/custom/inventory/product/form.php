<?php 
echo form_open($form_action.(!empty($record) ? '/'.$record->id : ''),
	array(
		'name'	=> 'custom_inventory_product_form',
		'id'	=> 'custom_inventory_product_form'
	)
);?>
	<table class="form-table">
		<tbody>
			<tr>
				<th width="120"><label for="custom_inventory_product_form_sku">SKU</label></th>
				<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'sku',
		'id' 	=> 'custom_inventory_product_form_sku',
		'class'	=> 'required',
		'value'	=> (!empty($record) ? $record->sku : '')
	)
);?>
				</td>
			</tr>
			<tr>
				<th><label for="custom_inventory_product_form_description">Description</label></th>
				<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'description',
		'id' 	=> 'custom_inventory_product_form_description',
		'class'	=> 'required',
		'value'	=> (!empty($record) ? $record->description : '')
	)
);?>
				</td>
			</tr>
			<tr>
				<th><label for="custom_inventory_product_form_barcode_length">Barcode Length</label></th>
				<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'barcode_length',
		'id' 	=> 'custom_inventory_product_form_barcode_length',
		'class'	=> 'required number',
		'value'	=> (!empty($record) ? $record->barcode_length : '')
	)
);?>
				</td>
			</tr>
			<tr>
				<th><label for="custom_inventory_product_form_qty_start">Qty Start</label></th>
				<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'qty_start',
		'id' 	=> 'custom_inventory_product_form_qty_start',
		'class'	=> 'required number',
		'value'	=> (!empty($record) ? $record->qty_start : '')
	)
);?>
				</td>
			</tr>
			<tr>
				<th><label for="custom_inventory_product_form_qty_end">Qty End</label></th>
				<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'qty_end',
		'id' 	=> 'custom_inventory_product_form_qty_end',
		'class'	=> 'required number',
		'value'	=> (!empty($record) ? $record->qty_end : '')
	)
);?>
				</td>
			</tr>
			<tr>
				<th><label for="custom_inventory_product_form_sku_start">SKU Start</label></th>
				<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'sku_start',
		'id' 	=> 'custom_inventory_product_form_sku_start',
		'class'	=> 'required number',
		'value'	=> (!empty($record) ? $record->sku_start : '')
	)
);?>
				</td>
			</tr>
			<tr>
				<th><label for="custom_inventory_product_form_sku_end">SKU End</label></th>
				<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'sku_end',
		'id' 	=> 'custom_inventory_product_form_sku_end',
		'class'	=> 'required number',
		'value'	=> (!empty($record) ? $record->sku_end : '')
	)
);?>
				</td>
			</tr>
			<tr>
				<th><label for="custom_inventory_product_form_carton_start">Carton Start</label></th>
				<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'carton_start',
		'id' 	=> 'custom_inventory_product_form_carton_start',
		'class'	=> 'required number',
		'value'	=> (!empty($record) ? $record->carton_start : '')
	)
);?>
				</td>
			</tr>
			<tr>
				<th><label for="custom_inventory_product_form_carton_end">Carton End</label></th>
				<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'carton_end',
		'id' 	=> 'custom_inventory_product_form_carton_end',
		'class'	=> 'required number',
		'value'	=> (!empty($record) ? $record->carton_end : '')
	)
);?>
				</td>
			</tr>
			
			<tr>
				<th><label for="custom_inventory_product_form_date_packed_start">Date Packed Start</label></th>
				<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'date_packed_start',
		'id' 	=> 'custom_inventory_product_form_date_packed_start',
		'class'	=> 'required number',
		'value'	=> (!empty($record) ? $record->date_packed_start : '')
	)
);?>
				</td>
			</tr>
			<tr>
				<th><label for="custom_inventory_product_form_date_packed_end">Date Packed End</label></th>
				<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'date_packed_end',
		'id' 	=> 'custom_inventory_product_form_date_packed_end',
		'class'	=> 'required number',
		'value'	=> (!empty($record) ? $record->date_packed_end : '')
	)
);?>
				</td>
			</tr>
		</tbody>
	</table>
<?php echo form_close();?>

<script type="text/javascript">
var custom_inventory_product_on_sucess;

jQuery(function(){
	jQuery("#custom_inventory_product_form").validate({
		submitHandler: function(form){
			jQuery("#custom_inventory_product_form").ajaxSubmit({
				dataType: "json",
				async : false,
				error: jquery_ajax_error_handler,
				beforeSend: function(jqXHR, settings){
					jquery_blockui();
				},
				success: function(data, textStatus, jqXHR){
					custom_inventory_product_on_sucess(data, textStatus, jqXHR);
				},
				complete: function(jqXHR, textStatus){
					jquery_unblockui();
				}
			});
		}
	});
});

function custom_inventory_product_form_submit(on_success){
	custom_inventory_product_on_sucess = on_success;
	jQuery('#custom_inventory_product_form').submit();
}
</script>