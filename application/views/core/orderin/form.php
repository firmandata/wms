<?php
$orderin_origins = array_merge(array('' => ''), $this->config->item('orderin_origins'));

echo form_open($form_action.(!empty($record) ? '/'.$record->id : ''),
	array(
		'name'	=> 'core_orderin_form',
		'id'	=> 'core_orderin_form'
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
						<th><label for="core_orderin_form_code">No</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'code',
		'id' 	=> 'core_orderin_form_code',
		'class'	=> (!empty($record) ? 'required' : ''),
		'style'	=> 'width:120px;',
		'value'	=> (!empty($record) ? $record->code : ''),
		'placeholder'	=> '## Auto ##'
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="core_orderin_form_c_businesspartner_caption">Business Partner</label></th>
						<td><input type="hidden" name="c_businesspartner_id" id="core_orderin_form_c_businesspartner_id" class="required" value="<?php echo (!empty($record) ? $record->c_businesspartner_id : '');?>" data-text="<?php echo (!empty($record) ? $record->c_businesspartner_text : '');?>"/></td>
					</tr>
					<tr>
						<th><label for="core_orderin_form_orderin_date">Date</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'orderin_date',
		'id' 	=> 'core_orderin_form_orderin_date',
		'class'	=> 'required date',
		'style'	=> 'width:75px;',
		'value'	=> (!empty($record->orderin_date) ? date($this->config->item('server_display_date_format'), strtotime($record->orderin_date)) : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="core_orderin_form_notes">Notes</label></th>
						<td>
<?php 
echo form_textarea(
	array(
		'name' 	=> 'notes',
		'id' 	=> 'core_orderin_form_notes',
		'value'	=> (!empty($record) ? $record->notes : ''),
		'cols'	=> 30, 'rows'	=> 2
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
		'name'		=> 'ppn',
		'id'		=> 'core_orderin_form_ppn',
		'value'		=> '0.1',
		'checked'	=> (!empty($record) ? ($record->ppn > 0 ? 1 : 0) : 0)
	)
);?>
						<label for="core_orderin_form_ppn">PPN (10%)</label>
						</td>
					</tr>
				</tbody>
			</table>
		</td>
		<td valign="top">
			<table class="form-table">
				<thead>
					<tr>
						<td colspan="2" class="form-table-title">Misc. Information</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th><label for="core_orderin_form_c_project_caption">Project</label></th>
						<td><input type="hidden" name="c_project_id" id="core_orderin_form_c_project_id" class="required" value="<?php echo (!empty($record) ? $record->c_project_id : '');?>" data-text="<?php echo (!empty($record) ? $record->c_project_text : '');?>"/></td>
					</tr>
					<tr>
						<th><label for="core_orderin_form_origin">Origin</label></th>
						<td>
<?php 
echo form_dropdown('origin', $orderin_origins, (!empty($record) ? $record->origin : ''), 'id="core_orderin_form_origin"');?>
						</td>
					</tr>
					<tr>
						<th><label for="core_orderin_form_bol_no">No Surat Jalan</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'bol_no',
		'id' 	=> 'core_orderin_form_bol_no',
		'value'	=> (!empty($record) ? $record->bol_no : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="core_orderin_form_external_no">External No</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'external_no',
		'id' 	=> 'core_orderin_form_external_no',
		'value'	=> (!empty($record) ? $record->external_no : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="core_orderin_form_signer">Signer</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'signer',
		'id' 	=> 'core_orderin_form_signer',
		'value'	=> (!empty($record) ? $record->signer : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="core_orderin_form_term">Term</label></th>
						<td>
<?php 
echo form_textarea(
	array(
		'name' 	=> 'term',
		'id' 	=> 'core_orderin_form_term',
		'value'	=> (!empty($record) ? $record->term : ''),
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
<br/>
<table id="core_orderin_orderindetail_list_table" class="table-data ui-widget ui-widget-content ui-corner-all" width="100%" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<th class="ui-state-default ui-corner-tl" width="22px">&nbsp;</th>
			<th class="ui-state-default">Product</th>
			<th class="ui-state-default">Qty Case</th>
			<th class="ui-state-default">Netto / Pack</th>
			<th class="ui-state-default">Qty Total</th>
			<th class="ui-state-default">UOM</th>
			<th class="ui-state-default">Discount (%)</th>
			<th class="ui-state-default">Price</th>
			<th class="ui-state-default">Amount</th>
			<th class="ui-state-default">Notes</th>
		</tr>
	</thead>
	<tbody></tbody>
	<tfoot>
		<tr>
			<td class="ui-state-default ui-corner-bl">
				<button id="core_orderin_orderindetail_list_table_add" class="table-data-button">Add</button>
			</td>
			<td colspan="7" class="ui-state-default" align="right">Total</td>
			<td class="ui-state-default" align="right" id="core_orderin_orderindetail_list_table_amount_total">0</td>
			<td class="ui-state-default ui-corner-br">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<?php echo form_close();?>

<script type="text/javascript">
var core_orderin_on_sucess;
var core_orderin_orderindetail_row_id = 0;

jQuery(function(){
	jquery_ready_load();
	
	jQuery("#core_orderin_form").validate({
		submitHandler: function(form){
			jQuery("#core_orderin_form").ajaxSubmit({
				dataType: "json",
				async : false,
				error: jquery_ajax_error_handler,
				beforeSend: function(jqXHR, settings){
					jquery_blockui();
				},
				success: function(data, textStatus, jqXHR){
					core_orderin_on_sucess(data, textStatus, jqXHR);
				},
				complete: function(jqXHR, textStatus){
					jquery_unblockui();
				}
			});
		}
	});
	
	jquery_autocomplete_build("#core_orderin_form_c_businesspartner_id", "<?php echo site_url('core/orderin/get_businesspartner_autocomplete_list_json');?>", {
		width : 250
	});
	
	jquery_autocomplete_build("#core_orderin_form_c_project_id", "<?php echo site_url('core/orderin/get_project_autocomplete_list_json');?>", {
		width : 200
	});
	
	jQuery('#core_orderin_orderindetail_list_table_add').button({
		text: false,
		icons: {
			primary: "ui-icon-plus"
		}
	})
	.click(function(e){
		e.preventDefault();
		core_orderin_form_orderindetail_add(
			  null, null, null, 0, 0, null
			, 1, 1
			, 0, 0
			, null
		);
		
		jQuery('#core_orderin_form_detail_m_product_id_' + core_orderin_orderindetail_row_id.toString() + '_caption').focus();
	});
	
	jQuery.each(<?php echo !empty($c_orderindetails) ? json_encode($c_orderindetails) : '[]';?>, function(orderindetail_idx, orderindetail){
		core_orderin_form_orderindetail_add(
			  orderindetail.id, orderindetail.m_product_id, orderindetail.m_product_text, orderindetail.m_product_netto, orderindetail.m_product_pack, orderindetail.m_product_uom
			, orderindetail.quantity_box, orderindetail.quantity
			, orderindetail.discount, orderindetail.price
			, orderindetail.notes
		);
	});
	
<?php
if (empty($c_orderindetails))
{?>
	core_orderin_form_orderindetail_add(
		  null, null, null, 0, 0, null
		, 1, 1
		, 0, 0
		, null
	);
<?php
}?>
});

function core_orderin_form_orderindetail_add(
	  id, m_product_id, m_product_text, m_product_netto, m_product_pack, m_product_uom
	, quantity_box, quantity
	, discount, price
	, notes
){
	core_orderin_orderindetail_row_id++;
	
	$('#core_orderin_orderindetail_list_table tbody').append(
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
				$("<input>", {type:'hidden', name:'c_orderindetails[' + core_orderin_orderindetail_row_id.toString() + '][id]', id:'core_orderin_form_detail_orderindetail_id_' + core_orderin_orderindetail_row_id.toString()})
					.val(id)
			).append(
				$("<input>", {type:'hidden', name:'c_orderindetails[' + core_orderin_orderindetail_row_id.toString() + '][m_product_id]', id:'core_orderin_form_detail_m_product_id_' + core_orderin_orderindetail_row_id.toString(), 'class':'required'})
					.val(m_product_id).attr('data-text', m_product_text)
			).append(
				$("<input>", {type:'hidden', name:'c_orderindetails[' + core_orderin_orderindetail_row_id.toString() + '][m_product_netto]', id:'core_orderin_form_detail_m_product_netto_' + core_orderin_orderindetail_row_id.toString()})
					.val(m_product_netto)
			).append(
				$("<input>", {type:'hidden', name:'c_orderindetails[' + core_orderin_orderindetail_row_id.toString() + '][m_product_pack]', id:'core_orderin_form_detail_m_product_pack_' + core_orderin_orderindetail_row_id.toString()})
					.val(m_product_pack)
			)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center'}).append(
				$("<input>", {type:'text', name:'c_orderindetails[' + core_orderin_orderindetail_row_id.toString() + '][quantity_box]', id:'core_orderin_form_detail_quantity_box_' + core_orderin_orderindetail_row_id.toString(), 'class':'required number'})
					.attr('data-index', core_orderin_orderindetail_row_id)
					.width(40).val(quantity_box).css('text-align', 'right')
					.change(function(){
						core_orderin_form_detail_calculate_quantity(jQuery(this).attr('data-index'));
						core_orderin_form_detail_calculate_amount(jQuery(this).attr('data-index'));
					})
			)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'right', id:'core_orderin_form_detail_m_product_netto_pack_' + core_orderin_orderindetail_row_id.toString()})
				.text(m_product_netto > 0 ? m_product_netto : m_product_pack)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center'}).append(
				$("<input>", {type:'text', name:'c_orderindetails[' + core_orderin_orderindetail_row_id.toString() + '][quantity]', id:'core_orderin_form_detail_quantity_' + core_orderin_orderindetail_row_id.toString(), 'class':'required number'})
					.attr('data-index', core_orderin_orderindetail_row_id)
					.width(40).val(quantity).css('text-align', 'right')
					.change(function(){
						core_orderin_form_detail_calculate_quantity(jQuery(this).attr('data-index'));
						core_orderin_form_detail_calculate_amount(jQuery(this).attr('data-index'));
					})
			)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center', id:'core_orderin_form_detail_m_product_uom_' + core_orderin_orderindetail_row_id.toString()})
				.text(m_product_uom)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center'}).append(
				$("<input>", {type:'text', name:'c_orderindetails[' + core_orderin_orderindetail_row_id.toString() + '][discount]', id:'core_orderin_form_detail_discount_' + core_orderin_orderindetail_row_id.toString(), 'class':'required number'})
					.attr('data-index', core_orderin_orderindetail_row_id)
					.width(30).val(discount).css('text-align', 'right')
					.change(function(){
						core_orderin_form_detail_calculate_amount(jQuery(this).attr('data-index'));
					})
			)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center'}).append(
				$("<input>", {type:'text', name:'c_orderindetails[' + core_orderin_orderindetail_row_id.toString() + '][price]', id:'core_orderin_form_detail_price_' + core_orderin_orderindetail_row_id.toString(), 'class':'required number'})
					.attr('data-index', core_orderin_orderindetail_row_id)
					.width(80).val(price).css('text-align', 'right')
					.change(function(){
						core_orderin_form_detail_calculate_amount(jQuery(this).attr('data-index'));
					})
			)
		).append(
			$("<td>", {'class':'ui-widget-content core_orderin_form_detail_amount', 'align':'right', id:'core_orderin_form_detail_amount_' + core_orderin_orderindetail_row_id.toString()})
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center'}).append(
				$("<input>", {type:'text', name:'c_orderindetails[' + core_orderin_orderindetail_row_id.toString() + '][notes]', id:'core_orderin_form_detail_notes_' + core_orderin_orderindetail_row_id.toString()})
					.width(200).val(notes)
			)
		)
	);
	
	core_orderin_form_detail_calculate_amount(core_orderin_orderindetail_row_id);
	core_orderin_form_product_select(core_orderin_orderindetail_row_id);
}

