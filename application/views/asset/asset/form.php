<?php
$currencies = $this->config->item('currencies');
$depreciation_period_types = $this->config->item('depreciation_period_type');
$asset_types = $this->config->item('asset_types');

echo form_open($form_action.(!empty($record) ? '/'.$record->id : ''),
	array(
		'name'	=> 'asset_asset_form',
		'id'	=> 'asset_asset_form'
	)
);?>
<div id="asset_asset_form_tabs">
	<ul>
		<li><a href="#asset_asset_form_tab_general">General</a></li>
		<li><a href="#asset_asset_form_tab_depreciation">Depreciation</a></li>
	</ul>
	<div id="asset_asset_form_tab_general">
		<table width="100%">
			<tr>
				<td valign="top">
					<table class="form-table">
						<thead>
							<tr>
								<td colspan="2" class="form-table-title">General</td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th width="80"><label for="asset_asset_form_code">Code</label></th>
								<td>
<?php 
echo form_input(
	array(
		'name' 			=> 'code',
		'id' 			=> 'asset_asset_form_code',
		'class'			=> (!empty($record) ? 'required' : ''),
		'placeholder'	=> "(Auto)",
		'readonly'		=> 'readonly',
		'value'			=> (!empty($record) ? $record->code : '')
	)
);?>
								</td>
							</tr>
							<tr>
								<th><label for="asset_asset_form_m_product_id_caption">Product</label></th>
								<td><input type="hidden" name="m_product_id" id="asset_asset_form_m_product_id" class="required" value="<?php echo (!empty($record) ? $record->m_product_id : '');?>" data-text="<?php echo (!empty($record) ? $record->m_product_text : '');?>"/></td>
							</tr>
							<tr>
								<th><label for="asset_asset_form_name">Name</label></th>
								<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'name',
		'id' 	=> 'asset_asset_form_name',
		'class'	=> 'required',
		'style'	=> 'width: 300px;',
		'value'	=> (!empty($record) ? $record->name : '')
	)
);?>
								</td>
							</tr>
							<tr>
								<th><label for="asset_asset_form_type">Type</label></th>
								<td>
<?php 
echo form_dropdown('type', $asset_types, (!empty($record) ? $record->type : ''), 'id="asset_asset_form_type"');?>
								</td>
							</tr>
							<tr>
								<th><label for="asset_asset_form_voucher_no">Voucher No</label></th>
								<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'voucher_no',
		'id' 	=> 'asset_asset_form_voucher_no',
		'style'	=> 'width: 180px;',
		'value'	=> (!empty($record) ? $record->voucher_no : '')
	)
);?>
								</td>
							</tr>
							<tr>
								<th><label for="asset_asset_form_quantity">Quantity</label></th>
								<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'quantity',
		'id' 	=> 'asset_asset_form_quantity',
		'class'	=> 'required number',
		'value'	=> (!empty($record) ? $record->quantity : '1')
	)
);?>
								</td>
							</tr>
							<tr>
								<th><label for="asset_asset_form_notes">Notes</label></th>
								<td>
