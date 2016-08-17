<?php
$product_uoms = array_merge(array('' => ''), $this->config->item('product_uoms'));
$product_origins = array_merge(array('' => ''), $this->config->item('product_origins'));
$product_types = array_merge(array('' => ''), $this->config->item('product_types'));

echo form_open($form_action.(!empty($record) ? '/'.$record->id : ''),
	array(
		'name'	=> 'material_product_form',
		'id'	=> 'material_product_form'
	)
);?>
	<div id="material_product_form_tabs">
		<ul>
			<li><a href="#material_product_form_tab_general">General</a></li>
			<li><a href="#material_product_form_tab_scan_config">Barcode Configuration</a></li>
		</ul>
		<div id="material_product_form_tab_general">
			<table class="form-table">
				<thead>
					<tr>
						<td colspan="2" class="form-table-title">Information</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th width="120"><label for="material_product_form_code">Code</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'code',
		'id' 	=> 'material_product_form_code',
		'class'	=> 'required',
		'value'	=> (!empty($record) ? $record->code : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_product_form_name">Name</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'name',
		'id' 	=> 'material_product_form_name',
		'class'	=> 'required',
		'value'	=> (!empty($record) ? $record->name : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_product_form_m_productgroup_id_caption">Zone</label></th>
						<td><input type="hidden" name="m_productgroup_id" id="material_product_form_m_productgroup_id" value="<?php echo (!empty($record) ? $record->m_productgroup_id : '');?>" data-text="<?php echo (!empty($record) ? $record->m_productgroup_text : '');?>"/></td>
					</tr>
					<tr>
						<th><label for="material_product_form_uom">UOM</label></th>
						<td>
<?php 
echo form_dropdown('uom', $product_uoms, (!empty($record) ? $record->uom : ''), 'id="material_product_form_uom"');?>
						</td>
					</tr>
					<tr>
						<th><label for="material_product_form_pack">Pack</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'pack',
		'id' 	=> 'material_product_form_pack',
		'class'	=> 'required number',
		'value'	=> (!empty($record) ? $record->pack : '0')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_product_form_origin">Origin</label></th>
						<td>
<?php 
echo form_dropdown('origin', $product_origins, (!empty($record) ? $record->origin : ''), 'id="material_product_form_origin"');?>
						</td>
					</tr>
					<tr>
						<th><label for="material_product_form_type">Type</label></th>
						<td>
<?php 
echo form_dropdown('type', $product_types, (!empty($record) ? $record->type : ''), 'id="material_product_form_type"');?>
						</td>
					</tr>
					<tr>
						<th><label for="material_product_form_brand">Brand</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'brand',
		'id' 	=> 'material_product_form_brand',
		'value'	=> (!empty($record) ? $record->brand : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_product_form_netto">Netto</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'netto',
		'id' 	=> 'material_product_form_netto',
		'class'	=> 'required number',
		'value'	=> (!empty($record) ? $record->netto : '0')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_product_form_minimum_stock">Minimum Stock</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'minimum_stock',
		'id' 	=> 'material_product_form_minimum_stock',
		'class'	=> 'required number',
		'value'	=> (!empty($record) ? $record->minimum_stock : '0')
	)
);?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div id="material_product_form_tab_scan_config">
			<table class="form-table">
				<thead>
					<tr>
						<td colspan="2" class="form-table-title">Scan Parameters</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th><label for="material_product_form_barcode_length">Barcode Length</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'barcode_length',
		'id' 	=> 'material_product_form_barcode_length',
		'class'	=> 'required number',
		'value'	=> (!empty($record) ? $record->c_barcode_length : '0'),
		'style'	=> 'width:35px; text-align:center;'
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_product_form_quantity_start">Quantity</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'quantity_start',
		'id' 	=> 'material_product_form_quantity_start',
		'class'	=> 'required number',
		'value'	=> (!empty($record) ? $record->c_quantity_start : '0'),
		'style'	=> 'width:35px; text-align:center;'
	)
);?>
							To 