function core_orderin_form_product_select(idx){
	jquery_autocomplete_build('#core_orderin_form_detail_m_product_id_' + idx.toString(), "<?php echo site_url('core/orderin/get_product_autocomplete_list_json');?>", {
		width : 300
	}, {
		change: function(event, ui){
			if (ui.item == null)
			{
				$(this).val('');
				$('#core_orderin_form_detail_m_product_id_' + idx.toString()).val('');
				$('#core_orderin_form_detail_m_product_netto_' + idx.toString()).val(0);
				$('#core_orderin_form_detail_m_product_pack_' + idx.toString()).val(0);
				$('#core_orderin_form_detail_m_product_netto_pack_' + idx.toString()).text(0);
				$('#core_orderin_form_detail_m_product_uom_' + idx.toString()).text('');
				$('#core_orderin_form_detail_price_' + idx.toString()).val(0);
			}
			else
			{
				$('#core_orderin_form_detail_m_product_id_' + idx.toString()).val(ui.item.id);
				$('#core_orderin_form_detail_m_product_netto_' + idx.toString()).val(ui.item.netto);
				$('#core_orderin_form_detail_m_product_pack_' + idx.toString()).val(ui.item.pack);
				$('#core_orderin_form_detail_m_product_netto_pack_' + idx.toString()).text(ui.item.netto > 0 ? ui.item.netto : ui.item.pack);
				$('#core_orderin_form_detail_m_product_uom_' + idx.toString()).text(ui.item.uom);
				$('#core_orderin_form_detail_price_' + idx.toString()).val(ui.item.price);
			}
			core_orderin_form_detail_calculate_amount(idx);
		}
	});
}