<?php 
echo form_textarea(
	array(
		'name' 	=> 'notes',
		'id' 	=> 'asset_asset_form_notes',
		'class'	=> '',
		'cols'	=> 30, 'rows' => 3,
		'value'	=> (!empty($record) ? $record->notes : '')
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
								<th><label for="asset_asset_form_c_region_id_caption">Region</label></th>
								<td><input type="hidden" name="c_region_id" id="asset_asset_form_c_region_id" class="required" value="<?php echo (!empty($record) ? $record->c_region_id : '');?>" data-text="<?php echo (!empty($record) ? $record->c_region_text : '');?>"/></td>
							</tr>
							<tr>
								<th><label for="asset_asset_form_c_department_id_caption">Department</label></th>
								<td><input type="hidden" name="c_department_id" id="asset_asset_form_c_department_id" class="required" value="<?php echo (!empty($record) ? $record->c_department_id : '');?>" data-text="<?php echo (!empty($record) ? $record->c_department_text : '');?>"/></td>
							</tr>
							<tr>
								<th><label for="asset_asset_form_c_location_id_caption">Location</label></th>
								<td><input type="hidden" name="c_location_id" id="asset_asset_form_c_location_id" class="required" value="<?php echo (!empty($record) ? $record->c_location_id : '');?>" data-text="<?php echo (!empty($record) ? $record->c_location_text : '');?>"/></td>
							</tr>
							<tr>
								<th><label for="asset_asset_form_c_businesspartner_user_id_caption">User</label></th>
								<td><input type="hidden" name="c_businesspartner_user_id" id="asset_asset_form_c_businesspartner_user_id" value="<?php echo (!empty($record) ? $record->c_businesspartner_user_id : '');?>" data-text="<?php echo (!empty($record) ? $record->c_businesspartner_user_text : '');?>"/></td>
							</tr>
						</tbody>
					</table>
					<table class="form-table">
						<thead>
							<tr>
								<td colspan="2" class="form-table-title">Purchase</td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th><label for="asset_asset_form_c_businesspartner_supplier_id_caption">Supplier</label></th>
								<td><input type="hidden" name="c_businesspartner_supplier_id" id="asset_asset_form_c_businesspartner_supplier_id" value="<?php echo (!empty($record) ? $record->c_businesspartner_supplier_id : '');?>" data-text="<?php echo (!empty($record) ? $record->c_businesspartner_supplier_text : '');?>"/></td>
							</tr>
							<tr>
								<th><label for="asset_asset_form_purchase_date">Date</label></th>
								<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'purchase_date',
		'id' 	=> 'asset_asset_form_purchase_date',
		'class'	=> 'required date',
		'style'	=> 'width:75px;',
		'value'	=> (!empty($record->purchase_date) ? date($this->config->item('server_display_date_format'), strtotime($record->purchase_date)) : '')
	)
);?>
								</td>
							</tr>
							<tr>
								<th><label for="asset_asset_form_purchase_price">Price</label></th>
								<td>
<?php 
echo form_dropdown('currency', $currencies, (!empty($record) ? $record->currency : ''));
echo form_input(
	array(
		'name' 	=> 'purchase_price',
		'id' 	=> 'asset_asset_form_purchase_price',
		'class'	=> 'required number',
		'value'	=> (!empty($record) ? $record->purchase_price : '0')
	)
);?>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
<?php
if (empty($record))
{?>
			<tr>
				<td>&nbsp;</td>
				<td style="border-top:1px solid #000000;">
					<table class="form-table">
						<tbody>
							<tr>
							<th><label for="asset_asset_form_quantity">Create Duplicate</label></th>
							<td>
<?php 
	echo form_input(
		array(
			'name' 	=> 'duplicate_count',
			'id' 	=> 'asset_asset_form_duplicate_count',
			'class'	=> 'required number',
			'style'	=> 'width:30px;',
			'value'	=> 0
		)
	);?>
							</td>
						</tr>
						</tbody>
					</table>
				</td>
			</tr>
<?php
}?>
		</table>
	</div>
	<div id="asset_asset_form_tab_depreciation">
		<table class="form-table" width="100%">
			<tbody>
				<tr>
					<th width="80"><label for="asset_asset_form_depreciation_period_type">Parameters</label></th>
					<td>
<?php 
echo form_dropdown('depreciation_period_type', $depreciation_period_types, (!empty($record) ? $record->depreciation_period_type : ''), 'id="asset_asset_form_depreciation_period_type"');
echo form_input(
	array(
		'name' 	=> 'depreciation_period_time',
		'id' 	=> 'asset_asset_form_depreciation_period_time',
		'class'	=> 'required number',
		'style'	=> 'width:40px;',
		'value'	=> (!empty($record) ? $record->depreciation_period_time : '0')
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
		'name'		=> 'is_editable_mode',
		'id'		=> 'asset_asset_form_is_editable_mode',
		'value'		=> 1,
		'checked'	=> FALSE,
		'style'		=> "vertical-align:middle;"
    )
);?>
						<label for="asset_asset_form_is_editable_mode">Editable Mode</label>
					</td>
				</tr>
			</tbody>
		</table>
		<div id="asset_asset_form_a_asset_amounts">
			<?php echo $a_assetamount_view;?>
		</div>
	</div>
</div>
*) If purchase date, purchase price or depreciation parameter changes, than depreciation list will be change too.
<?php echo form_close();?>

<script type="text/javascript">
var asset_asset_on_sucess;

