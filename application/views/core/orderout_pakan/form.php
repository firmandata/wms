<?php 
$orderout_origins = array_merge(array('' => ''), $this->config->item('orderout_origins'));

echo form_open($form_action.(!empty($record) ? '/'.$record->id : ''),
	array(
		'name'	=> 'core_orderout_pakan_form',
		'id'	=> 'core_orderout_pakan_form'
	)
);?>
<table>
	<tr><td valign="top">
			<table class="form-table">
				<tbody>
					<tr>
						<th width="120"><label for="core_orderout_pakan_form_code">No</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'code',
		'id' 	=> 'core_orderout_pakan_form_code',
		'class'	=> (!empty($record) ? 'required' : ''),
		'style'	=> 'width:120px;',
		'value'	=> (!empty($record) ? $record->code : ''),
		'placeholder'	=> '## Auto ##'
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="core_orderout_pakan_form_c_businesspartner_id_caption">Business Partner</label></th>
						<td><input type="hidden" name="c_businesspartner_id" id="core_orderout_pakan_form_c_businesspartner_id" class="required" value="<?php echo (!empty($record) ? $record->c_businesspartner_id : '');?>" data-text="<?php echo (!empty($record) ? $record->c_businesspartner_text : '');?>"/></td>
					</tr>
					<tr>
						<th><label for="core_orderout_pakan_form_orderout_date">Date</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'orderout_date',
		'id' 	=> 'core_orderout_pakan_form_orderout_date',
		'class'	=> 'required date',
		'style'	=> 'width:75px;',
		'value'	=> (!empty($record->orderout_date) ? date($this->config->item('server_display_date_format'), strtotime($record->orderout_date)) : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="core_orderout_pakan_form_notes">Notes</label></th>
						<td>
<?php 
echo form_textarea(
	array(
		'name' 	=> 'notes',
		'id' 	=> 'core_orderout_pakan_form_notes',
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
				<tbody>
					<tr>
						<th width="120"><label for="core_orderout_pakan_form_c_project_caption">Project</label></th>
						<td><input type="hidden" name="c_project_id" id="core_orderout_pakan_form_c_project_id" class="required" value="<?php echo (!empty($record) ? $record->c_project_id : '');?>" data-text="<?php echo (!empty($record) ? $record->c_project_text : '');?>"/></td>
					</tr>
					<tr>
						<th><label for="core_orderout_pakan_form_external_no">External No</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'external_no',
		'id' 	=> 'core_orderout_pakan_form_external_no',
		'value'	=> (!empty($record) ? $record->external_no : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="core_orderout_pakan_form_approval_by">Approval By</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'approval_by',
		'id' 	=> 'core_orderout_pakan_form_approval_by',
		'value'	=> (!empty($record) ? $record->approval_by : '')
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
<table id="core_orderout_pakan_orderout_pakandetail_list_table" class="table-data ui-widget ui-widget-content ui-corner-all" width="100%" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<th class="ui-state-default ui-corner-tl" width="22px">&nbsp;</th>
			<th class="ui-state-default">Product</th>
			<th class="ui-state-default">Type</th>
			<th class="ui-state-default">Casing</th>
			<th class="ui-state-default">Quantity</th>
			<th class="ui-state-default">UOM</th>
			<th class="ui-state-default">Notes</th>
		</tr>
	</thead>
	<tbody></tbody>
	<tfoot>
		<tr>
			<td colspan="7" class="ui-state-default ui-corner-bl ui-corner-br">
				<button id="core_orderout_pakan_orderout_pakandetail_list_table_add" class="table-data-button">Add</button>
			</td>
		</tr>
	</tfoot>
</table>
<?php echo form_close();?>

<script type="text/javascript">
var core_orderout_pakan_on_sucess;
var core_orderout_pakan_orderout_pakandetail_row_id = 0;

jQuery(function(){
	jquery_ready_load();
	
	jQuery("#core_orderout_pakan_form").validate({
		submitHandler: function(form){
			jQuery("#core_orderout_pakan_form").ajaxSubmit({
				dataType: "json",
				async : false,
				error: jquery_ajax_error_handler,
				beforeSend: function(jqXHR, settings){
					jquery_blockui();
				},
				success: function(data, textStatus, jqXHR){
					core_orderout_pakan_on_sucess(data, textStatus, jqXHR);
				},
				complete: function(jqXHR, textStatus){
					jquery_unblockui();
				}
			});
		}
	});
	
	jquery_autocomplete_build("#core_orderout_pakan_form_c_businesspartner_id", "<?php echo site_url('core/orderout_pakan/get_businesspartner_autocomplete_list_json');?>", {
		width : 250
	});
	
	jquery_autocomplete_build("#core_orderout_pakan_form_c_project_id", "<?php echo site_url('core/orderout_pakan/get_project_autocomplete_list_json');?>", {
		width : 200
	});
	
	jQuery('#core_orderout_pakan_orderout_pakandetail_list_table_add').button({
		text: false,
		icons: {
			primary: "ui-icon-plus"
		}
	})
	.click(function(e){
		e.preventDefault();
		core_orderout_pakan_form_orderout_pakandetail_add(
			null,
			null, null, 0, 0, null, null, null,
			1,
			null
		);
		
		jQuery('#core_orderout_pakan_form_detail_m_product_id_' + core_orderout_pakan_orderout_pakandetail_row_id.toString() + '_caption').focus();
	});
	
	jQuery.each(<?php echo !empty($c_orderoutdetails) ? json_encode($c_orderoutdetails) : '[]';?>, function(orderout_pakandetail_idx, orderout_pakandetail){
		core_orderout_pakan_form_orderout_pakandetail_add(
			orderout_pakandetail.id,
			orderout_pakandetail.m_product_id, orderout_pakandetail.m_product_text, orderout_pakandetail.m_product_netto, orderout_pakandetail.m_product_pack, orderout_pakandetail.m_product_type, orderout_pakandetail.m_product_casing, orderout_pakandetail.m_product_uom,
			orderout_pakandetail.quantity,
			orderout_pakandetail.notes
		);
	});
	
<?php
if (empty($c_orderoutdetails))
{?>
	core_orderout_pakan_form_orderout_pakandetail_add(
		null,
		null, null, 0, 0, null, null, null,
		1,
		null
	);
<?php
}?>
});

function core_orderout_pakan_form_orderout_pakandetail_add(
	id,
	m_product_id, m_product_text, m_product_netto, m_product_pack, m_product_type, m_product_casing, m_product_uom,
	quantity,
	notes
){
	core_orderout_pakan_orderout_pakandetail_row_id++;
	
	$('#core_orderout_pakan_orderout_pakandetail_list_table tbody').append(
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
				$("<input>", {type:'hidden', name:'c_orderout_pakandetails[' + core_orderout_pakan_orderout_pakandetail_row_id.toString() + '][id]', id:'core_orderout_pakan_form_detail_orderout_pakandetail_id_' + core_orderout_pakan_orderout_pakandetail_row_id.toString()})
					.val(id)
			).append(
				$("<input>", {type:'hidden', name:'c_orderout_pakandetails[' + core_orderout_pakan_orderout_pakandetail_row_id.toString() + '][m_product_id]', id:'core_orderout_pakan_form_detail_m_product_id_' + core_orderout_pakan_orderout_pakandetail_row_id.toString(), 'class':'required'})
					.val(m_product_id).attr('data-text', m_product_text)
			).append(
				$("<input>", {type:'hidden', name:'c_orderout_pakandetails[' + core_orderout_pakan_orderout_pakandetail_row_id.toString() + '][m_product_netto]', id:'core_orderout_pakan_form_detail_m_product_netto_' + core_orderout_pakan_orderout_pakandetail_row_id.toString()})
					.val(m_product_netto)
			).append(
				$("<input>", {type:'hidden', name:'c_orderout_pakandetails[' + core_orderout_pakan_orderout_pakandetail_row_id.toString() + '][m_product_pack]', id:'core_orderout_pakan_form_detail_m_product_pack_' + core_orderout_pakan_orderout_pakandetail_row_id.toString()})
					.val(m_product_pack)
			)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center', id:'core_orderout_pakan_form_detail_m_product_type_' + core_orderout_pakan_orderout_pakandetail_row_id.toString()})
				.text(m_product_type)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center', id:'core_orderout_pakan_form_detail_m_product_casing_' + core_orderout_pakan_orderout_pakandetail_row_id.toString()})
				.text(m_product_casing)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center'}).append(
				$("<input>", {type:'text', name:'c_orderout_pakandetails[' + core_orderout_pakan_orderout_pakandetail_row_id.toString() + '][quantity]', id:'core_orderout_pakan_form_detail_quantity_' + core_orderout_pakan_orderout_pakandetail_row_id.toString(), 'class':'required number'})
					.width(40).val(quantity).css('text-align', 'right')
			)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center', id:'core_orderout_pakan_form_detail_m_product_uom_' + core_orderout_pakan_orderout_pakandetail_row_id.toString()})
				.text(m_product_uom)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center'}).append(
				$("<input>", {type:'text', name:'c_orderout_pakandetails[' + core_orderout_pakan_orderout_pakandetail_row_id.toString() + '][notes]', id:'core_orderout_pakan_form_detail_notes_' + core_orderout_pakan_orderout_pakandetail_row_id.toString()})
					.width(200).val(notes)
			)
		)
	);
	
	core_orderout_pakan_form_select(core_orderout_pakan_orderout_pakandetail_row_id);
}