function core_orderin_form_detail_calculate_quantity(idx){
	var netto = parseFloat($('#core_orderin_form_detail_m_product_netto_' + idx.toString()).val());
	var pack = parseFloat($('#core_orderin_form_detail_m_product_pack_' + idx.toString()).val());
	
	var quantity_box = parseFloat($('#core_orderin_form_detail_quantity_box_' + idx.toString()).val());
	if (quantity_box == 0) {
		$('#core_orderin_form_detail_quantity_box_' + idx.toString()).val(1);
	}
	
	if (netto > 0)
	{
		$('#core_orderin_form_detail_quantity_' + idx.toString()).val(netto * quantity_box);
	}
	else if (pack > 0)
	{
		$('#core_orderin_form_detail_quantity_' + idx.toString()).val(pack * quantity_box);
	}
}

function core_orderin_form_detail_calculate_amount(idx){
	var price = parseFloat($('#core_orderin_form_detail_price_' + idx.toString()).val());
	var discount = parseFloat($('#core_orderin_form_detail_discount_' + idx.toString()).val());
	var quantity = parseFloat($('#core_orderin_form_detail_quantity_' + idx.toString()).val());
	
	var amount = (quantity * price) - ((quantity * price * (discount / 100)));
	$('#core_orderin_form_detail_amount_' + idx.toString()).text(amount);
	
	var amount_total = 0;
	$('#core_orderin_orderindetail_list_table tbody .core_orderin_form_detail_amount').each(function(){
		amount_total += parseFloat($(this).text());
	});
	$('#core_orderin_orderindetail_list_table_amount_total').text(amount_total);
}

function core_orderin_form_submit(on_success){
	core_orderin_on_sucess = on_success;
	jQuery('#core_orderin_form').submit();
}
</script>