jQuery(function(){
	jQuery("#asset_asset_form_tabs").tabs({
		activate: function(event, ui){
			switch (ui.newTab.context.hash)
			{
				case '#asset_asset_form_tab_depreciation':
					a_assetamount_global_scalled();
					break;
				default:
			}
		}
	});
	jquery_ready_load();
	
	jQuery("#asset_asset_form").validate({
		submitHandler: function(form){
			jQuery("#asset_asset_form").ajaxSubmit({
				dataType: "json",
				async : false,
				error: jquery_ajax_error_handler,
				beforeSend: function(jqXHR, settings){
					jquery_blockui();
				},
				success: function(data, textStatus, jqXHR){
					asset_asset_on_sucess(data, textStatus, jqXHR);
				},
				complete: function(jqXHR, textStatus){
					jquery_unblockui();
				}
			});
		}
	});
	
	jquery_autocomplete_build("#asset_asset_form_m_product_id", "<?php echo site_url('asset/asset/get_product_autocomplete_list_json');?>", {
		width : 200
	},
	{
		autoFocus : true
	});
	
	jquery_autocomplete_build("#asset_asset_form_c_region_id", "<?php echo site_url('asset/asset/get_region_autocomplete_list_json');?>", {
		width : 200
	},
	{
		autoFocus : true
	});
	
	jquery_autocomplete_build("#asset_asset_form_c_department_id", "<?php echo site_url('asset/asset/get_department_autocomplete_list_json');?>", {
		width : 200
	},
	{
		autoFocus : true
	});
	
	jquery_autocomplete_build("#asset_asset_form_c_location_id", "<?php echo site_url('asset/asset/get_location_autocomplete_list_json');?>", {
		width : 200
	},
	{
		autoFocus : true
	});
	
	jquery_autocomplete_build("#asset_asset_form_c_businesspartner_supplier_id", "<?php echo site_url('asset/asset/get_businesspartner_autocomplete_list_json/SUPPLIER');?>", {
		width : 200
	},
	{
		autoFocus : false
	});
	
	jquery_autocomplete_build("#asset_asset_form_c_businesspartner_user_id", "<?php echo site_url('asset/asset/get_businesspartner_autocomplete_list_json/EMPLOYEE');?>", {
		width : 200
	},
	{
		autoFocus : false
	});
	
	jQuery("#asset_asset_form_purchase_price,#asset_asset_form_purchase_date,#asset_asset_form_depreciation_period_type,#asset_asset_form_depreciation_period_time,#asset_asset_form_is_editable_mode").change(function(){
		asset_asset_form_load_asset_amounts();
	});
});

function asset_asset_form_submit(on_success){
	asset_asset_on_sucess = on_success;
	jQuery('#asset_asset_form').submit();
}

function asset_asset_form_load_asset_amounts(){
	jQuery.ajax({
		url: "<?php echo site_url('asset/asset/get_depreciation_calculator');?>",
		data: {
			is_editable_mode			: (jQuery('#asset_asset_form_is_editable_mode').is(":checked") ? 1 : 0),
			a_asset_id					: <?php echo (!empty($record) ? "'" + $record->id + "'" : 'null');?>,
			purchase_date				: asset_asset_form_get_date('asset_asset_form_purchase_date'),
			purchase_price				: jQuery('#asset_asset_form_purchase_price').val(),
			depreciation_period_type	: jQuery('#asset_asset_form_depreciation_period_type').val(),
			depreciation_period_time	: jQuery('#asset_asset_form_depreciation_period_time').val()
		},
		type: "GET",
		dataType: "html",
		async : false,
		error: jquery_ajax_error_handler,
		beforeSend: function(jqXHR, settings){
			jquery_blockui(jQuery('#asset_asset_form_a_asset_amounts'));
		},
		success: function(data, textStatus, jqXHR){
			jQuery('#asset_asset_form_a_asset_amounts').html(data);
		},
		complete: function(jqXHR, textStatus){
			jquery_unblockui(jQuery('#asset_asset_form_a_asset_amounts'));
		}
	});
}

function asset_asset_form_get_date(elem_id){
	var date = jQuery('#' + elem_id).val();
	if (jQuery.trim(date) != '')
	{
		var _date = new Date(getDateFromFormat(date, client_validate_date_format));
		date = formatDate(_date, server_client_parse_validate_date_format);
	}
	return date;
}
</script>