function core_orderout_pakan_form_select(idx){
	jquery_autocomplete_build('#core_orderout_pakan_form_detail_m_product_id_' + idx.toString(), "<?php echo site_url('core/orderout_pakan/get_product_autocomplete_list_json');?>", {
		width : 350
	}, {
		change: function(event, ui){
			if (ui.item == null)
			{
				$(this).val('');
				$('#core_orderout_pakan_form_detail_m_product_id_' + idx.toString()).val('');
				$('#core_orderout_pakan_form_detail_m_product_netto_' + idx.toString()).val(0);
				$('#core_orderout_pakan_form_detail_m_product_pack_' + idx.toString()).val(0);
				$('#core_orderout_pakan_form_detail_m_product_casing_' + idx.toString()).text('');
				$('#core_orderout_pakan_form_detail_m_product_uom_' + idx.toString()).text('');
				$('#core_orderout_pakan_form_detail_m_product_type_' + idx.toString()).text('');
			}
			else
			{
				$('#core_orderout_pakan_form_detail_m_product_id_' + idx.toString()).val(ui.item.id);
				$('#core_orderout_pakan_form_detail_m_product_netto_' + idx.toString()).val(ui.item.netto);
				$('#core_orderout_pakan_form_detail_m_product_pack_' + idx.toString()).val(ui.item.pack);
				$('#core_orderout_pakan_form_detail_m_product_casing_' + idx.toString()).text(ui.item.casing);
				$('#core_orderout_pakan_form_detail_m_product_uom_' + idx.toString()).text(ui.item.uom);
				$('#core_orderout_pakan_form_detail_m_product_type_' + idx.toString()).text(ui.item.type);
			}
		}
	});
}

function core_orderout_pakan_form_submit(on_success){
	core_orderout_pakan_on_sucess = on_success;
	jQuery('#core_orderout_pakan_form').submit();
}
</script>