<?php 
echo form_input(
	array(
		'name' 	=> 'quantity_end',
		'id' 	=> 'material_product_form_quantity_end',
		'class'	=> 'required number',
		'value'	=> (!empty($record) ? $record->c_quantity_end : '0'),
		'style'	=> 'width:35px; text-align:center;'
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_product_form_quantity_start">Quantity Point</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'quantity_point_start',
		'id' 	=> 'material_product_form_quantity_point_start',
		'class'	=> 'required number',
		'value'	=> (!empty($record) ? $record->c_quantity_point_start : '0'),
		'style'	=> 'width:35px; text-align:center;'
	)
);?>
							To 
<?php 
echo form_input(
	array(
		'name' 	=> 'quantity_point_end',
		'id' 	=> 'material_product_form_quantity_point_end',
		'class'	=> 'required number',
		'value'	=> (!empty($record) ? $record->c_quantity_point_end : '0'),
		'style'	=> 'width:35px; text-align:center;'
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_product_form_quantity_divider">Quantity Divider</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'quantity_divider',
		'id' 	=> 'material_product_form_quantity_divider',
		'class'	=> 'required number',
		'value'	=> (!empty($record) ? $record->c_quantity_divider : '0'),
		'style'	=> 'width:70px; text-align:right;'
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_product_form_sku_start">SKU</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'sku_start',
		'id' 	=> 'material_product_form_sku_start',
		'class'	=> 'required number',
		'value'	=> (!empty($record) ? $record->c_sku_start : '0'),
		'style'	=> 'width:35px; text-align:center;'
	)
);?>
							To 
<?php 
echo form_input(
	array(
		'name' 	=> 'sku_end',
		'id' 	=> 'material_product_form_sku_end',
		'class'	=> 'required number',
		'value'	=> (!empty($record) ? $record->c_sku_end : '0'),
		'style'	=> 'width:35px; text-align:center;'
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_product_form_carton_start">Carton</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'carton_start',
		'id' 	=> 'material_product_form_carton_start',
		'class'	=> 'required number',
		'value'	=> (!empty($record) ? $record->c_carton_start : '0'),
		'style'	=> 'width:35px; text-align:center;'
	)
);?>
							To 
<?php 
echo form_input(
	array(
		'name' 	=> 'carton_end',
		'id' 	=> 'material_product_form_carton_end',
		'class'	=> 'required number',
		'value'	=> (!empty($record) ? $record->c_carton_end : '0'),
		'style'	=> 'width:35px; text-align:center;'
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_product_form_packed_date_start">Packed Date</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'packed_date_start',
		'id' 	=> 'material_product_form_packed_date_start',
		'class'	=> 'required number',
		'value'	=> (!empty($record) ? $record->c_packed_date_start : '0'),
		'style'	=> 'width:35px; text-align:center;'
	)
);?>
							To 
<?php 
echo form_input(
	array(
		'name' 	=> 'packed_date_end',
		'id' 	=> 'material_product_form_packed_date_end',
		'class'	=> 'required number',
		'value'	=> (!empty($record) ? $record->c_packed_date_end : '0'),
		'style'	=> 'width:35px; text-align:center;'
	)
);?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
<?php echo form_close();?>

<script type="text/javascript">
var material_product_on_sucess;

jQuery(function(){
	$("#material_product_form_tabs").tabs();
	
	jQuery("#material_product_form").validate({
		submitHandler: function(form){
			jQuery("#material_product_form").ajaxSubmit({
				dataType: "json",
				async : false,
				error: jquery_ajax_error_handler,
				beforeSend: function(jqXHR, settings){
					jquery_blockui();
				},
				success: function(data, textStatus, jqXHR){
					material_product_on_sucess(data, textStatus, jqXHR);
				},
				complete: function(jqXHR, textStatus){
					jquery_unblockui();
				}
			});
		}
	});
	
	jquery_autocomplete_build("#material_product_form_m_productgroup_id", "<?php echo site_url('material/product/get_productgroup_autocomplete_list_json');?>", {
		width : 200
	},
	{
		autoFocus : false
	});
});

function material_product_form_submit(on_success){
	material_product_on_sucess = on_success;
	jQuery('#material_product_form').submit();
}
</script>