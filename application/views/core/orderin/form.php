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
		'class'	=> 'required',
		'style'	=> 'width:120px;',
		'value'	=> (!empty($record) ? $record->code : '')
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
						<th><label for="core_orderin_form_bol_no">Bill of Load</label></th>
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
			<th class="ui-state-default">Box</th>
			<th class="ui-state-default">Quantity</th>
			<th class="ui-state-default">Notes</th>
		</tr>
	</thead>
	<tbody></tbody>
	<tfoot>
		<tr>
			<td colspan="5" class="ui-state-default ui-corner-bl ui-corner-br">
				<button id="core_orderin_orderindetail_list_table_add" class="table-data-button">Add</button>
			</td>
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
			null, null, null, 0, 1, 1, null
		);
		
		jQuery('#core_orderin_form_detail_m_product_id_' + core_orderin_orderindetail_row_id.toString() + '_caption').focus();
	});
	
	jQuery.each(<?php echo !empty($c_orderindetails) ? json_encode($c_orderindetails) : '[]';?>, function(orderindetail_idx, orderindetail){
		core_orderin_form_orderindetail_add(
			orderindetail.id, orderindetail.m_product_id, orderindetail.m_product_text, orderindetail.m_product_netto, orderindetail.quantity_box, orderindetail.quantity, orderindetail.notes
		);
	});
	
<?php
if (empty($c_orderindetails))
{?>
	core_orderin_form_orderindetail_add(
		null, null, null, 0, 1, 1, null
	);
<?php
}?>
});

function core_orderin_form_orderindetail_add(
	id, m_product_id, m_product_text, m_product_netto, quantity_box, quantity, notes
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
			)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center'}).append(
				$("<input>", {type:'text', name:'c_orderindetails[' + core_orderin_orderindetail_row_id.toString() + '][quantity_box]', id:'core_orderin_form_detail_quantity_box_' + core_orderin_orderindetail_row_id.toString(), 'class':'required number'})
					.attr('data-index', core_orderin_orderindetail_row_id)
					.width(40).val(quantity_box).css('text-align', 'right')
					.change(function(){
						core_orderin_form_quantity_calculate(jQuery(this).attr('data-index'));
					})
			)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center'}).append(
				$("<input>", {type:'text', name:'c_orderindetails[' + core_orderin_orderindetail_row_id.toString() + '][quantity]', id:'core_orderin_form_detail_quantity_' + core_orderin_orderindetail_row_id.toString(), 'class':'required number'})
					.width(40).val(quantity).css('text-align', 'right')
			)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center'}).append(
				$("<input>", {type:'text', name:'c_orderindetails[' + core_orderin_orderindetail_row_id.toString() + '][notes]', id:'core_orderin_form_detail_notes_' + core_orderin_orderindetail_row_id.toString()})
					.width(200).val(notes)
			)
		)
	);
	
	core_orderin_form_product_select(core_orderin_orderindetail_row_id);
}

function core_orderin_form_product_select(idx){
	jquery_autocomplete_build('#core_orderin_form_detail_m_product_id_' + idx.toString(), "<?php echo site_url('core/orderin/get_product_autocomplete_list_json');?>", {
		width : 350
	}, {
		change: function(event, ui){
			if (ui.item == null)
			{
				$(this).val('');
				$('#core_orderin_form_detail_m_product_id_' + idx.toString()).val('');
				$('#core_orderin_form_detail_m_product_netto_' + idx.toString()).val(0);
			}
			else
			{
				$('#core_orderin_form_detail_m_product_id_' + idx.toString()).val(ui.item.id);
				$('#core_orderin_form_detail_m_product_netto_' + idx.toString()).val(ui.item.netto);
			}
			core_orderin_form_quantity_calculate(idx);
		}
	});
}

function core_orderin_form_quantity_calculate(idx){
	var netto = parseFloat($('#core_orderin_form_detail_m_product_netto_' + idx.toString()).val());
	if (netto > 0)
	{
		var quantity_box = parseFloat($('#core_orderin_form_detail_quantity_box_' + idx.toString()).val());
		$('#core_orderin_form_detail_quantity_' + idx.toString()).val(netto * quantity_box);
	}
}

function core_orderin_form_submit(on_success){
	core_orderin_on_sucess = on_success;
	jQuery('#core_orderin_form').submit();
}
</script>